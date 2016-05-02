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
	 * Overwrite initial output value for editorial intros
	 *
	 * @since 0.1.0
	 * @access protected
	 **/
	 protected function create_output() {
		if ( is_string( $this->input ) && ! empty( $this->input ) ) {
			$this->output_tag_open();
			$content = new Paragraph( $this->input, $this->element );
			$this->output .= $content->embed();
			$this->output_tag_close();
		}
	}
}
