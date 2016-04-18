<?php
/**
 * Interview - conversation style
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 08/04/16
 *
 * @package Exchange Plugin
 **/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
};

/**
 * Interview Conversation Class
 *
 *  Class description
 *
 * @since 0.1.0
 **/
abstract class BaseInterview extends BasePattern {

	/**
	 * Constructor for Interview Conversation class objects.
	 *
	 * @since 0.1.0
	 * @access public
	 *
	 * @param mixed  $input Pattern content as defined in ACF input values.
	 * @param string $context String referring to pattern.
	 * @param array  $modifiers Optional. Additional modifiers that influence look and functionality.
	 **/
	public function __construct( $input, $context = '', $modifiers = array() ) {
		Parent::__construct( $input, $context, $modifiers );
		$this->output_tag_open();
		$this->output .= $this->build_interview( $input ) . PHP_EOL;
		$this->output_tag_close();
	}

}
