<?php
/**
 * Embedded Video pattern
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
class Embed extends BasePattern {

	/**
	 * Video Caption, if available.
	 *
	 * @since 0.1.0
	 * @access private
	 * @var object $caption Image orientation.
	 **/
	private $caption;

	/**
	 * Prepare ACF input for embedding
	 *
	 * @param type var Description
	 * @return string $output HTML string
	 */
	protected function create_output() {
		// Get iframe HTML
		$iframe = $this->set_embed_attributes();
		$el = 'div';
		if ( $this->context === 'gallery' ) {
			$this->classes[] = 'gallery__item';
			$this->classes[] = 'orbit-slide';
			$el = 'li';
		}

		// Open element.
		$this->output_tag_open($el);
		
		if ( 'gallery' === $this->context ) {
			$this->output .= '<div class="gallery__video-wrapper">';
		}

		$this->output .= $iframe;

		// Set caption if available.
		if ( ! empty( $this->input['video_caption'] ) ) {
			$this->set_video_caption();
		}

		// Add caption if set.
		if ( is_object( $this->caption ) ) {
		   $this->output .= $this->caption->embed();
		}

		if ( 'gallery' === $this->context ) {
			// Close wrapper.
			$this->output .= '</div>';
		}
		
		// Close element.
		$this->output_tag_close($el);

	}

	/**
	 * Set iframe attributes according to video provider
	 *
	 * @param type var Description
	 * @return string $iframe Iframe code
	 *
	 * @TODO Vimeo API to add extra styling
	 */
	protected function set_embed_attributes() {
		$embed_code = $this->input['embed_code'];
			
 		// use preg_match to find iframe src
 		preg_match( '/src="(.+?)"/', $embed_code, $matches );
		if ( empty( $matches ) ) {
			return;
		}
 		$src = $matches[1];

		// Detect vimeo url.
		if ( is_string( strstr( $src, 'vimeo' ) ) ) {

	 		// Add extra params to iframe src.
	 		$params = array(
	 			'badge'    => 0,
	 			'byline'   => 0,
	 			'color'    => $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['COLOURS']['yellow-tandem'],
	 			'portrait' => 0,
	 			'title'    => 0,
	 		 );

	 		$new_src = add_query_arg($params, $src);

			$embed_code = str_replace($src, $new_src, $embed_code);

		}

 		// Add extra attributes to iframe html
 		$attributes = ' frameborder="2"';
 		$clean_embed_code = str_replace('></iframe>', ' ' . $attributes . '></iframe>', $embed_code );
		return $clean_embed_code;
	}

	/**
	 * Set video caption object from input and modifiers.
	 *
	 * @since 0.1.0
	 **/
	protected function set_video_caption() {

		if ( empty( $this->input['video_caption'] ) ) {
			return;
		}
		$this->caption = new Caption( $this->input['video_caption'], $this->element );
	}
}
