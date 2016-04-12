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

	public function map_collaboration( $collaboration, $postobj ) {
		$post_id = $postobj->ID;
		if ( ! ( $post_id >= 1 ) || ! ( 'collaboration' === $postobj->post_type  ) ) {
			unset( $collaboration );
			throw new Exception( 'This is not a valid collaboration' );
		}
		// Dump ACF variables.
		$acf = get_fields( $post_id );
		$link = get_permalink( $post_id );

		if ( ! empty( $postobj->post_title ) ) {
			$collaboration->title = $postobj->post_title;
		}

		if ( $postobj->post_parent >= 1 ) {
			$this->set_programme_round( $collaboration, $postobj->post_parent );
		}

		if ( !empty( $link ) ) {
			$collaboration->link = $link;
		}
	}

	public function set_programme_round( $collaboration, $parent_id ) {
		$context = get_post( $parent_id );
		if ( 'programme_round' === $context->post_type ) {
			$collaboration->programme_round = new ProgrammeRound( $context );
		}
	}
}
