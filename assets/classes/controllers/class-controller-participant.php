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
 * Collaboration Controller class
 *
 * This controller contains all collaboration logic
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
	public function map_participant( $participant, $post ) {

		$participant_id = $post->ID;

		if ( ! ( $participant_id >= 1 ) || 'participant' !== $post->post_type ) {
			unset( $participant );
			throw new Exception( 'This is not a valid participant' );
		}

		// Dump ACF variables.
		$acf = get_fields( $post_id );

		if ( ! empty( $post->post_title ) ) {
			$participant->name = $post->post_title;
		}

		if ( ! empty( $acf['collaboration'] ) ) {
			$participant->collaboration = $acf['collaboration'];
		}

		return $participant;
	}
}
