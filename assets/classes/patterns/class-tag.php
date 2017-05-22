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
	 	if ( empty( $this->input->name ) || ! is_string( $this->input->name ) ) {
	 		return;
	 	}
		$el = 'a';
 		$this->output_tag_open( $el );
 		if ( current_theme_supports( 'lowercase_tags' ) && 'location' !== $this->input->taxonomy ) {
 			$name = '<span>' . strtolower( $this->input->name ) . '</span>';
		} else {
 			$name = '<span>' . $this->input->name . '</span>';
		}
		$this->output .= $name;
 		$this->output_tag_close( $el );
	}

}
