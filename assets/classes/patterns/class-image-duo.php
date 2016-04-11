<?php
/**
 * Image Duo Class
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
 * Image Duo pattern class.
 *
 * This class serves to build image pairs that need to be shown side by side;
 *
 * @since 0.1.0
 *
 * @TODO Test for portrait / landscape portraits.
 **/
class ImageDuo extends BasePattern {

	/**
	 * Array with the two images.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var array $gallery List consisting of - ideally - two images.
	 **/
	public $gallery = array();

	/**
	 * Constructor for Image Duo's.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed  $input Pattern content as defined in ACF input values.
	 * @param string $parent Optional. String referring to pattern.
	 * @param array  $modifiers Optional. Additional modifiers that influence look and functionality.
	 **/
	function __construct( $input, $parent = '', $modifiers = array() ) {
		Parent::__construct( $input, $parent, $modifiers );

		// Check if there are two images and add them to gallery.
		if ( 2 === count( $input ) ) {

			// Open element.
			$this->output_tag_open( 'section' );

			$this->set_gallery( $input );

			// Close element.
			$this->output_tag_close( 'section ' );
		}
	}

	/**
	 * Set gallery with two image objects.
	 *
	 * @since 0.1.0
	 *
	 * @param array $input ACF field input.
	 **/
	protected function set_gallery( $input ) {
		$i = 0;
		foreach ( $input as $image ) {
			if ( ! empty( $image['filename'] ) ) {
				$this->gallery[ $i ] = $image;
			}
			$pos = 'left';
			if ( 1 === $i ) {
				$pos = 'right';
			}
			$mods['position'] = $pos;
			$gallery_item = new Image( $this->gallery[ $i ], $this->base, $mods );
			$this->output .= $gallery_item->embed();
			$i++;
		}
	}
}
