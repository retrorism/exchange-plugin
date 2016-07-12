<?php
/**
 * Simple Content Grid Class File
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 10/07/2016
 *
 * @package Exchange Plugin
 **/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
};

/**
 * Related Grid Class
 *
 * This class serves as the basis for Related post views.
 *
 * @since 0.1.0
 **/
class SimpleGrid extends BaseGrid {
	/**
	 * Overwrite initial output value for Grid blocks
	 *
	 * @since 0.1.0
	 * @access protected
	 *
	 * @throws Exception when there's no valid input array.
	 **/
	 protected function create_output() {

		// If a grid is created inside a story, make this into an aside class.
		if ( is_single() ) {
			$el = 'aside';
		} else {
			$el = 'div';
		}
		$colour = '#' . $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['COLOURS']['salmon-1-web'];
		$this->set_grid_items();
		 // Create grid with posts embedded.
		if ( $this->has_grid_items ) {
			$this->set_modifier_class( 'colour', $colour );
			$this->set_attribute( 'data', 'background-colour', $colour );
			$this->output_tag_open( $el );
			foreach ( $this->grid_items as $item ) {
				$this->output .= $item->embed();
			}
			$this->output_tag_close( $el );
		}
	}
}
