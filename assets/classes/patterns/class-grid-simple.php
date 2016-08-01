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
		$this->set_grid_items();
		 // Create grid with posts embedded.
		if ( $this->has_grid_items ) {
			$this->output_tag_open( $el );
			$sum = 0;
			foreach ( $this->grid_items as $item ) {
				if ( $sum == 0 ) {
					$this->output .= '<div class="row" data-equalizer>';
				}
				$this->fill_rows( $item, $sum );
				$width = $item->get_modifier( 'grid_width_num' );
				if ( $width ) {
					$sum = $sum + $width;
				}
				if ( $sum > 0 && 12 / $sum == 1 ) {
					$this->output .= '</div><!--end equalizer-row-->';
					$sum = 0;
				}
			}
			$this->output_tag_close( $el );
		}
	}

	protected function fill_rows( $item, $sum ) {
		$this->output .= $item->embed();
	}
}
