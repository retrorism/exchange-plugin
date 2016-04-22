<?php
/**
 * PullQuote Class
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 08/03/2016
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
 * Pullquote pattern class.
 *
 * This class serves to build paragraph elements.
 *
 * @since 0.1.0
 **/
class PullQuote extends BasePattern {

	/**
	 * Array with source information.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var array $classes Class-list.
	 **/
	protected $source = array();

	/**
	 * Constructor for Caption Pattern class objects.
	 *
	 * At instantiation this method adds background colour modifier,
	 *
	 * @since 0.1.0
	 *
	 * @param mixed  $input Pattern content as defined in ACF input values.
	 * @param string $context Optional. String referring to pattern.
	 * @param array  $modifiers Optional. Additional modifiers that influence look and functionality.
	 *
	 * @throws Exception Throws error when there's no parent set for this caption.
	 **/
	function __construct( $input, $context = '', $modifiers = array() ) {
		Parent::__construct( $input, $context, $modifiers );

		if ( ! empty( $input['pquote_source_text'] ) ) {
			$this->text = $input['pquote_text'];
		}

		if ( ! empty( $input['pquote_source_individual'] ) ) {
			$this->source['source_name'] = $input['pquote_source_individual'];
		}

		if ( ! empty( $input['pquote_source_info'] ) ) {
			$this->source['source_info'] = $input['pquote_source_info'];
		}

		if ( ! empty( $input['pquote_text'] ) ) {
			$this->output_tag_open( 'aside' );
			$this->output .= $input['pquote_text'];
		}

		// If source is set, add quote source name as caption.
		if ( ! empty( $this->source ) ) {
			$caption = new Caption( $this->source, $this->element );
			$this->output .= $caption->embed();
		}

		// Close element.
		$this->output_tag_close( 'aside' );
	}
}
