<?php
/**
 * Section Subheader Class
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
 * Section Subheader pattern class.
 *
 * This class serves to build section headers.
 *
 * @since 0.1.0
 **/
class SubHeader extends BasePattern {

	/**
	 * Constructor for Caption Pattern class objects.
	 *
	 * At instantiation this method adds background colour modifier,
	 *
	 * @since 0.1.0
	 *
	 * @param mixed  $input Pattern content as defined in ACF input values.
	 * @param string $parent Optional. String referring to pattern.
	 * @param array  $modifiers Optional. Additional modifiers that influence look and functionality.
	 *
	 * @throws Exception Throws error when there's no parent set for this caption.
	 **/
	public function __construct( $input, $parent = '', $modifiers = array() ) {
		Parent::__construct( $input );

		$this->output_tag_open( 'header' );
		$this->output .= '<h3>' . $input . '</h3>' . PHP_EOL;
		$this->output_tag_close( 'header' );

	}
}
