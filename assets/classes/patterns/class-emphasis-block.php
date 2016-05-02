<?php
/**
 * Emphasis Block Class
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 08/04/2016
 *
 * @package Exchange Plugin
 **/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
};

/**
 * Emphasis Block (Post-It) Class
 *
 *  This pattern creates all Post-It-style Emphasis blocks
 *
 * @since 0.1.0
 **/
class EmphasisBlock extends BasePattern {

	/**
	 * Overwrite initial output value for Emphasis blocks
	 *
	 * @since 0.1.0
	 * @access protected
	 **/
	 protected function create_output() {
		if ( is_array( $this->input ) && count( $this->input ) > 0 ) {
			$this->output_tag_open();
			$type = isset( $this->modifiers['type'] ) ? $this->modifiers['type'] : 'post-it';
			$this->output .= $this->build_block_elements( $type );
			$this->output_tag_close();
		}
	}

	/**
	 * For each CTA element in array, embed the right pattern class instantiation.
	 *
	 * @since 0.1.0
	 *
	 * @param string $type String describing the kind of block we're building.
	 *
	 * @TODO proper Error notifications.
	 **/
	protected function build_block_elements( $type ) {

		// Check for CTA elements.
		foreach ( $this->input as $e ) {
			// Loop through elements.
			$layout = str_replace($type, '', $e['acf_fc_layout'] );
			switch ( $layout ) {

				case '_block_graphic':
					$image_mods = array();
					$image = new ImageSVG( $e[ $type . '_block_graphic_select' ],$this->element, $image_mods );
					$this->output .= $image->embed();
					break;

				case '_block_paragraph':
					$paragraph = new Paragraph( $e[ $type . '_block_paragraph_text'], $this->element );
					$this->output .= $paragraph->embed();
					break;

				case '_block_header':
					$subheader = new SubHeader( $e[ $type . '_block_header_text'], $this->element );
					$this->output .= $subheader->embed();
					break;

				case '_block_button':
					$button_mods = array(
						'link_attributes' => array(),
						'data_attributes' => array(),
					);
					if ( ! empty( $e['button_link'] ) ) {
						$button_mods['link_attributes']['href'] = $e['button_link'];
					}
					if ( ! empty( $e['button_target'] ) ) {
						$button_mods['link_attributes']['target'] = $e['button_target'];
					}
					if ( ! empty( $e['button_help_text'] ) ) {
						$button_mods['link_attributes']['title'] = $e['button_help_text'];
					}
					if ( ! empty( $e['button_size'] ) ) {
						$button_mods['size'] = $e['button_size'];
					}
					$button = new Button( $e, $this->element, $button_mods );
					$this->output .= $button->embed();
					break;

				default:
					$this->output .= ( 'Unknown layout' );
					break;
			}
		}
	}
}
