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
	 * Overwrite initial output value for Section Headers.
	 *
	 * @since 0.1.0
	 * @access private
	 **/
	 protected function create_output() {
		$this->output_tag_open();
		$this->output .= '<h2>' . $this->input . '</h2>' . PHP_EOL;
		$this->output_tag_close();
	}
}
