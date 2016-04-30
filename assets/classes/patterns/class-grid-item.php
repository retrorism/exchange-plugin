<?php
/**
 * Grid Item Class
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 12/04/2016
 *
 * @package Exchange Plugin
 **/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
};

/**
 * GridItem pattern class.
 *
 * This class serves to build Item elements.
 *
 * @since 0.1.0
 **/
class GridItem extends BasePattern {

	/**
	 * Constructor for Item Pattern class objects.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed  $input Pattern content as defined in ACF input values.
	 * @param string $context String referring to pattern.
	 * @param array  $modifiers Optional. Additional modifiers that influence look and functionality.
	 *
	 * @throws Exception Throws error when there's no parent set for this Item.
	 **/
	 public function __construct( $input, $context = '', $modifiers = array() ) {
 		Parent::__construct( $input, $context, $modifiers );
 		$this->output_tag_open();
 		//$this->output .= $this->build_grid_item( $input, $modifiers );
		$this->output .= Timber::render('grid-item.twig');
 		$this->output_tag_close();
 		// End construct.
 	}

	/**
	 * Build Item output
	 *
	 * @since 0.1.0
	 *
	 * @param array $input List of ACF fields.
	 **/
	protected function build_grid_item( $exchange, $modifiers ) {
		if ( locate_template( 'parts/grid-' . $exchange->type . '.php' ) !== '') {
			$template = $exchange->type;
		} elseif ( locate_template( 'parts/grid-default.php' ) !== '') {
			$template = 'default';
		} else {
			$template = false;
		}
		if ( $template ) {
			ob_start();
			include( locate_template( 'parts/grid-' . $template .'.php' ) );
			$grid_item = ob_get_contents();
			ob_end_clean();
		} else {
			$grid_item = "I couldn't find the right template";
		}
		return $grid_item;
	}
}
