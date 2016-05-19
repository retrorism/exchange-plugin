<?php
/**
 * Base Class
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 19/05/2016
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
 * BaseQuote pattern class.
 *
 * This class serves to build paragraph elements.
 *
 * @since 0.1.0
 **/
abstract class BaseQuote extends BasePattern {

	/**
	 * Quote caption.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var object $caption Contains the caption once this is instantiated.
	 **/
	public $caption;

	/**
	 * Quote caption check
	 *
	 * @since 0.1.0
	 * @access public
	 * @var boolean $has caption Whether there is a caption present. Defaults to false.
	 **/
	public $has_caption = false;

	/**
	 * List of details about the source. Default value is an empty array.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var array $source Extra information about the quote source.
	 **/
	public $source = array();

	/**
	 * Create caption.
	 *
	 * @since 0.1.0
	 *
	 * @param string $source_ind String from ACF fields for individual source.
	 * @param string $source_info String from ACF fields for extra source info.
	 **/
	protected function set_quote_caption() {
		if ( 'BlockQuote' === get_class( $this ) ) {
			$source_ind = $this->input['bquote_source_individual'];
			$source_info = $this->input['bquote_source_info'];
		} elseif ( 'PullQuote' === get_class( $this ) ) {
			$source_ind = $this->input['pquote_source_individual'];
			$source_info = $this->input['pquote_source_info'];
		}
		if ( ! empty( $source_ind ) ) {
			$this->source['source_name'] = $source_ind;
		}

		if ( ! empty( $source_info ) ) {
			$this->source['source_info'] = $source_info;
		}

		if ( count( $this->source ) ) {
			$this->caption = new Caption( $this->source, $this->element );
			$this->has_caption = true;
		}
	}
}
