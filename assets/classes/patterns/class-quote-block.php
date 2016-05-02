<?php
/**
 * BlockQuote Class
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
 * Blockquote pattern class.
 *
 * This class serves to build paragraph elements.
 *
 * @since 0.1.0
 **/
class BlockQuote extends BasePattern {

	/**
	 * Quote caption.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var object $caption Contains the caption once this is instantiated.
	 **/
	public $caption;

	/**
	 * List of details about the source. Default value is an empty array.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var array $source Extra information about the quote source.
	 **/
	public $source = array();

	/**
	 * Overwrite initial output value for Blockquotes
	 *
	 * @since 0.1.0
	 * @access protected
	 **/
	 protected function create_output() {
		if ( ! empty( $this->input['bquote_text'] ) ) {

			$source_ind = $this->input['bquote_source_individual'];
			$source_info = $this->input['bquote_source_info'];

			// Open element.
			$this->output_tag_open();
			$this->output .= '<blockquote>' . $this->input['bquote_text'] . '</blockquote>';

			// Prepare caption.
			if ( ! empty( $source_ind ) || ! empty( $source_info ) ) {
				$this->set_quote_caption( $source_ind, $source_info );
				if ( is_object( $this->caption ) ) {
					$this->output = $this->build_quote_caption();
				}
			}

			// Close element.
			$this->output_tag_close();
		}
	}

	/**
	 * Create caption.
	 *
	 * @since 0.1.0
	 *
	 * @param string $source_ind String from ACF fields for individual source.
	 * @param string $source_info String from ACF fields for extra source info.
	 **/
	protected function set_quote_caption( $source_ind, $source_info ) {
		if ( ! empty( $source_ind ) ) {
			$this->source['source_name'] = $source_ind;
		}

		if ( ! empty( $source_info ) ) {
			$this->source['source_info'] = $source_info;
		}

		if ( count( $this->source ) > 0 ) {
			$caption = new Caption( $this->source, $this->element );
		}
	}

	/**
	 * Return caption for output.
	 *
	 * @since 0.1.0
	 *
	 * @return string $embed HTML string containing the quote. Defaults to ''.
	 **/
	protected function build_quote_caption() {

		// Add quote source name as caption.
		$embed = $this->caption->embed();
		if ( ! empty( $embed ) ) {
			return $embed;
		}
	}
}
