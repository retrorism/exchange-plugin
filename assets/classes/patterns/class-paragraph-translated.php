<?php
/**
 * Translated Paragraph Class
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 08/07/2016
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
 * Translated Paragraph pattern class.
 *
 * This class serves to build paragraph elements.
 *
 * @since 0.1.0
 **/
class TranslatedParagraph extends BasePattern {
	/**
	 * HTML containing one paragraph with the necessary classes.
	 *
	 * @var array Associative array with language as key, text as input,
	 */
	public $translations = array();

	/**
	 * Overwrites initial output function.
	 *
	 * @since 0.1.0
	 * @access protected
	 *
	 * @TODO move HTML output to template parts instead?
	 * @TODO escape input here?
	 **/
	protected function create_output() {
		var_dump( $this->input );
		if ( empty( $this->input['text'] ) ) {
			return;
		}
		if ( 'has_translations' === $this->modifiers['type'] ) {
			$this->set_translations();
		}
		$this->output_tag_open();
		$this->build_translation_box();
		$this->output_tag_close();
	}

	/**
	 * undocumented function summary
	 *
	 * Undocumented function long description
	 *
	 * @param type var Description
	 * @return {11:return type}
	 */
	protected function build_translation_box() {
		$translations = $this->translations;
		if ( empty( $translations ) ) {
			$this->build_paragraph();
		} else {
			$this->build_paragraph();
			foreach ( $translations as $translation ) {
				$this->build_translation( $translation );
			}
			$this->build_translation_dropdown();

		}
	}

	protected function build_paragraph() {
		$p_mods = array(
			'lang' => 'original',
			'misc' => array( 'id' => 'original' ),
		 	'classes' => array( 'show' ),
		);
		$p = new Paragraph( $this->input['text'], $this->element, $p_mods );
		$this->output .= $p->embed();
	}

	protected function get_translation_language( $translation ) {
		$lang_id = $translation['translation_language'];
		$lang_term = get_term( $lang_id, 'language');
		if ( $lang_term instanceof WP_Term ) {
			return $lang_term;
		}
	}

	protected function build_translation( $translation ) {
		$lang_term = $this->get_translation_language( $translation );
		if ( empty( $lang_term ) ) {
			return;
		}
		$p_mods = array(
			'lang' => $lang_term->slug,
			'misc' => array(
				'id' => $lang_term->slug,
			),
	 	);
		if ( $lang_term->description === 'rtl' ) {
			$p_mods['misc']['dir'] = 'rtl' ;
		}
		$p = new Paragraph( $translation['translation_text'], $this->element, $p_mods );
		$this->output .= $p->embed();
	}

	protected function build_translation_dropdown( ) {
		$original = isset( $GLOBALS['story_language'] ) ? $GLOBALS['story_language'] : 'English';
		$dropdown = '<fieldset class="translatedparagraph__translation-select-wrapper"><label class="show-for-sr">' . __( 'Available translations',EXCHANGE_PLUGIN ) . '</label>';
		$dropdown .= '<select class="translation-select">' . PHP_EOL;
		$dropdown .= '<option value="" disabled selected>' . __( 'Available translations',EXCHANGE_PLUGIN ) . '</option>';
		$dropdown .= '<option value="original">' . $original . '</option>';
		foreach ( $this->translations as $translation ) {
			$lang_term = $this->get_translation_language( $translation );
			if ( empty( $lang_term ) ) {
				continue;
			}
			$lang_name = $lang_term->name;
			$dropdown .= '<option value="' . $lang_term->slug . '">' . $lang_term->name . '</option>';
		}
		$dropdown .= '</select></fieldset>';
		$this->output .= $dropdown;
	}

	public function set_translations() {
		foreach( $this->input['translations'] as $translation ) {
			if ( empty( $translation['translation_language'] || empty( $translation['translation_text'] ) ) ) {
				return;
			} else {
				$this->translations[] = $translation;
			}
		}
	}
}
