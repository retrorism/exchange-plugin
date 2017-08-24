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
			$equalizer = '';
			if ( $this->context === 'griditem' ) {
				$equalizer .= 'data-equalizer-watch';
			}
			$this->output_tag_open();
			$this->output .= '<div class="emphasisblock-inner" ' . $equalizer . '>';
			if ( isset( $this->modifiers['type'] ) && 'post-it' === $this->modifiers['type'] ) {
				$this->output .= '<div class="post-it-bg"></div>';
			}
			$this->output .= $this->build_block_elements();
			$this->output .= '</div>';
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
	protected function build_block_elements() {
		// Check for CTA elements.
		foreach ( $this->input as $e ) {

			switch ( $e['acf_fc_layout'] ) {

				case 'block_graphic':
					$image_arr = $e['block_graphic_select'];
					if ( empty( $image_arr ) || empty( $image_arr['mime_type'] ) ) {
						break;
					}
					$image_mods = array();
					if ( 'image/svg+xml' === $image_arr['mime_type'] ) {
						$image = new ImageSVG( $image_arr, $this->element, $image_mods );
					} else {
						$image = new Image( $image_arr, $this->element, $image_mods );
					}
					$this->output .= $image->embed();
					break;

				case 'block_logo':
					if ( ! empty( $e['block_programme'] ) ) {					
						$colour = isset( $this->modifiers['colour'] ) ? $this->modifiers['colour'] : 'default';
						$image_mods = array(
							'background-colour' => $colour,
						);
						$image = new ImageSVG( $e['block_programme'],$this->element, $image_mods );
						$this->output .= $image->embed();
					}
					break;

				case 'block_paragraph':
					if ( ! empty( $e['block_paragraph_text'] ) ) {
						$paragraph = new Paragraph( $e['block_paragraph_text'], $this->element );
						$this->output .= $paragraph->embed();
					}
					break;

				case 'block_header':
					if ( ! empty( $e['block_header_text'] ) ) {
						$subheader = new SubHeader( $e['block_header_text'], $this->element );
						$this->output .= $subheader->embed();
					}
					break;

				case 'block_button':
					$button_mods = array(
						'link' => array(),
						'data' => array(),
					);
					if ( ! empty( $e['button_link'] ) ) {
						$button_mods['link']['href'] = $e['button_link'];
					}
					if ( ! empty( $e['button_target'] ) ) {
						$button_mods['link']['target'] = $e['button_target'];
					}
					if ( ! empty( $e['button_help_text'] ) ) {
						$button_mods['link']['title'] = $e['button_help_text'];
					}
					if ( ! empty( $e['button_size'] ) ) {
						$button_mods['size'] = $e['button_size'];
					}
					$button = new Button( $e, $this->element, $button_mods );
					$this->output .= $button->embed();
					break;

				default:
					break;
			}
		}
	}
}
