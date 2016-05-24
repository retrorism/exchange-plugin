<?php
/**
 * Caption Class
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
 * Caption pattern class.
 *
 * This class serves to build caption elements.
 *
 * @since 0.1.0
 **/
class Caption extends BasePattern {

	/**
	 * Overwrite initial output value for Captions.
	 *
	 * @since 0.1.0
	 * @access protected
	 **/
	protected function create_output() {
		if ( ! empty( $this->context )
			&& in_array( $this->context, array( 'image', 'simplemap', 'video' ), true ) ) {
			$this->output_tag_open( 'figcaption' );
			$this->output .= $this->input;
			$this->output_tag_close( 'figcaption' );
		} elseif ( in_array( $this->context, array( 'blockquote', 'pullquote' ), true ) ) {
			$this->build_quote_caption();
		} else {
			throw new Exception( 'No valid parent for this caption.' );
		}
	}

	/**
	 * Create quote caption if source name and/or source info is set.
	 *
	 * @since 0.1.0
	 **/
	protected function build_quote_caption() {
		if ( ! empty( $this->input['source_name'] ) || ! empty( $this->input['source_info'] ) ) {
			$this->output_tag_open( 'footer' );
			$this->output .= '<cite>';

			// Add name if available.
			if ( ! empty( $this->input['source_name'] ) ) {
				$this->output .= '<div class="' . $this->context . '__source-name">' . $this->input['source_name'] . '</div>' . PHP_EOL;
			}

			// Add info if available.
			if ( ! empty( $this->input['source_info'] ) ) {
				$info_cleaned = strip_tags( apply_filters( 'the_content',$this->input['source_info'] ),'<a>' );
				$this->output .= '<p class="' . $this->context . '__source-info">' . $info_cleaned . '</p>' . PHP_EOL;
			}

			$this->output .= '</cite>';
			$this->output_tag_close( 'footer' );
		}
	}
}
