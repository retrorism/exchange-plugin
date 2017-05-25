<?php
/**
 * Base Map Class File
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 11/05/2016
 *
 * @package Exchange Plugin
 **/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
};

/**
 * Base Map Class
 *
 * This class serves as the basis for all Map views, to be used for
 * Overview pages and possibly collaboration pages.
 *
 * @since 0.1.0
 **/
abstract class BaseMap extends BasePattern {

	/**
	 * Hash the input to create a unique ID.
	 *
	 * @var string $map_hash 
	 **/
	protected $map_hash;

	/**
	 * Map Markers variable
	 *
	 * @var array $map_markers Items array containing all Mappable objects
	 */
	protected $map_markers;

	/**
	 * Map Markers variable
	 *
	 * @var array $map_markers Items array containing all Mappable objects
	 */
	protected $map_marker_shortcodes;

	/**
	 * Collaboration Data variable
	 *
	 * @var array $collaboration_data Items array containing all Mappable objects
	 */
	protected $map_polylines;

	/**
	 * Collaboration Data variable
	 *
	 * @var array $collaboration_shortcodes;
	 */
	protected $map_polyline_shortcodes;

	/**
	 * Map Markers Check
	 *
	 * @var boolean $has_markers
	 */
	protected $has_markers = false;

	/**
	 * Map Network Check
	 *
	 * @var boolean $has_markers
	 */
	protected $has_network = false;

	/**
	 * Map Caption Check
	 *
	 * @var boolean $has_caption
	 */
	protected $has_caption = false;

	/**
	 * Map Caption Check
	 *
	 * @var boolean $has_caption
	 */
	protected $use_shortcodes_for_objects = false;

	/**
	 * Map Caption
	 *
	 * @var object $caption (if available)
	 */
	protected $caption;

	/**
	 * Overwrite initial output value for Grid blocks
	 *
	 * @since 0.1.0
	 * @access protected
	 *
	 * @TODO create single addition script.
	 **/
	 protected function create_output() {

		// If a grid is created inside a story, make this into an figure class.
		$el = 'div';
		if ( is_single() || is_page() || is_archive() ) {
			$el = 'figure';
		}

		if ( class_exists( 'Leaflet_Map_Plugin_Extension' ) ) {
			$map_shortcode = $this->create_map_shortcode();

			if ( 'dots' === $this->input['map_style'] ) {
				$this->set_participant_markers();
			} elseif ( 'network' === $this->input['map_style'] ) {
				$this->set_map_collaborations();
			}
			$this->set_caption();

			// Random string for via: http://stackoverflow.com/questions/4356289/php-random-string-generator#comment35061829_4356295
			$this->map_hash = substr(str_shuffle(MD5(microtime())), 0, 10);

			$this->set_attribute('data','map-hash', $this->map_hash );

			$this->output_tag_open( $el );

			$this->output .= apply_filters('the_content', $map_shortcode);

			if ( $this->use_shortcodes_for_objects ) {
				$this->embed_object_shortcodes();
			} else {
				$this->pass_json_as_var();
				$auto_draw = count( $this->map_polylines ) === 1 ? ' auto_draw=1' : '';
				$layers_shortcode = '[leaflet-layers map_hash="' . $this->map_hash . '"' . $auto_draw . ']';
				$this->output .= apply_filters( 'the_content', $layers_shortcode );
			}

			// Add caption.
			if ( $this->has_caption ) {
				$this->output .= $this->caption->embed();
			}

			// Close map element.
			$this->output_tag_close( $el );


		} else {
			$this->output_tag_open( $el );
			$this->output .= '<span>Normally, we would see a map here!</span>';
			$this->output_tag_close( $el );
		}
	}

	/**
	 * Set map markers.
	 *
	 * Sets input array to map_markers property.
	 */
	protected function set_map_markers_1() {
		$markers = $this->input['map_markers'];
		if ( empty( $markers ) || count( $markers ) > 0 )  {
			return;
		} else {
			foreach( $markers as $marker ) {
				$this->verify_and_add_marker_data( $marker );
			}
			if ( ! empty( $this->map_markers > 0 ) ) {
				$this->has_markers = true;
			}
		}
	}

