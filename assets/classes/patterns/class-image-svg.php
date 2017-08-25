<?php
/**
 * Image SVG Class
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 09/04/2016
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
 * Image SVG pattern class.
 *
 * This class serves to build SVG Graphics for the grid, emphasis blocks and sections.
 *
 * @since 0.1.0
 **/
class ImageSVG extends BasePattern {

	/**
	 * Overwrite initial output value for images.
	 *
	 * @since 0.1.0
	 * @access protected
	 **/
	protected function create_output() {

		$this->output_tag_open( 'figure' );
		if ( $this->is_programme_logo() ) {
			$this->output .= $this->build_logo();
		} elseif ( is_array( $this->input ) && ! empty( $this->input['ID'] ) ) {
			$this->output .= $this->build_svg( get_attached_file( $this->input['ID'] ) );
		} else {
			$this->output .= __( 'Oh no! We cannot create this graphic', EXCHANGE_PLUGIN );
		}

		// Close element.
		$this->output_tag_close( 'figure' );

		// Close wrapper.
		if ( 'story__header' === $this->context ) {
			$this->output .= '</div>';
		}
	}

	/**
	 * Check if input has been created through ACF
	 *
	 * @since 0.1.0
	 * @access protected
	 **/
	private function is_programme_logo() {
		if ( ! is_string( $this->input ) ) {
			return false;
		}
		if ( array_key_exists( remove_accents( $this->input ), $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['IMAGES']['programme-logos'] ) ) {
			return true;
		} else {
			return false;
		}
	}

	protected function build_logo() {
		$white = '_White';
		$input = remove_accents( $this->input );
		$programme_logo = get_template_directory() . '/assets/images/svg/T_logo_' . $input . $white . '_WEB.svg';
		if ( ! file_exists( $programme_logo ) ) {
			return;
		}
		$this->build_svg( $programme_logo, true );
	}

	/**
	 * Inline SVG from file
	 *
	 * @param string $svg_src Path to SVG file.
	 * @return void.
	 */
	protected function build_svg( $svg_src, $fallback = false ) {
		if ( ! is_string( $svg_src ) || '' === $svg_src ) {
			return;
		}
		if ( ! is_readable( $svg_src ) ) {
			return;
		}
		$svg = file_get_contents( $svg_src );
		if ( $fallback ) {
			$png_src = str_replace( 'svg', 'png', $svg_src );
			$svg = $this->insert_svg_fallback( $svg, $png_src );
		}
		$this->output .= $svg;
	}

	protected function insert_svg_fallback( $svg, $png_src ) {
		// Add png fallback if available.
		if ( !is_readable( $png_src ) ) {
			return $svg;
		} else {
			$png_insert = '<image src="' . $png_src . '" xlink:href=""></svg>';
			$svg = substr_replace( $svg, $png_insert, -6 );
			return $svg;
		}
	}
}
