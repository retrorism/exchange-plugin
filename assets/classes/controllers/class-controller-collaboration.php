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

		// Add featured image
		$this->set_featured_image();

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

		// Set sections.
		if ( ! empty( $acf_sections ) ) {
			$this->set_sections( $acf_sections );
		}

		$this->set_gallery();

		if ( $this->container->has_participants ) {
			$this->set_collaboration_stories();
		}

		if ( get_field( 'related_content_auto_select', $post_id ) ) {
			$related_content = $this->get_related_grid_content_by_tags();
		} else {
			$related_content = get_field( 'related_content', $post_id );
		}

		if ( is_array( $related_content ) && count( $related_content ) > 0 ) {
			$this->container->has_related_content = true;
			$this->set_related_grid_content( $related_content );
		}
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
				$locations[$p_id]['org_lng'] = $p_obj->org_coords['lng'];
				$locations[$p_id]['org_address'] = $p_obj->org_coords['address'];
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

	protected function set_collaboration_stories() {
		if ( ! $this->container->participants ) {
			return;
		}
		foreach ( $this->container->participants as $p_obj ) {
			$meta_query_arr[] = $p_obj->post_id;
		}
		$args = array(
			'post_type' => 'story',
			'post_status' => 'publish',
	    	'meta_query' => array(
				array(
					'key'   => 'storyteller',
					'value' => $meta_query_arr,
					'compare' => 'IN'
				),
			)
		);
		$query = new WP_Query( $args );
		if ( empty( $query->posts ) ) {
			return;
		}
		foreach( $query->posts as $post ) {
			$this->container->stories[] = $post;
		}
		if ( count( $this->container->stories ) > 0 ) {
			$this->container->has_stories = true;
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
