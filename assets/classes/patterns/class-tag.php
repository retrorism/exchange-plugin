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
	 * Data
	 *
	 * @access protected
	 * @var array $data Data attributes can be passed through modifiers array.
	 **/
	protected $data;

	/**
	 * Constructor for Tag Pattern class objects.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed  $input Pattern content as defined in ACF input values.
	 * @param string $context String referring to pattern.
	 * @param array  $modifiers Optional. Additional modifiers that influence look and functionality.
	 *
	 * @throws Exception Throws error when there's no parent set for this Tag.
	 **/
	 public function __construct( $input, $context = '', $modifiers = array() ) {
 		Parent::__construct( $input, $context, $modifiers );
		$el = 'span';
		if ( ! empty( $modifiers['link_attributes'] ) ) {
			$el = 'a';
		}
 		$this->output_tag_open( $el );
 		$this->output .= $input['Tag_text'];
 		$this->output_tag_close( $el );
 		// End construct.
 	}

	/**
	 * Create Tag.
	 *
	 * @since 0.1.0
	 *
	 * @param array $input List of ACF fields.
	 **/
	protected function build_tag( $input ) {
	}
}