	/**
	 * Set map markers.
	 *
	 * Sets input array to map_markers property.
	 */
	protected function set_participant_markers() {
		$objects = $this->input['map_markers'];
		$p_loc_transient = get_transient( 'participant_locations' );
		if ( empty( $objects ) || count( $objects ) == 0 ) {
			if ( $p_loc_transient ) {
				foreach( $p_loc_transient as $p_loc_id => $p_loc ) {
					$p_loc['id'] = $p_loc_id;
					$this->map_markers[] = $p_loc;
				}
			}
		} else {
			$participants = array();
			foreach ( $objects as $object ) {
				if ( is_numeric( $object ) && $object > 0 ) {
					$object = get_post( $object );
				}
				switch ( $object->post_type ) {
					case 'participant' :
						$participants[] = $object;
						break;
					default :
						break;
				}
			}
			$participants = array_unique( $participants, SORT_REGULAR );
			$participant_total = count( $participants );

			if ( $p_loc_transient ) {
				for ( $i = 0; $i < $participant_total; $i++ ) {
					if ( ! empty( $p_loc_transient[ $participants[$i]->ID ] ) ) {
						$this->map_markers[] = $p_loc_transient[ $participants[$i]->ID ];
					} else {
						$this->set_participant_data( $participants[$i] );
					}
				}
			} else {
				for ( $i = 0; $i < $participant_total; $i++ ) {
					$this->set_participant_data( $participants[ $i ] );
				}
			}
		}
		if ( ! empty( $this->map_markers ) ) {
			$this->has_markers = true;
		}
	}

	/**
	 * Set map markers.
	 *
	 * Sets input array to map_markers property.
	 */
	protected function set_map_collaborations( ) {
		$objects = $this->input['map_collaborations'];
		$col_loc_transient = get_transient( 'collaboration_locations' );
		if ( empty( $objects ) || count( $objects ) == 0 ) {
			if ( $col_loc_transient ) {
				foreach( $col_loc_transient as $col_loc_id => $col_loc ) {
					$col_loc['id'] = $col_loc_id;
					$this->map_polylines[] = $col_loc;
				}
			}
		} else {
			$collaborations = array();
			foreach ( $objects as $object ) {
				if ( is_numeric( $object ) && $object > 0 ) {
					$object = get_post( $object );
				}
				switch ( $object->post_type ) {
					case 'programme_round':
						$programme_round = new Programme_round( $object );
						if ( ! $programme_round instanceof Programme_Round ) {
							continue;
						}
						$programme_round_collabs = $programme_round->controller->get_collaborations();
						if ( count( $programme_round_collabs ) ) {
							$collaborations = array_merge( $collaborations, $programme_round_collabs );
						}
						break;
					case 'collaboration' :
						$collaborations[] = $object;
						break;
					default :
						break;
				}
			}
			$collaborations = array_unique( $collaborations, SORT_REGULAR );
			$collab_total = count( $collaborations );

			// Limit to 20 collaborations.
			//for ( $i = 0; $i < $collab_total && $i < $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['PATTERNS']['map_max-collaboration-count']; $i++ ) {
			if ( $col_loc_transient ) {
				for ( $i = 0; $i < $collab_total; $i++ ) {
					if ( ! empty( $col_loc_transient[ $collaborations[$i]->ID ] ) ) {
						$this->map_polylines[] = $col_loc_transient[ $collaborations[$i]->ID ];
					} else {
						$this->set_collaboration_data( $collaborations[$i] );
					}
				}
			} else {
				for ( $i = 0; $i < $collab_total; $i++ ) {
					$this->set_collaboration_data( $collaborations[ $i ] );
				}
			}
		}
		if ( ! empty( $this->map_polylines ) ) {
			$this->has_network = true;
		}
	}

