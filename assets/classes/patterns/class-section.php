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

	public function create_output() {
		// Check for background colour modifier and add to classes.
		if ( isset( $this->input['background_colour'] ) ) {
			$this->set_modifier_class( 'colour', $this->input['background_colour'] );
		}

		$this->output_tag_open( 'section' );
		$this->output .= '<div class="section-inner">';
		$this->build_section_header();

		if ( ! empty( $this->input['story_elements'] ) ) {
			$this->build_story_elements();
		} elseif ( ! empty( $this->input['contact_details'] ) ) {
			$this->build_contact_block();
		}
		$this->output .= '</div>';
		$this->output_tag_close( 'section' );
	}

	/**
	 * Set optional section header, if given and embed it into this section's output.
	 *
	 * @since 0.1.0
	 * @global $base Object base name is passed to embeddable child.
	 **/
	protected function build_section_header() {
		if ( ! empty( $this->input['section_header'] ) ) {
			$this->section_header = new SectionHeader( $this->input['section_header'], $this->element );
			$this->output .= $this->section_header->embed();
		}
	}

	/**
	 * For each story element in array, embed the right pattern class instantiation.
	 *
	 * @since 0.1.0
	 *
	 * @throws Exception Errors when input does not contain story elements.
	 *
	 * @TODO proper Error notifications.
	 **/
	protected function build_story_elements() {
		// Check for story elements.
		$this->story_elements = $this->input['story_elements'];
		if ( count( $this->story_elements ) > 0 ) {
			foreach ( $this->story_elements as $e ) {

				// Loop through elements.
				switch ( $e['acf_fc_layout'] ) {

					case 'image':
						$image_mods = array();
						if ( 'portrait' === $e['image_orientation']  ) {
							$image_mods['orientation'] = 'portrait';
						}
						$image = new Image( $e['image'], $this->element, $image_mods );
						$this->output .= $image->embed();
						break;

					case 'two_images':
						$duo = new ImageDuo( $e['two_images'], $this->element );
						$this->output .= $duo->embed();
						break;

					case 'paragraph':
						$paragraph = new Paragraph( $e['text'], $this->element );
						$this->output .= $paragraph->embed();
						break;

					case 'block_quote':
						$blockquote = new BlockQuote( $e, $this->element );
						$this->output .= $blockquote->embed();
						break;

					case 'pull_quote':
						$pquote_mods = array();
						if ( ! empty( $e['pquote_colour'] ) ) {
							$pquote_mods['colour'] = $e['pquote_colour'];
						}
						$pullquote = new PullQuote( $e, $this->element, $pquote_mods );
						$this->output .= $pullquote->embed();
						break;

					case 'embedded_video':
						$video = new Video( $e, $this->element );
						$this->output .= $video->embed();
						break;

					case 'interview_conversation':
						$interview = new InterviewConversation( $e['interview'], $this->element );
						$this->output .= $interview->embed();
						break;
					case 'interview_q_and_a':
						$interview = new InterviewQA( $e['interview'], $this->element );
						$this->output .= $interview->embed();
						break;
					case 'subheader':
						$subheader = new SubHeader( $e['text'], $this->element );
						$this->output .= $subheader->embed();
						break;
					case 'emphasis_block':
						$block_mods = array();
						$type = $e['block_type'];
						if ( isset( $type ) && in_array( $type, array( 'cta','post-it', true ) ) ) {
							$block_mods['type'] = $type;
							$block_mods['colour'] = $e[ $type . '_colour' ];
							$emphasis_block = new EmphasisBlock( $e [ $type . '_block_elements' ], $this->element, $block_mods );
							$this->output .= $emphasis_block->embed();
						}
						break;
					case 'map':
						$map_mods = array();
						$data = array();
						$style = $e['map_style'];
						$size = $e['map_size'];
						$center = $e['map_center'];
						$zoom_level = $e['map_zoom_level'];
						$markers = $e['map_markers'];

						// Set map style.
						if ( isset( $style ) && in_array( $style, array( 'dots','network', 'route', true ) ) ) {
							$map_mods['style'] = $style;
						}

						// Set map size.
						if ( isset( $size ) && in_array( $size, array( 'wide','square', 'small', true ) ) ) {
							$map_mods['size'] = $size;
						}
						// Set map center.
						if ( is_array( $center ) && array_key_exists( 'lat', $center ) && array_key_exists( 'lng', $center ) ) {
							$map_mods['data']['center'] = $center['lat'] . ';' . $center['lng'];
						}
						// Set zoom level.
						if ( isset( $zoom_level ) ) {
							$map_mods['data']['zoom_level'] = $zoom_level;
						}
						if ( isset( $map_mods['data']['zoom_level'] ) && isset( $map_mods['data']['center'] ) ) {
							$map = new SimpleMap( $e, $this->element, $map_mods );
							//var_dump( $map );
							$this->output .= $map->embed();
						} else
						break;
					default:
						$this->output .= '<div data-alert class="alert-box alert">';
						$this->output .= '<strong>' . __( 'Error: This layout has not yet been defined', EXCHANGE_PLUGIN ) . '</strong>';
						$this->output .= '</div>';
						break;
				}
			}
		} else {
			throw new Exception( 'Error: no input given' );
		}
	}

	/**
	 * Build contact block from user object input.
	 *
	 */
	 function build_contact_block() {
		$team_members = $this->input['contact_details'];
		if ( count( $team_members ) < 1 ) {
			return;
		}
		foreach( $team_members as $team_member ) {
			$contact_block = new ContactBlock( $team_member, $this->element );
			$this->output .= $contact_block->embed();
			break;
		}
	}
}
