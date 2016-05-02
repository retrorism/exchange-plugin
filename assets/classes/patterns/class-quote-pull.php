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
	 * Overwrite initial output value for Pullquotes
	 *
	 * @since 0.1.0
	 * @access protected
	 **/
	 protected function create_output() {

		if ( ! empty( $this->input['pquote_source_text'] ) ) {
			$this->text = $this->input['pquote_text'];
		}

		if ( ! empty( $this->input['pquote_source_individual'] ) ) {
			$this->source['source_name'] = $this->input['pquote_source_individual'];
		}

		if ( ! empty( $this->input['pquote_source_info'] ) ) {
			$this->source['source_info'] = $this->input['pquote_source_info'];
		}

		if ( ! empty( $this->input['pquote_text'] ) ) {
			$this->output_tag_open( 'aside' );
			$this->output .= $this->input['pquote_text'];
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