		/**
	 * Set map markers.
	 *
	 * Sets input array to map_markers property.
	 */
	protected function set_map_participants( ) {
		$objects = $this->input['map_participants'];
		$p_loc_transient = get_transient( 'participant_locations' );
		if ( empty( $objects ) || count( $objects ) == 0 ) {
			if ( $p_loc_transient ) {
				foreach( $p_loc_transient as $p_loc_id => $p_loc ) {
					$p_loc['id'] = $p_loc_id;
					$this->map_markers[] = $p_loc;
				}
			}
		} else {
			$participants = array();
			foreach ( $objects as $object ) {
				if ( is_numeric( $object ) && $object > 0 ) {
					$object = get_post( $object );
				}
				switch ( $object->post_type ) {
					case 'participant' :
						$participants[] = $object;
						break;
					default :
						break;
				}
			}
			$participants = array_unique( $participants, SORT_REGULAR );
			$participant_total = count( $participants );

			if ( $p_loc_transient ) {
				for ( $i = 0; $i < $participant_total; $i++ ) {
					if ( ! empty( $p_loc_transient[ $participants[$i]->ID ] ) ) {
						$this->map_markers[] = $p_loc_transient[ $participants[$i]->ID ];
					} else {
						$this->set_collaboration_data( $collaborations[$i] );
					}
				}
			} else {
				for ( $i = 0; $i < $collab_total; $i++ ) {
					$this->set_collaboration_data( $collaborations[ $i ] );
				}
			}
		}
		if ( ! empty( $this->map_polylines ) ) {
			$this->has_network = true;
		}
	}

	/**
	 * Retrieve the right map size from the users selection
	 *
	 * @param string $key ACF input
	 * @return array $sizes with one pair of sizes.
	 */
	protected function get_map_size() {
		$sizes = array(
			'full' => array( '100%', '700px'),
			'wide' => array( '100%', '460px' ),
			'square' => array( '100%', '575px' ),
			'small' => array( '100%', '460px' ),
		);
		$key = $this->input['map_size'];
		return $sizes[$key];
	}

	/**
	 * Set participant data
	 *
	 * This function creates a marker
	 *
	 * @param object $collaboration Post object
	 * @return void
	 */
	 protected function set_participant_data( $participant ) {
		$p_object = BaseController::exchange_factory( $participant );
		if ( ! $p_object instanceof Participant ) {
			return;
		}
		if ( ! $p_object->has_locations ) {
			return;
		}
		// Create label
		if ( $this->use_shortcodes_for_objects ) {
			$line_label = addslashes( exchange_create_link( $c_object ) );
			// Feed the collaboration geodata into a shortcode
			$marker = array(
				'lat'     => $p_object->locations[0]['lat'],
				'lng'     => $p_object->locations[0]['lng'],
				'message' => $line_label,
			);
			$this->map_marker_shortcodes[] = $this->create_map_marker_shortcode( $marker );
		} else {
			$this->map_markers[] = $this->create_map_marker_data( $p_object );
		}
	}

	/**
	 * Set collaboration data
	 *
	 * This function creates an array with two line coordinates
	 *
	 * @param object $collaboration Post object
	 * @return void
	 */
	 protected function set_collaboration_data( $collaboration ) {
		$c_object = BaseController::exchange_factory( $collaboration, 'simplemap' );
		if ( ! $c_object instanceof Collaboration ) {
			return;
		}
		if ( ! $c_object->has_locations ) {
			return;
		}
		// Create label
		if ( $this->use_shortcodes_for_objects ) {
			$line_label = addslashes( exchange_create_link( $c_object ) );
			// Feed the collaboration geodata into a shortcode
			$this->map_polyline_shortcodes[] = $this->create_map_polyline_shortcode( $c_object->locations, $line_label );
		} else {
			$this->map_polylines[] = $this->create_map_polyline_data( $c_object );
		}
	}


