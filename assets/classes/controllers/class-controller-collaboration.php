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

	public function set_programme_round( $parent_id ) {
		$parent = get_post( $parent_id );
		if ( 'programme_round' === $parent->post_type ) {
			$this->container->programme_round = new Programme_Round( $parent );
		}
	}
}
