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
	 * Map Properties variable
	 *
	 * @var array $map_properties Array containing all Map Properties
	 */
	protected $map_properties;

	/**
	 * Map Markers Check
	 *
	 * @var boolean $has_markers
	 */
	protected $has_markers = false;

	/**
	 * Map Caption Check
	 *
	 * @var boolean $has_caption
	 */
	protected $has_caption = false;

	/**
	 * Overwrite initial output value for Grid blocks
	 *
	 * @since 0.1.0
	 * @access protected
	 *
	 * TODO Do something with this:
	 *
	 * add_shortcode('leaflet-map', array(&$this, 'map_shortcode'));
	 * add_shortcode('leaflet-marker', array(&$this, 'marker_shortcode'));
	 * add_shortcode('leaflet-line', array(&$this, 'line_shortcode'));
	 * add_shortcode('leaflet-image', array(&$this, 'image_shortcode'));
	 *
	 */
	 **/
	 protected function create_output() {

		// If a grid is created inside a story, make this into an figure class.
		if ( is_single() ) {
			$el = 'figure';
		}
		if ( class_exists( 'Exchange_Leaflet_Map' ) ) {
			// $this->set_map;
			$this->set_map_markers();

			 // Create grid with posts embedded.
			if ( $this->has_map_markers ) {
				$this->output_tag_open( $el );
				foreach ( $this->map_markers as $item ) {
					$this->output .= $item->embed();
				}
				$this->output_tag_close( $el );
			}
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
		$markers = $this->input['map_markers']
		if ( ! is_array( $markers ) && count( $markers ) > 0 )  {
			return;
		} else {
			foreach( $markers as $marker ) {
				$add_marker = $this->verify_and_add_marker_data( $marker );
			}
			if ( count( $this->markers > 0 ) ) {
				$this->has_markers = true;
			}
		}
	}

	/** Add map markers.
	 *
	 * If not empty, sets input array to map_markers property.
	 *
	 */
	public function verify_and_add_marker_data( $marker ) {
		$marker_title = $marker['map_marker_title'];
		$marker_location = $marker['map_marker_location'];
		$marker_linked_object = $marker['map_marker_link']
		if ( empty( $marker_title ) )
			return;
		} else {
			// @TODO Possibly add another pattern just for the label.
			$marker_label = array(
				'title' = $marker_title;
			)
			$marker_label['linked_object'] = is_object( $marker_linked_object ) ? $marker_linked_object : null;
		}
		// See if both long and lat are set
		if ( is_array( $marker_location ) && array_key_exists( 'lat', $marker_location ) && array_key_exists( 'long', $marker_location ) ) {
			if ( is_float( $marker_location['lat'] ) && is_float( $marker_location['long'] ) ) {
				$marker = $this->create_map_marker( $marker_location, $marker_label );
				$this->add_marker( $marker );
			}
		}
	}

	/** Add map markers.
	 *
	 * If not empty, sets input array to map_markers property.

	 */
	protected function add_map_marker( $marker ) {
		$this->markers[] = $marker;
	}

	/**
	 * Remove map marker.
	 *
	 * If found, removes item with corresponding post_id from map_markers property.
	 *
	 * @param integer $post_id Post_id stored in grid-item.
	 * @TODO I'm guessing this should be a frontend JS function instead.
	 */
	protected function remove_map_marker() {
	}

	/**
	 * Create map marker.
	 * @param array $marker_location Array with lat and long
	 * @param array $marker_label Array with title and possibly page ID as link.
	 */
	protected function create_map_marker( $marker_location, $marker_label ) {
		$marker = array(
			'title' => $marker_label['title'];
			'lat'   => $marker_location['lat'];
			'long'  => $marker_location['long'];
		);
		if ( ! empty( $marker_label['linked_object'] ) {
			// Create link from object
			$object = BaseController::exchange_factory( $marker_label['linked_object'] );
			$link = exchange_create_link( $object, false );
		}
		if ( $link ) {
			$marker['title'] = $link . $marker_label['title'] . '</a>';
		}
		return $marker;
	}
	/**
	 * Add term modifiers to post before creating Pattern object.
	 *
	 * @access public
	 * @return array $modifiers Modifiers with / without updated term list.
	 */
	public static function add_grid_modifiers() {
	}
}
