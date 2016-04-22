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
	 * List of block elements - these are the elements (content styled in patterns).
	 *
	 * @since 0.1.0
	 * @access protected
	 * @var array $block_elements Elements-list.
	 **/
	protected $block_elements;

	/**
	 * Constructor for Emphasis Blocks.
	 *
	 * @since 0.1.0
	 * @access public
	 **/
	public function __construct( $input, $context = '', $modifiers = array() ) {
		Parent::__construct( $input, $context, $modifiers );
		if ( is_array( $input ) && count( $input ) > 0 ) {
			$this->block_elements = $input;
			$this->output_tag_open();
			$type = isset( $modifiers['type'] ) ? $modifiers['type'] : 'post-it';
			$this->output .= $this->build_block_elements( $this->block_elements, $type );
			$this->output_tag_close();
		}
	}

	/**
	 * For each CTA element in array, embed the right pattern class instantiation.
	 *
	 * @since 0.1.0
	 *
	 * @param array $input List of ACF content elements for which different patterns have been selected.
	 *
	 * @TODO proper Error notifications.
	 **/
	protected function build_block_elements( $block_elements, $type ) {

		// Check for CTA elements.
		foreach ( $block_elements as $e ) {
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
