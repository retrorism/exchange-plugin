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
	 * Overwrite initial output value for Duo Images.
	 *
	 * @since 0.1.0
	 * @access protected
	 **/
	 protected function create_output() {

		// Check if there are two images and add them to gallery.
		if ( 2 === count( $this->input ) ) {

			// Open element.
			$this->output_tag_open();
			$this->set_gallery(  );
			// Close element.
			$this->output_tag_close();
		}
	}

	/**
	 * Set gallery with two image objects.
	 *
	 * @since 0.1.0
	 **/
	protected function set_gallery() {
		$i = 0;
		foreach ( $this->input as $image ) {
			if ( ! empty( $image['filename'] ) ) {
				$this->gallery[ $i ] = $image;
			}
			$pos = 'left';
			if ( 1 === $i ) {
				$pos = 'right';
			}
			$mods['position'] = $pos;
			$gallery_item = new Image( $this->gallery[ $i ], $this->element, $mods );
			$this->output .= $gallery_item->embed();
			$i++;
		}
	}
}
