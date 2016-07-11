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
	 * Overwrite initial output value for Interviews.
	 *
	 * @since 0.1.0
	 * @access protected
	 **/
	 protected function create_output() {
		$this->output_tag_open();
		$this->output .= $this->build_interview() . PHP_EOL;
		$this->output_tag_close();
	}
}
