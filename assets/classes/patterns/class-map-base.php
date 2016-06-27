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
	 * Map Markers variable
	 *
	 * @var array $map_markers Items array containing all Mappable objects
	 */
	protected $map_markers;

	/**
	 * Collaboration Data variable
	 *
	 * @var array $collaboration_data Items array containing all Mappable objects
	 */
	protected $collaboration_data;

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
		if ( is_single() ) {
			$el = 'figure';
		}

		if ( class_exists( 'Exchange_Leaflet_Map' ) ) {
			$map_shortcode = $this->prepare_map_attributes();

			if ( 'dots' === $this->input['map_style'] ) {
				$this->set_map_markers();
			} elseif ( 'network' === $this->input['map_style'] ) {
				$this->set_map_collaborations();
			}

			$this->set_caption();

			$this->output_tag_open( $el );
			$this->output .= apply_filters('the_content', $map_shortcode);

			// Add individual marker script elements.
			if ( $this->has_markers ) {
				$markers = $this->map_markers;
				foreach ( $markers as $marker ) {
					$this->output .= apply_filters('the_content', $marker );
				}
			} elseif ( $this->has_network ) {
				$collaborations = $this->collaboration_data;
				foreach ( $collaborations as $collaboration ) {
					$this->output .= apply_filters('the_content', $collaboration );
				}
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
	protected function set_map_markers() {
		$markers = $this->input['map_markers'];
		if ( ! is_array( $markers ) && count( $markers ) > 0 )  {
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
	protected function set_map_collaborations() {
		$collaborations = $this->input['map_collaborations'];
		if ( ! is_array( $collaborations ) && count( $collaborations ) > 0 )  {
			return;
		} else {
			foreach( $collaborations as $collaboration ) {
				$this->set_collaboration_data( $collaboration );
			}
			if ( ! empty( $this->collaboration_data ) ) {
				$this->has_network = true;
			}
		}
	}

	protected function prepare_map_attributes() {
		$sizes = $this->get_map_size();
		$map_shortcode = '[leaflet-map zoomcontrol=1 ';
		$map_shortcode .= 'zoom=' . $this->input['map_zoom_level'] . ' ';
		$map_shortcode .= 'height=' . $sizes[1] . ' ';
		$map_shortcode .= 'lat=' . $this->input['map_center']['lat'] . ' ';
		$map_shortcode .= 'lng=' . $this->input['map_center']['lng'] . ']';
		return $map_shortcode;
	}

	/**
	 * Retrieve the right map size from the users selection
	 *
	 * @param string $key ACF input
	 * @return array $sizes with one pair of sizes.
	 */
	protected function get_map_size() {
		$sizes = array(
			'wide' => array( '100%', '460px' ),
			'square' => array( '100%', '575px' ),
			'small' => array( '100%', '460px' ),
		);
		$key = $this->input['map_size'];
		return $sizes[$key];
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
		$c_object = BaseController::exchange_factory( $collaboration );
		if ( ! $c_object instanceof Collaboration ) {
			return;
		}
		if ( ! $c_object->has_locations ) {
			return;
		}
		// Create label
		$line_label = addslashes( exchange_create_link( $c_object ) );
		// Feed the collaboration geodata into a shortcode
		$location_line = $this->create_map_line( $c_object->locations, $line_label );
		if ( is_string( $location_line ) ){
			$this->collaboration_data[] = $location_line;
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
				$this->create_map_marker( $verified_marker );
			}
		}
	}

	/** Add map markers.
	 *
	 * If not empty, sets input array to map_markers property.

	 */
	protected function create_map_marker( $marker ) {

		$marker_shortcode = '[leaflet-marker ';
		// $marker_shortcode .= 'visible="true" ';
		$marker_shortcode .= 'lat=' . $marker['lat'] . ' ';
		$marker_shortcode .= 'lng=' . $marker['lng'] . ']';
		if ( isset( $marker['message'] ) ) {
			$marker_shortcode .= $marker['message'] . '[/leaflet-marker]';
		}
		$this->map_markers[] = $marker_shortcode;
	}

	/**
	 * Create map line
	 *
	 * @param array $locations Array with lats, longs and cities for each participant's organisation.
	 * @return string $line Map line shortcode with either addresses or coordinates.
	 */
	protected function create_map_line( $locations, $line_label = '' ) {
		$line = '[leaflet-line ';
		$line .= 'color="#' . $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['COLOURS']['yellow-tandem'] . '" ';

		$latlngs = array();
		$cities = array();
		foreach ( $locations as $location ) {
			if ( ! empty( $location['org_lat'] ) && ! empty( $location['org_lng'] ) ) {
				$latlngs[] = $location['org_lat'] . ', '. $locations['org_lng'];
			}
			if ( !empty( $location['org_city'] ) ) {
				$cities[] = $location['org_city'];
			}
		}
		if ( count( $latlngs ) > 1 ) {
			$line .= 'latlngs' . implode( '; ', $latlngs ) . ';"]';
		} elseif ( count( $cities ) > 1 ) {
			$line .= 'addresses="' . implode( '; ', $cities ) . ';"]';
		} else {
			return false;
		}
		if ( ! empty( $line_label ) ) {
			$line .= $line_label . '[/leaflet-line]';
		}
		return $line;
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
}
