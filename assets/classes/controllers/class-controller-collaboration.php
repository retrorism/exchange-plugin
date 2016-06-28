<?php
/**
 * Collaboration Controller
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 31/03/2016
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
 * Collaboration Controller class
 *
 * This controller contains all collaboration logic
 *
 * @since 0.1.0
 **/
class CollaborationController extends BaseController {

	public static function get_collaboration_by_participant_id( $participant_id ) {
		$args = array(
			'post_type' => 'collaboration',
			'numberposts' => 1,
			'meta-query' => array(
				'key' => 'participant',
				'value' => '"' . $participant_id . '"', // Matches exaclty "123", not just 123. This prevents a match for "1234".
				'compare' => 'LIKE',
			),
		);
		if ( ! $participant_id >= 1  ) {
			throw new Exception( 'This is not a valid participant ID' );
		} else {
			$collaboration_query = new WP_Query( $args );
			if ( ! empty( $collaboration_query->posts ) ) {
				return new Collaboration( $collaboration_query->posts[0] );
			} else {
				return false;
			}
		}
	}

	public function map_collaboration_basics() {
		// Add participants.
		$this->set_participants();
		// Add participants' locations
		if ( $this->container->has_participants ) {
			$this->set_collaboration_locations();
		}

		// Add tags
		$this->set_ordered_tag_list();
	}

	public function map_full_collaboration() {
		// Store post ID in a variable for faster access.
		$post_id = $this->container->post_id;
		// Add description.
		$this->set_description();
		// Add header image.
		$this->set_header_image( $post_id, 'collaboration__header' );

		$acf_sections = get_field( 'sections', $post_id );
		$acf_related_content = get_field( 'related_content', $post_id );

		// Set related content.
		if ( is_array( $acf_related_content ) && count( $acf_related_content ) > 0 ) {
			$this->set_related_grid_content( $acf_related_content );
		}

		// Set sections.
		if ( ! empty( $acf_sections ) ) {
			$this->set_sections( $acf_sections );
		}

		// // Dump ACF variables.
		// $acf_related_content = get_field( 'related_content', $post_id );
		//
		// if ( is_array( $acf_related_content ) && count( $acf_related_content ) > 0 ) {
		// 	$this->set_related_content_grid( $collaboration, $acf_related_content );
		// }
	}

	protected function set_collaboration_locations() {
		$locations = array();
		foreach( $this->container->participants as $p_obj ) {
			$p_id = $p_obj->post_id;
			if ( ! is_a( $p_obj, 'Participant' ) ) {
				continue;
			}

			if ( !empty( $p_obj->org_name ) ) {
				$locations[$p_id]['org_name'] = $p_obj->org_name;
			}
			if ( ! empty( $p_obj->org_coords ) ) {
				$locations[$p_id]['org_lat'] = $p_obj->org_coords['lat'];
				$locations[$p_id]['org_lat'] = $p_obj->org_coords['lng'];
			}
			if ( ! empty( $p_obj->org_city ) ) {
				$locations[$p_id]['org_city'] = $p_obj->org_city;
			}
		}
		if ( count( $locations ) > 1 ) {
			$this->container->locations = $locations;
			$this->container->has_locations = true;
		}
	}

	/**
	 * Set participant IDs
	 *
	 * @return void.
	 */
	protected function set_participants() {
		$participants = get_field( 'participants', $this->container->post_id );
		if ( empty( $participants ) || ! is_array( $participants ) ) {
			return;
		}
		foreach( $participants as $participant ) {
			$participant_obj = self::exchange_factory( $participant );
			if ( $participant_obj ) {
				$this->container->participants[] = $participant_obj;
			}
		}
		if ( ! empty( $this->container->participants ) ) {
			$this->container->has_participants = true;
		}
	}

	/**
	 * Set collaboration description
	 *
	 * @return void.
	 */
	protected function set_description() {
		$description = get_field( 'description', $this->container->post_id );
		if ( empty( $description ) || ! is_string( $description ) ) {
			return;
		}
		$this->container->description = new Paragraph( $description );
		$this->container->has_description = true;
	}



}
