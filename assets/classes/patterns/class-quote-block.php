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
class BlockQuote extends BaseQuote {

	/**
	 * Overwrite initial output value for Blockquotes
	 *
	 * @since 0.1.0
	 * @access protected
	 **/
	 protected function create_output() {
		if ( empty( $this->input['bquote_text'] ) ) {
			return;
		}

		// Open element.
		$this->output_tag_open();
		$this->output .= '<blockquote>';
		$this->output .= $this->input['bquote_text'];
		$this->output .= '</blockquote>';

		// Prepare caption.
		$this->set_quote_caption();
		if ( $this->has_caption ) {
			$this->output .= $this->caption->embed();
		}

		// Close element.
		$this->output_tag_close();
	}
}
