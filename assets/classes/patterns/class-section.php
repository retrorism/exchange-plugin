<?php
/**
 * Section Class
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
 * Section pattern class.
 *
 * This class serves to build the section container, which in turn contains a
 * collection of patterns constructed with ACF.
 *
 * @since 0.1.0
 **/
class Section extends BasePattern {

	/**
	 * Optional section header given to this section.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var string $section_header Optional section header.
	 **/
	protected $section_header;

	/**
	 * List of story elements - these are the elements (content styled in patterns).
	 *
	 * @since 0.1.0
	 * @access protected
	 * @var array $story_elements Elements-list.
	 **/
	protected $story_elements;

	/**
	 * Constructor for Section Pattern class objects.
	 *
	 * At instantiation this method adds background colour modifier,
	 *
	 * @since 0.1.0
	 *
	 * @param mixed  $input Pattern content as defined in ACF input values.
	 * @param string $context Optional. String referring to pattern.
	 * @param array  $modifiers Optional. Additional modifiers that influence look and functionality.
	 **/
	public function __construct( $input, $context = '', $modifiers = array() ) {
		Parent::__construct( $input, $context, $modifiers );

		// Check for background colour modifier and add to classes.
		if ( isset( $input['background_colour'] ) ) {
			$this->set_modifier_class( 'colour', $input['background_colour'] );
		}

		$this->output_tag_open( 'section' );
		$this->build_section_header( $input );
		$this->build_story_elements( $input );
		$this->output_tag_close( 'section' );
	}

	/**
	 * Set optional section header, if given and embed it into this section's output.
	 *
	 * @since 0.1.0
	 * @global $base Object base name is passed to embeddable child.
	 *
	 * @param string $input ACF text input value.
	 **/
	protected function build_section_header( $input ) {
		if ( ! empty( $input['section_header'] ) ) {
			$this->section_header = new SectionHeader( $input['section_header'], $this->base );
			$this->output .= $this->section_header->embed();
		}
	}

	/**
	 * For each story element in array, embed the right pattern class instantiation.
	 *
	 * @since 0.1.0
	 *
	 * @param array $input List of ACF content elements for which different patterns have been selected.
	 *
	 * @throws Exception Errors when input does not contain story elements.
	 *
	 * @TODO proper Error notifications.
	 **/
	protected function build_story_elements( $input ) {
		// Check for story elements.
		if ( isset( $input['story_elements'] ) ) {
			$this->story_elements = $input['story_elements'];
			if ( count( $this->story_elements ) > 0 ) {
				foreach ( $this->story_elements as $e ) {

					// Loop through elements.
					switch ( $e['acf_fc_layout'] ) {

						case 'image':
							$image_mods = array();
							if ( 'portrait' === $e['image_orientation']  ) {
								$image_mods['orientation'] = 'portrait';
							}
							$image = new Image( $e['image'], $this->base, $image_mods );
							$this->output .= $image->embed();
							break;

						case 'two_images':
							$duo = new ImageDuo( $e['two_images'], $this->base );
							$this->output .= $duo->embed();
							break;

						case 'paragraph':
							$paragraph = new Paragraph( $e['text'], $this->base );
							$this->output .= $paragraph->embed();
							break;

						case 'block_quote':
							$blockquote = new BlockQuote( $e, $this->base );
							$this->output .= $blockquote->embed();
							break;

						case 'pull_quote':
							$pquote_mods = array();
							if ( ! empty( $e['pquote_colour'] ) ) {
								$pquote_mods['colour'] = $e['pquote_colour'];
							}
							$pullquote = new PullQuote( $e, $this->base, $pquote_mods );
							$this->output .= $pullquote->embed();
							break;

						case 'embed_video':
							$video = new Video( $e['embed_video'], $this->base );
							$this->output .= $video->embed();
							break;

						case 'interview_conversation':
							$interview = new InterviewConversation( $e['interview'], $this->base );
							$this->output .= $interview->embed();
							break;
						case 'interview_q_and_a':
							$interview = new InterviewQA( $e['interview'], $this->base );
							$this->output .= $interview->embed();
							break;
						case 'subheader':
							$subheader = new SubHeader( $e['text'], $this->base );
							$this->output .= $subheader->embed();
							break;

						case 'emphasis_block':
							$block_mods = array();
							$type = $e['block_type'];
							if ( ! empty( $type ) ) {
								$block_mods['type'] = $type;
								if ( 'cta_tandem' === $type ) {
									$input = $e['cta_elements'];
									$colour = $e['cta_colour'];
								} else {
									$input = $e['block_elements'];
									$colour = $e['post-it_colour'];
								}
								$block_mods['colour'] = $colour;
								$emphasis_block = new EmphasisBlock( $input, $this->base, $block_mods );
								$this->output .= $emphasis_block->embed();
							}
							break;

						default:
							$this->output .= '<div data-alert class="alert-box alert">';
							$this->output .= '<strong>' . __( 'Error: This layout has not yet been defined', EXCHANGE_PLUGIN ) . '</strong>';
							$this->output .= '</div>';
							break;
					}
				}
			}
		} else {
			throw new Exception( 'Error: no input given' );
		}
	}
}
