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
	 * Constructor for Caption Pattern class objects.
	 *
	 * At instantiation this method adds background colour modifier,
	 *
	 * @since 0.1.0
	 *
	 * @param mixed  $input Pattern content as defined in ACF input values.
	 * @param string $context String referring to pattern.
	 * @param array  $modifiers Optional. Additional modifiers that influence look and functionality.
	 *
	 * @throws Exception Throws error when there's no parent set for this caption.
	 **/
	public function __construct( $input, $context = '', $modifiers = array() ) {
		Parent::__construct( $input, $context, $modifiers );

		if ( ! empty( $this->context ) ) {
			if ( 'image' === $this->context ) {
				$this->output_tag_open( 'figcaption' );
				$this->output .= $input;
				$this->output_tag_close( 'figcaption' );
			} elseif ( in_array( $this->context, array( 'blockquote', 'pullquote' ), true ) ) {
				$this->build_quote_caption( $input );
			} else {
				throw new Exception( 'No valid parent for this caption.' );
			}
		}
		// End construct.
	}

	/**
	 * Create quote caption if source name and/or source info is set.
	 *
	 * @since 0.1.0
	 *
	 * @param array $input List of ACF fields.
	 **/
	protected function build_quote_caption( $input ) {
		if ( ! empty( $input['source_name'] ) || ! empty( $input['source_info'] ) ) {
			$this->output_tag_open( 'footer' );
			$this->output .= '<cite>';

			// Add name if available.
			if ( ! empty( $input['source_name'] ) ) {
				$this->output .= '<div class="' . $this->context . '__source-name">' . $input['source_name'] . '</div>' . PHP_EOL;
			}

			// Add info if available.
			if ( ! empty( $input['source_info'] ) ) {
				$info_cleaned = strip_tags( apply_filters( 'the_content',$input['source_info'] ),'<a>' );
				$this->output .= '<p class="' . $this->context . '__source-info">' . $info_cleaned . '</p>' . PHP_EOL;
			}

			$this->output .= '</cite>';
			$this->output_tag_close( 'footer' );
		}
	}
}
