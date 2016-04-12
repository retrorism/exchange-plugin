<?php
/*
 * Byline Class
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 31/03/2016
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
 * Byline pattern class.
 *
 * This class serves to build paragraph elements.
 *
 * @since 0.1.0
 **/
class Byline extends BasePattern {

	/**
	 * Constructor for Paragraphs.
	 *
	 * At instantiation this method checks if input is a string and is not empty.
	 *
	 * @since 0.1.0
	 * @access public
	 *
	 * @param mixed $input Pattern content as defined in ACF input values.
	 * @param string $context Optional. String referring to pattern
	 * @param array $modifiers Optional. Additional modifiers that influence look and functionality.
	 **/
  function __construct( $input, $context = '', $modifiers = array() ) {
	Parent::__construct( $input, $context, $modifiers );

	if ( 'string' === gettype( $input ) && ! empty( $input ) ) {

		$this->output_tag_open('div');

		$this->output .= $input;

		$this->output_tag_close('div');

	}

  }

}
