<?php
/**
 * Tag Class
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 08/04/2016
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
 * Tag pattern class.
 *
 * This class serves to build Tag elements.
 *
 * @since 0.1.0
 **/
class Tag extends BasePattern {

	/**
	 * Overwrite initial output value for buttons.
	 *
	 * @since 0.1.0
	 * @access protected
	 **/
	 protected function create_output() {
		$el = 'a';
 		$this->output_tag_open( $el );
 		$this->output .= $this->input->name;
 		$this->output_tag_close( $el );
	}

}
