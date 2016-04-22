<?php
/**
 * Editorial Intro Class
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 07/03/2016
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
 * Paragraph pattern class.
 *
 * This class serves to build paragraph elements.
 *
 * @since 0.1.0
 **/
class EditorialIntro extends BasePattern {

	/**
	 * Constructor for Editorial Intros.
	 *
	 * At instantiation this method checks if input is a string and is not empty.
	 *
	 * @since 0.1.0
	 * @access public
	 *
	 * @param mixed  $input Pattern content as defined in ACF input values.
	 * @param string $context Optional. String referring to pattern.
	 * @param array  $modifiers Optional. Additional modifiers that influence look and functionality.
	 **/
	function __construct( $input, $context = '', $modifiers = array() ) {
		Parent::__construct( $input, $context, $modifiers );
		if ( is_string( $input ) && ! empty( $input ) ) {
			$this->output_tag_open();
			$content = new Paragraph( $input, $this->element );
			$this->output .= $content->embed();
			$this->output_tag_close();
		}
	}
}
