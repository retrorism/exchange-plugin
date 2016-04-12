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
	 * Constructor for Related Grid Class.
	 *
	 * @since 0.1.0
	 * @access public
	 * @param array  $input Collection of one or more related content items
	 * @param string $context Optional. String referring to pattern.
	 * @param array  $modifiers Optional. Additional modifiers that influence look and functionality.
	 **/
	public function __construct( $input, $context = '', $modifiers = array() ) {
		Parent::__construct( $input, $context, $modifiers );

		if ( $this->has_grid_items ) {

			$this->output_tag_open( 'aside' );
			foreach ( $this->grid_items as $item ) {
				$this->output .= $item->embed();
			}
			$this->output_tag_close( 'aside' );
		}
	}
}
