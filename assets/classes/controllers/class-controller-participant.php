<?php
/**
 * Participant Controller
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
 * Participant Controller class
 *
 * This controller contains all Participant logic
 *
 * @since 0.1.0
 **/
class ParticipantController extends BaseController {

	/**
	 * Map participant properties.
	 *
	 * @param object $participant Newly instantiated participant class object.
	 * @param object $post Participant post type object.
	 * @return participant with added properties
	 *
	 * @throws Exception When no participant has been provided.
	 **/
	public function map_participant( $participant, $post_object ) {
		$post_id = $post_object->ID;
		if ( ! ( $post_id >= 1 ) || ! ( $post_object->post_type === 'participant') ) {
			unset( $participant );
			throw new Exception( 'This is not a valid participant' );
		}
		// Dump ACF variables.
		$acf = get_fields( $post_id );

		if ( ! empty( $post_object->post_title ) ) {
			$participant->name = $post_object->post_title;
		}

		$collaboration = CollaborationController::get_collaboration_by_participant_id( $post_id );
		if ( ! empty( $collaboration ) ) {
			$participant->collaboration = $collaboration;
		}

		return $participant;
	}
}

/**
 * Map participant properties.
 *
 * @param object $participant Newly instantiated participant class object.
 * @param object $post Participant post type object.
 * @return participant with added properties
 *
 * @throws Exception When no participant has been provided.
 **/