	/** Verify and add marker data.
	 *
	 * If not empty, sets input array to map_markers property.
	 *
	 */
	protected function verify_and_add_marker_data( $marker ) {
		$marker_title = $marker['map_marker_title'];
		$marker_location = $marker['map_marker_location'];
		$marker_linked_object = $marker['map_marker_link'];
		if ( empty( $marker_title ) ) {
			return;
		} else {
			// @TODO Possibly add another pattern just for the label.
			$marker_label = array(
				'title' => $marker_title,
			);
			$marker_label['linked_object'] = is_object( $marker_linked_object ) ? $marker_linked_object : null;
		}
		// See if both long and lat are set
		if ( is_array( $marker_location ) && array_key_exists( 'lat', $marker_location ) && array_key_exists( 'lng', $marker_location ) ) {
			if ( is_string( $marker_location['lat'] ) && is_string( $marker_location['lng'] ) ) {
				$verified_marker = $this->prepare_map_marker( $marker_location, $marker_label );
				$this->map_markers[] = $verified_marker;
				if ( $this->use_shortcodes_for_objects ) {
					$this->map_marker_shortcodes[] = $this->create_map_marker_shortcode( $verified_marker );
				}
			}
		}
	}

	protected function create_map_shortcode() {
		$sizes = $this->get_map_size();
		$map_shortcode = '[leaflet-map zoomcontrol=1 ';
		$map_shortcode .= 'height=' . $sizes[1] ;
		// if ( 'network' !== $this->input['map_style'] ) {
		// 	$map_shortcode .= ' zoom=' . $this->input['map_zoom_level'] . ' ';
		// 	$map_shortcode .= 'lat=' . $this->input['map_center']['lat'] . ' ';
		// 	$map_shortcode .= 'lng=' . $this->input['map_center']['lng'];
		// }
		// if ( 'full' === $this->input['map_size'] ) {
		// 	$map_shortcode .= ' zoomcontrol=0'; 
		// }
		$map_shortcode .= ']';
		return $map_shortcode;
	}

	/**
	 * Create map marker shortcode
	 *
	 * If not empty, sets input array to map_markers property.
	 *
	 * @param array $marker Array with marker data.
	 */
	protected function create_map_marker_shortcode( $marker ) {
		$marker_shortcode = '[leaflet-marker ';
		if ( ! empty( $marker['options'] ) ) {
			foreach( $marker_options as $option => $value ) {
				if ( ! empty( $value ) ) {
					if ( is_array( $value ) ) {
						$value = implode( ',', $value );
					}
					$marker_shortcode .= $option . '=' . '"' . $value .'" ';
				}
			}
		}
		$marker_shortcode .= 'lat=' . $marker['lat'] . ' ';
		$marker_shortcode .= 'lng=' . $marker['lng'] . ']';

		if ( isset( $marker['message'] ) ) {
			$marker_shortcode .= $marker['message'] . '[/leaflet-marker]';
		}
		return $marker_shortcode;
	}

	/**
	 * Create map line.
	 *
	 * @param array $locations Array with lats, longs and cities for each participant's organisation.
	 * @param string $line_label defaults to ''.
	 * @return string $line Map line shortcode with either addresses or coordinates.
	 */
	protected function create_map_polyline_shortcode( $locations, $line_label = '' ) {
		$line = '[leaflet-polyline fitbounds=1 ';
		$line .= 'color="#' . $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['COLOURS']['yellow-tandem'] . '" ';
		$no_of_locations = count( $locations );
		$latlngs = array();
		$cities = array();
		$addresses = array();

		foreach ( $locations as $location ) {
			if ( ! empty( $location['org_lat'] ) && ! empty( $location['org_lng'] ) ) {
				$latlngs[] = $location['org_lat'] . ', '. $location['org_lng'];
			}
			if ( ! empty( $location['org_city'] ) ) {
				$cities[] = $location['org_city'];
			} else {
				$cities[] = '';
			}
			if ( ! empty( $location['org_address'] ) ) {
				$addresses[] = $location['org_address'];
			} elseif ( ! empty( $location['org_city'] ) ) {
				$addresses[] = $location['org_city'];
			}
		}
		if ( count( $latlngs ) > 1 ) {
			$line .= 'latlngs="' . implode( '; ', $latlngs ) . ';"';
		} elseif ( count( $addresses ) == $no_of_locations ) {
			$line .= 'addresses="' . implode( '; ', $addresses ) . ';"';
		} else {
			return false;
		}
		if ( count( $cities ) == $no_of_locations ) {
			$line .= ' cities="' . implode( '; ', $cities ) . ';"';
		}
		$line .=']';
		if ( ! empty( $line_label ) ) {
			$line .= $line_label . '[/leaflet-polyline]';
		}
		return $line;
	}

