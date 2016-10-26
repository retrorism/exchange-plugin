<?php
/**
 * Document Block Class
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 23/08/2016
 *
 * @package Exchange Plugin
 **/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
};

/**
 * Document Block (Post-It) Class
 *
 *  This pattern creates all Post-It-style Document blocks
 *
 * @since 0.1.0
 **/
class DocumentBlock extends EmphasisBlock {

	protected function build_block_elements() {

		// Subheader.
		$section_header_text = ! empty( $this->input['uploaded_files_header'] )
		 	? $this->input['uploaded_files_header']
			: __( 'Available downloads',EXCHANGE_PLUGIN );
		$section_header_mods = array(
			'type' => 'taped_header',
			'colour' => exchange_slug_to_hex('blue-3'),
		);
		$section_header = new SectionHeader( $section_header_text, $this->element, $section_header_mods );
		$this->output .= $section_header->embed();

		// Paragraph.
		$paragraph_text = '<ul class="documentblock__file-list dont-break-out">';
		foreach( $this->input['add_file'] as $doc ) {
			if ( ! is_numeric( $doc['file'] ) ) {
				continue;
			}
			if ( function_exists( 'acf_get_attachment' ) ) {
				$meta = acf_get_attachment( $doc['file'] );
			}
			if ( empty( $meta ) ) {
				continue;
			}
			$description = ! empty( $meta['description'] ) ? $meta['description'] : $meta['filename'];
			$paragraph_text .= '<li class="documentblock__file-list__item">';
			$paragraph_text .= '<a href="' . $meta['url'] . '" target="_blank">' . $description . '</a>';
			$paragraph_text .= '</li>';
		}

		$paragraph_text .= '</ul>';
		$paragraph = new Paragraph( $paragraph_text, $this->element );
		$this->output .= $paragraph->embed();
	}
}
