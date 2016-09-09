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
	 * Array containing map data.
	 *
	 * @since 0.1.0
	 * @access protected
	 * @var array $story_elements Elements-list.
	 **/
	protected $map_data = array();



	// public function create_output() {
	//
	// 	// Check for background colour modifier and add to classes.
	// 	if ( ! empty( $this->input['background_colour'] ) ) {
	// 		$colour = $this->input['background_colour'];
	// 		$this->set_modifier_class( 'colour', $colour );
	// 		$this->set_attribute( 'data', 'background-colour', $colour );
	// 	}
	//
	// 	$this->output_tag_open( 'section' );
	// 	if ( isset( $colour ) ) {
	// 		$this->output .= BasePattern::build_edge_svg( 'top', $colour );
	// 	}
	// 	$this->output .= '<div class="section-inner">';
	//
	// 	$this->build_section_header();
	//
	// 	if ( ! empty( $this->input['story_elements'] ) && count( $this->input['story_elements'] ) ) {
	// 		$this->story_elements = $this->input['story_elements'];
	// 		$this->build_story_elements();
	// 	}
	//
	// 	if ( ! empty( $this->input['gravity_forms'] ) ) {
	// 		$this->build_form();
	// 	}
	//
	// 	if ( 'has_map' === $this->input['section_contents'] ) {
	// 		$this->set_map_data()->build_map();
	// 	}
	//
	// 	if ( 'has_grid' === $this->input['section_contents'] ) {
	// 		$this->build_simple_grid();
	// 	}
	//
	// 	if ( ! empty( $this->input['contact_details'] ) ) {
	// 		$this->build_contact_block();
	// 	}
	//
	// 	$this->output .= '</div>';
	// 	if ( isset( $colour ) ) {
	// 		$this->output .= BasePattern::build_edge_svg( 'bottom', $colour );
	// 	}
	// 	$this->output_tag_close( 'section' );
	// }


	public function create_output() {

		if ( ! is_array( $this->input['contents'] ) ) {
			return;
		}

		$length = count( $this->input['contents'] );

		if ( 0 === $length ) {
			return;
		}

		for ( $i = 0 ; $i < $length ; $i++ ) {
			if ( ! empty( $this->input['contents'][ $i ]['acf_fc_layout'] ) ) {
				$this->set_modifier_class( 'contents_' . $i, $this->input['contents'][ $i ]['acf_fc_layout'] );
			}
		}

		// Check for background colour modifier and add to classes.
		if ( ! empty( $this->input['background_colour'] ) ) {
			$colour = $this->input['background_colour'];
			$this->set_modifier_class( 'colour', $colour );
			$this->set_modifier_class( 'style', 'coloured' );
			$this->set_attribute( 'data', 'background-colour', $colour );
		}

		// Open section with edge.
		$this->output_tag_open( 'section' );
		if ( isset( $colour ) ) {
			$this->output .= BasePattern::build_edge_svg( 'top', $colour );
		}

		$this->output .= '<div class="section-inner">';

		$this->build_section_header();

		foreach ( $this->input['contents'] as $section_contents ) {

			switch ( $section_contents['acf_fc_layout'] ) {
				case 'has_map' :
					$this->set_map_data( $section_contents )->build_map();
					break;
				case 'has_form' :
					$this->build_form( $section_contents );
					break;
				case 'has_grid' :
					$this->build_simple_grid( $section_contents );
					break;
				case 'has_story_elements' :
					$this->build_story_elements( $section_contents );
					break;
				case 'has_contact_details' :
					$this->build_contact_block( $section_contents );
					break;
				case 'has_social_icons' :
					$this->build_social_icons( $section_contents );
					break;
			}

		}

		$this->output .= '</div>';

		// Close section with edge.
		if ( isset( $colour ) ) {
			$this->output .= BasePattern::build_edge_svg( 'bottom', $colour );
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
	 * For each story element in array, embed the right pattern class instantiation.
	 *
	 * @since 0.1.0
	 *
	 * @return void;
	 *
	 * @TODO proper Error notifications.
	 **/
	protected function build_story_elements( $section_contents ) {
		// Check for story elements, return when none present.
		if ( empty( $section_contents['story_elements'] ) ) {
			return;
		}
		$this->story_elements = $section_contents['story_elements'];
		foreach ( $this->story_elements as $input ) {
			// Loop through elements.
			$type = $input['acf_fc_layout'];
			$this->output .= self::pattern_factory( $input, $type, $this->element );
		}
	}

	/**
	 * Build contact block from user object input.
	 *
	 */
	 protected function build_contact_block( $section_contents ) {
		$team_members = $section_contents['contact_details'];
		if ( count( $team_members ) < 1 ) {
			return;
		}
		foreach( $team_members as $team_member ) {
			$contact_block = new ContactBlock( $team_member, $this->element );
			$this->output .= $contact_block->embed();
		}
	}

	/**
	 * Callback for map_settings_filter
	 *
	 * @return bool that allows the filter to pick input value
	 */
	protected function map_key_filter_cb( $var ) {
		return 0 === strpos( $var, 'map_' );
	}

	/**
	 * Set up section map data from ACF input.
	 *
	 * @return the section object.
	 * @TODO don't count settings. account for new leaflet plugin settings.
	 */
	protected function set_map_data( $section_contents ) {
		$map_data = array_filter( $section_contents, array( $this, 'map_key_filter_cb'), ARRAY_FILTER_USE_KEY );
		// At least four map settings must be provided: style, size, map_center, map_zoom, map_markers / map_collabs.
		if ( count( $map_data ) > 4 ) {
			$this->map_data = $map_data;
		}
		return $this;
	}

	/**
	 * Build map from input array
	 *
	 */
	protected function build_map( $map_element = array() ) {
		$map_mods = array();
		$data = array();
		// attach section map data if element turns out to be empty, otherwise return void.
		$e = ! empty( $map_element ) ? $map_element : $this->map_data;
		if ( empty( $e ) ) {
			return;
		}
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
		if ( isset( $map_mods['data']['zoom_level'] ) ) {
			$map = new SimpleMap( $e, $this->element, $map_mods );
			$this->output .= $map->embed();
		}
	}

	private function update_form_ids() {
		$ids = array();
		$updateable = array( 'collaboration', 'participant', 'story' );
		foreach( $updateable as $type ) {
			$update_form = get_option( 'options_' . $type . '_update_form');
			if ( ! empty( $update_form ) ) {
				$ids[] = $update_form;
			}
		}
		return $ids;
	}

	private function process_token( $form_id ) {
		parse_str( $_SERVER[ 'QUERY_STRING' ] );
		$updateable = array( 'collaboration', 'participant' );
		$form_id_arr = $this->update_form_ids();
		if ( empty( $form_id ) ) {
			return;
		}
		if ( ! in_array( strval( $form_id ), $form_id_arr, true ) ) {
			return;
		}
		if ( isset( $update_id )
			&& isset( $update_token )
			&& is_numeric( $update_id )
			&& in_array( get_post_type( $update_id ), $updateable  ) ) {
				$exchange = BaseController::exchange_factory( $update_id );
		}
		if ( ! $exchange instanceof Exchange ) {
			return;
		}
		switch( $exchange->type ) {
			case 'collaboration':
				$programme_round_id = $exchange->programme_round->post_id;
				break;
			case 'participant':
				$exchange->controller->set_collaboration();
				if ( is_object( $exchange->collaboration ) ) {
					$programme_round_id = $exchange->collaboration->programme_round->post_id;
				}
				break;
			default:
				return;
		}
		if ( ! isset( $programme_round_id ) ) {
			return;
		}
		$programme_round = BaseController::exchange_factory( $programme_round_id );
		if ( ! is_a( $programme_round, 'Programme_Round' ) ) {
			return;
		} else {
			$pr_update_token = $programme_round->controller->get_programme_round_token();
		}
		if ( $pr_update_token ) {
			$verifications = array();
			foreach( $form_id_arr as $id ) {
				$verifications[] = sha1( $pr_update_token . $id );
			}
			if ( in_array( $update_token, $verifications ) ) {
				$update_string = ' update="' . $update_id . '" ';
				return $update_string;
			}
		}
	}

	/**
	 * Build contact block from Gravity Forms input.
	 *
	 */
	protected function build_form( $section_contents ) {
		$form = $section_contents['gravity_forms'];
		$update_string = '';
		if ( ! empty( $_SERVER[ 'QUERY_STRING' ] ) ) {
			var_dump( $_SERVER[ 'QUERY_STRING' ] );
			$processed = $this->process_token( $form['id'] );
			$update_string = ! empty( $processed ) ? $processed : '';
		}
		$this->output .= do_shortcode( '[gravityform id="' . $form['id'] . '"' . $update_string . ' title="true" description="true" ajax="true"]' );
	}


	/**
	 * Build grid from ACF layouts
	 *
	 */
	protected function build_simple_grid( $section_contents ) {
		$grid = new SimpleGrid( $section_contents['select_grid_items'], $this->element );
		$this->output .= $grid->embed();
	}

	/**
	 * Build grid from ACF layouts
	 *
	 */
	protected function build_social_icons( $section_contents ) {
		$platforms = $section_contents['platforms'];
		if ( is_array( $platforms ) && ! empty( $platforms ) ) {
			$this->output .= exchange_build_social_icons( $this->element, $platforms );
		}
	}
}