	protected function create_map_marker_data( $obj ) {
		$response = array(
			'title' => $obj->locations->title,
			'link' => $obj->locations->link,
		);
		$response_locations = array();
		// Set locations
		foreach ( $obj->locations as $p => $location ) {
			$lat = floatval( $location['org_lat'] );
			$lng = floatval( $location['org_lng'] );
			if ( ! empty( $lat ) && ! empty( $lng ) ) {
				$response_locations[ $p ]['latlngs'] = array( $lat, $lng );
			} elseif ( ! empty( $location['org_city'] ) || ! empty( $location['org_address'] ) ) {
				$geocoded_latlngs = $this->get_location_coords( $location );
				if ( ! empty( $geocoded_latlngs ) ) {
					$response_locations[ $p ]['latlngs'] = $geocoded_latlngs;
				}
			}
			if ( $location['name'] ) {
				$response_locations[ $p ]['name'] = $location['name'];
			}
			if ( $location['exchange_id'] ) {
				$response_locations[ $p ]['exchange_id'] = $location['exchange_id'];
			}
			if ( $location['org_city'] ) {
				$response_locations[ $p ]['city'] = $location['org_city'];
			}
			if ( $location['org_name'] ) {
				$response_locations[ $p ]['org_name'] = $location['org_name'];
			}
			if ( $location['country'] ) {
				$response_locations[ $p ]['country'] = $location['country'];
			}
		}
		// There should be only one, otherwise, take the first.
		$response['location'] = $response_locations[0];
		if ( $obj->has_featured_image && ! empty( $obj->featured_image->input['sizes'] ) ) {
			$response['image'] = $obj->featured_image->input['sizes']['thumbnail'];
		}
		return $response;
	}


	protected function create_map_polyline_data( $collab ) {
		$response = array(
			'title' => $collab->locations->title,
			'link' => $collab->locations->link,
		);
		$response_locations = array();
		// Set locations
		foreach ( $collab->locations as $participant => $location ) {
			$lat = floatval( $location['org_lat'] );
			$lng = floatval( $location['org_lng'] );
			if ( ! empty( $lat ) && ! empty( $lng ) ) {
				$response_locations[ $participant ]['latlngs'] = array( $lat, $lng );
			} elseif ( ! empty( $location['org_city'] ) || ! empty( $location['org_address'] ) ) {
				$geocoded_latlngs = $this->get_location_coords( $location );
				if ( ! empty( $geocoded_latlngs ) ) {
					$response_locations[ $participant ]['latlngs'] = $geocoded_latlngs;
				}
			}
			if ( $location['name'] ) {
				$response_locations[ $participant ]['name'] = $location['name'];
			}
			if ( $location['exchange_id'] ) {
				$response_locations[ $participant ]['exchange_id'] = $location['exchange_id'];
			}
			if ( $location['org_city'] ) {
				$response_locations[ $participant ]['city'] = $location['org_city'];
			}
			if ( $location['org_name'] ) {
				$response_locations[ $participant ]['org_name'] = $location['org_name'];
			}
		}
		$response['locations'] = $response_locations;
		if ( $collab->has_featured_image && ! empty( $collab->featured_image->input['sizes'] ) ) {
			$response['image'] = $collab->featured_image->input['sizes']['thumbnail'];
		}
		return $response;
	}

	protected function create_map_polyline_data_1( $collab, $line_label = '' ) {
		$no_of_locations = count( $locations );
		$latlngs = array();
		$cities = array();
		$addresses = array();

		foreach ( $collab->locations as $location ) {
			$lat = floatval( $location['org_lat'] );
			$lng = floatval( $location['org_lng'] );
			if ( ! empty( $lat ) && ! empty( $lng ) ) {
				$latlngs[] = array( $lat, $lng );
			} elseif ( ! empty( $location['org_city'] ) || ! empty( $location['org_address'] ) ) {
				$geocoded_latlngs = $this->get_location_coords( $location );
				if ( ! empty( $geocoded_latlngs ) ) {
					$latlngs[] = $geocoded_latlngs;
				}
			}
			if ( ! empty( $location['org_city'] ) ) {
				$cities[] = $location['org_city'];
			} else {
				$cities[] = '';
			}
		}
		if ( count( $latlngs ) !== $no_of_locations ) {
			return false;
		}
		$response = array(
			'line_label' => $line_label,
			'latlngs' => $latlngs,
		);
		if ( count( $cities ) == $no_of_locations ) {
			$response['cities'] = $cities;
		}
		return $response;
	}

