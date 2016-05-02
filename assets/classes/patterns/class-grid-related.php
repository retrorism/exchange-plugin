<?php
/**
 * Related Content Grid Class File
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 12/04/2016
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
class RelatedGrid extends BaseGrid {

	/**
	 * Overwrite initial output value for Subheaders.
	 *
	 * @since 0.1.0
	 * @access protected
	 **/
	 protected function create_output() {
		if ( $this->has_grid_items ) {
			$this->output_tag_open();
			foreach ( $this->grid_items as $item ) {
				$this->output .= $item->embed();
			}
			$this->output_tag_close();
		}
	}
}
