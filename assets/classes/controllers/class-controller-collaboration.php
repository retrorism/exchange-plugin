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

	public function map_collaboration_basics( $post ) {
		if ( $post->post_parent >= 1 ) {
			$this->set_programme_round( $post->post_parent );
		}

		// Add participants.
		$this->set_participants();

		// Add participants' locations
		if ( $this->container->has_participants ) {
			$this->set_collaboration_locations();
		}
	}

	public function map_full_collaboration() {
		$post_id = $this->container->post_id;

		// // Dump ACF variables.
		// $acf_related_content = get_field( 'related_content', $post_id );
		//
		// if ( is_array( $acf_related_content ) && count( $acf_related_content ) > 0 ) {
		// 	$this->set_related_content_grid( $collaboration, $acf_related_content );
		// }
	}

	protected function set_collaboration_locations() {
		$locations = array();
		foreach( $this->container->participants as $p_id ) {
			$org_name = get_field( 'organisation_name', $p_id );
			$org_coords = get_field( 'organisation_location', $p_id );
			$org_city = get_field( 'organisation_city', $p_id );
			if ( !empty( $org_name ) ) {
				$locations[$p_id]['org_name'] = $org_name;
			}
			if ( ! empty( $org_coords ) ) {
				$locations[$p_id]['org_lat'] = $org_coords['lat'];
				$locations[$p_id]['org_lat'] = $org_coords['lng'];
			}
			if ( ! empty( $org_city ) ) {
				$locations[$p_id]['org_city'] = $org_city;
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
		if ( is_array( $participants ) && count( $participants ) > 0 ) {
			foreach( $participants as $participant ) {
				if ( exchange_post_exists( $participant ) ) {
					$this->container->participants[] = $participant;
				}
			}
			if ( count( $this->container->participants ) > 0 ) {
				$this->container->has_participants = true;
			}
		}
	}

	public function set_programme_round( $parent_id ) {
		$parent = get_post( $parent_id );
		if ( 'programme_round' === $parent->post_type ) {
			$this->container->programme_round = new Programme_Round( $parent );
		}
	}
}
