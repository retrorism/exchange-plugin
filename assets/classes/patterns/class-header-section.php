<?php
/**
 * Section Header Class
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
 * Section Header pattern class.
 *
 * This class serves to build section headers.
 *
 * @since 0.1.0
 **/
class SectionHeader extends BasePattern {

	/**
	 * Constructor for Caption Pattern class objects.
	 *
	 * At instantiation this method adds background colour modifier,
	 *
	 * @since 0.1.0
	 *
	 * @param mixed  $input Pattern content as defined in ACF input values.
	 * @param string $context String referring to pattern.
	 * @param array  $modifiers Optional. Additional modifiers that influence look and functionality.
	 **/
	public function __construct( $input, $context = '', $modifiers = array() ) {
		Parent::__construct( $input, $context, $modifiers );

		$this->output_tag_open( 'header' );
		$this->output .= '<h2>' . $input . '</h2>' . PHP_EOL;
		$this->output_tag_close( 'header' );

	}
}
