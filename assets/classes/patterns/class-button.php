<?php
/**
 * Button Class
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 08/04/2016
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
 * Button pattern class.
 *
 * This class serves to build button elements.
 *
 * @since 0.1.0
 **/
class Button extends BasePattern {

	/**
	 * Data
	 *
	 * @access protected
	 * @var array $data Data attributes can be passed through modifiers array.
	 **/
	protected $data;

	/**
	 * Constructor for Button Pattern class objects.
	 *
	 * At instantiation this method adds button colour.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed  $input Pattern content as defined in ACF input values.
	 * @param string $parent String referring to pattern.
	 * @param array  $modifiers Optional. Additional modifiers that influence look and functionality.
	 *
	 * @throws Exception Throws error when there's no parent set for this button.
	 **/
	 public function __construct( $input, $parent = '', $modifiers = array() ) {
 		Parent::__construct( $input, $parent, $modifiers );
		$el = 'button';
		if ( ! empty( $modifiers['link_attributes'] ) ) {
			$el = 'a';
		}
 		$this->output_tag_open( $el );
 		$this->output .= $input['button_text'];
 		$this->output_tag_close( $el );
 		// End construct.
 	}

	/**
	 * Create button.
	 *
	 * @since 0.1.0
	 *
	 * @param array $input List of ACF fields.
	 **/
	protected function build_button( $input ) {
	}
}