	protected function get_location_coords( $location ) {
		if ( ! class_exists( 'Leaflet_Map_Plugin' ) || ! count( $location ) ) {
			return;
		}
		if ( ! empty( $location['org_address'] ) ) {
			$geocoded = Leaflet_Map_Plugin::geocoder( $address );
            $coords = array( $geocoded->{'lat'}, $geocoded->{'lng'} );
		} elseif ( ! empty( $location['org_city'] ) ) {
			$coords = array( $geocoded->{'lat'}, $geocoded->{'lng'} );
		}
		if ( $coords ) {
			return $coords;
		}
	}

	/**
	 * Prepare map marker.
	 * @param array $marker_location Array with lat and long
	 * @return string array $marker_label Array with title and possibly page ID as link.
	 */
	protected function prepare_map_marker( $marker_location, $marker_label ) {
		$marker = array(
			'message' => $marker_label['title'],
			'lat'   => $marker_location['lat'],
			'lng'  => $marker_location['lng'],
			'options' => $this->get_marker_icon_options(),
		);
		if ( ! empty( $marker_label['linked_object'] ) ) {
			// Create link from object
			$object = BaseController::exchange_factory( $marker_label['linked_object'] );
			$link = exchange_create_link( $object, false );
		}
		if ( isset( $link ) ) {
			$marker['message'] = $link . $marker_label['title'] . '</a>';
		}
		return $marker;
	}

	/**
	 * Get marker options available in plugin, 
	 *
	 * @return void
	 * @author 
	 **/
	protected function get_marker_icon_options() {
		$leaflet_options = array(
			'iconUrl' => NULL,
			'iconSize' => NULL,
			'iconAnchor' => NULL
			);
		$stored_options = get_option( 'leaflet_map_plugin_extension_options' );
		if ( ! empty( $stored_options ) ) {
			foreach( $stored_options as $option => $val ) {
				if ( array_key_exists( $option, $leaflet_options ) && ! empty( $val ) ) {
					$leaflet_options[ $option ] = $val;
				}
			}
		}
		return $leaflet_options;
	}

	/**
	 * Set caption object
	 *
	 * @access protected
	 */
	protected function set_caption() {
		$caption = $this->input['map_caption'];
		if ( ! empty( $caption ) ) {
			$this->caption = new Caption( $caption, $this->element );
			$this->has_caption = true;
		}
	}

	/**
	 * Embed object shortcodes
	 *
	 * @return void
	 * @author Willem Prins | Somtijds
	 **/
	protected function embed_object_shortcodes() {
	// Add individual marker script elements.
		if ( $this->has_markers ) {
			$markers = $this->map_marker_shortcodes;
			foreach ( $markers as $marker ) {
				$this->output .= apply_filters('the_content', $marker );
			}
		} elseif ( $this->has_network ) {
			$collaborations = $this->map_polyline_shortcodes;
			foreach ( $collaborations as $collaboration ) {
				$this->output .= apply_filters('the_content', $collaboration );
			}
		}
	}

	/**
	 * Make map JSON available for script
	 *
	 * @return void
	 * @author 
	 **/
	protected function pass_json_as_var() {
		if ( class_exists( 'Leaflet_Map_Plugin_Extension' ) ) {
			$translate_this = array(
				'map_hash'      => $this->map_hash,
				'map_markers'   => $this->map_markers,
				'map_polylines' => $this->map_polylines,
			);
	        wp_localize_script( 'leaflet_create_route_js', 'leaflet_objects_' . $this->map_hash, $translate_this );
        }
	}
}
