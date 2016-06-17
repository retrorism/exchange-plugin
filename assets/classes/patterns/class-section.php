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
			$colour = $this->input['background_colour'];
			$this->set_modifier_class( 'colour', $colour );
		}

		$this->output_tag_open( 'section' );
		if ( isset( $colour ) ) {
			$this->output .= $this->build_edge_svg( 'top', $colour );
		}
		$this->output .= '<div class="section-inner">';

		$this->build_section_header();

		if ( ! empty( $this->input['story_elements'] ) && count( $this->input['story_elements'] ) ) {
			$this->story_elements = $this->input['story_elements'];
			$this->build_story_elements();
		}

		if ( ! empty( $this->input['gravity_forms'] ) ) {
			$this->build_form();
		}

		if ( ! empty( $this->input['contact_details'] ) ) {
			$this->build_contact_block();
		}
		$this->output .= '</div>';
		if ( isset( $colour ) ) {
			$this->output .= $this->build_edge_svg( 'bottom', $colour );
		}
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
	 * Set optional section header, if given and embed it into this section's output.
	 *
	 * @since 0.1.0
	 * @global $base Object base name is passed to embeddable child.
	 **/
	protected function build_edge_svg( $pos, $colour ) {
		return '<svg class="section__edge--' . $pos . '" viewBox="0 0 850 20" preserveAspectRatio="none"><desc>Created with Snap</desc><defs></defs><polyline points="0 20,0 20,2 10,9 8,6 8,9 10,12 9,18 9,20 8,20 10,24 8,26 9,29 8,33 8,35 10,35 9,41 8,45 10,53 8,59 8,62 9,62 9,69 10,69 10,75 9,79 10,86 8,91 8,94 10,101 10,107 10,109 9,117 8,125 10,125 9,126 8,128 8,133 8,139 8,144 8,146 9,152 8,160 8,164 10,170 8,170 9,173 8,181 9,183 10,184 8,191 10,196 9,199 10,199 10,204 10,206 8,206 10,208 8,208 9,214 9,214 8,215 8,219 10,219 8,225 8,232 9,232 10,233 9,233 8,241 8,241 9,243 10,247 8,249 10,257 8,264 10,265 9,267 8,267 9,274 8,274 8,277 9,279 10,283 8,288 8,294 9,298 10,302 8,305 10,308 8,312 10,314 8,320 10,328 9,330 9,330 9,334 10,338 9,343 8,347 10,351 8,352 10,356 9,356 10,361 10,361 8,364 8,365 10,371 9,371 10,371 8,375 9,375 10,378 9,384 10,391 10,392 10,394 9,401 8,409 10,409 9,417 10,425 9,429 9,436 8,441 8,446 9,452 10,455 10,459 8,464 9,470 9,475 8,480 8,481 8,483 9,487 9,491 10,493 10,496 8,498 10,504 10,512 8,513 9,521 9,522 10,530 8,531 9,534 8,541 8,548 9,555 10,560 10,566 9,574 9,582 9,585 9,591 10,591 10,592 8,594 10,601 8,605 9,605 9,607 8,614 10,622 9,629 8,633 8,637 8,643 10,651 8,659 10,661 9,663 9,667 8,668 8,671 10,678 8,685 10,688 9,691 8,692 10,693 8,694 10,702 10,709 8,714 8,718 8,718 10,718 9,726 8,732 10,732 9,740 10,740 8,745 10,751 10,758 10,766 9,769 9,770 10,773 8,781 8,785 9,793 9,800 10,807 8,812 10,814 8,821 9,828 9,833 8,841 8,848 10,850 20,849" fill="' . $colour . '"></polyline></svg>';
	}

	/**
	 * For each story element in array, embed the right pattern class instantiation.
	 *
	 * @since 0.1.0
	 *
	 * @return void;
	 *
	 * @TODO proper Error notifications.
	 **/
	protected function build_story_elements() {
		// Check for story elements, return when none present.
		if ( empty( $this->story_elements ) ) {
			return;
		}
		foreach ( $this->story_elements as $e ) {

			// Loop through elements.
			switch ( $e['acf_fc_layout'] ) {

				case 'image':
					$image_mods = array();
					$focus_points = exchange_get_focus_points( $e['image'] );
					$image_mods['data'] = array( 'img_id' => $e['image']['id'] );
					if ( ! empty( $focus_points ) ) {
						$image_mods['data'] = array_merge( $image_mods['data'], $focus_points );
						$image_mods['classes'] = array('focus');
					}
					if ( 'portrait' === $e['image_orientation']  ) {
						$image_mods['orientation'] = 'portrait';
					}
					$image = new Image( $e['image'], $this->element, $image_mods );
					if ( is_object( $image ) && is_a( $image, 'Image' ) ) {
						$this->output .= $image->embed();
					}
					break;

				case 'two_images':
					$duo = new ImageDuo( $e, $this->element );
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
				case 'section_header':
					$header_mods = array();
					$colour = $e['tape_colour'];
					$type = $e['type'];
					if ( ! empty( $colour ) ) {
						$header_mods['colour'] = $e['tape_colour'];
						$header_mods['data'] = array( 'tape_colour' => $colour );
					}
					if ( ! empty( $type ) ) {
						$header_mods['type'] = $e['type'];
					}
					$subheader = new SectionHeader( $e['text'], $this->element, $header_mods );
					$this->output .= $subheader->embed();
					break;
				case 'emphasis_block':
					$block_mods = array();
					$type = $e['block_type'];
					$align = $e['block_alignment'];
					$block_elements = $e[ $type . '_block_elements' ];
					if (  empty( $type ) || ! count( $block_elements ) ) {
						break;
					}
					switch ( $align ) {
						case 'left':
						case 'right':
							$block_mods['classes'] = array( 'floated' );
						case 'full':
							$block_mods['align'] = $align;
						default:
							break;
					}
					$block_mods['type'] = $type;
					$block_mods['colour'] = $e[ $type . '_colour' ];
					$block_mods['data'] = array( 'element_count' => count( $block_elements ) );
					$emphasis_block = new EmphasisBlock( $block_elements, $this->element, $block_mods );
					$this->output .= $emphasis_block->embed();
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

	/**
	 * Build contact block from Gravity Forms input.
	 *
	 */
	function build_form() {
		$form = $this->input['gravity_forms'];
		$this->output .= do_shortcode('[gravityform id="' . $form['id'] . '" title="true" description="true" ajax="true"]');
	}
}
