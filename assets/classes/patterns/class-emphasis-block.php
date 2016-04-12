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
			if ( 'cta_tandem' === $modifiers['type'] ) {
				$this->output .= $this->build_cta_elements( $this->block_elements );
			} elseif ( 'other' === $modifiers['type'] ) {
				$this->output .= $this->build_block_elements( $this->block_elements );
			}
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
	protected function build_cta_elements( $input ) {
		// Check for CTA elements.
		foreach ( $input as $e ) {
			// Loop through elements.
			switch ( $e['acf_fc_layout'] ) {

				case 'block_graphic':
					$image_mods = array();
					$image = new ImageSVG( $e['select_block_graphic'],$this->base, $image_mods );
					$this->output .= $image->embed();
					break;

				case 'block_paragraph':
					$paragraph = new Paragraph( $e['block_paragraph_text'], $this->base );
					$this->output .= $paragraph->embed();
					break;

				case 'block_header':
					$subheader = new SubHeader( $e['block_header_text'], $this->base );
					$this->output .= $subheader->embed();
					break;

				case 'block_button':
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
					$button = new Button( $e, $this->base, $button_mods );
					$this->output .= $button->embed();
					break;

				default:
					$this->output .= ( 'Unknown layout' );
					break;
			}
		}
	}

	/**
	 * For each block element in array, embed the right pattern class instantiation.
	 *
	 * @since 0.1.0
	 *
	 * @param array $input List of ACF content elements for which different patterns have been selected.
	 *
	 * @TODO proper Error notifications.
	 **/
	protected function build_block_elements( $input ) {
		// Check for Block elements.
		foreach ( $input as $e ) {
			// Loop through elements.
			switch ( $e['acf_fc_layout'] ) {

				case 'block_header':
					$subheader = new SubHeader( $e['block_header'], $this->base );
					$this->output .= $subheader->embed();
					break;

				case 'block_graphic':
					$image_mods = array();
					print_r( $e['select_block_graphic'] );
					$image = new ImageSVG( $e['select_block_graphic'],$this->base, $image_mods );
					$this->output .= $image->embed();
					break;

				case 'block_rich_text':
					$list = new Paragraph( $e['block_rich_text_content'],$this->base );
					$this->output .= $list->embed();
					break;

				default:
					$this->output .= ( 'Unknown layout' );
					break;
			}
		}
	}
}
