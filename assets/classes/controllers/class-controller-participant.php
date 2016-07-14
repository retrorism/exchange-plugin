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
	 *
	 * @throws Exception When no participant has been provided.
	 **/
	public function map_participant_basics() {

		// Mapping / aliasing title to name.
		$this->container->name = $this->container->title;

		// Mapping organisation data.
		$this->set_organisation_data();
	}

	public function set_organisation_data() {
		$post_id = $this->container->post_id;
		$org_name = get_field( 'organisation_name', $post_id );
		$org_coords = get_field( 'organisation_location', $post_id );
		$org_city = get_field( 'organisation_city', $post_id );
		$org_country = get_field( 'organisation_country', $post_id );
		$org_description = get_field( 'organisation_description', $post_id );
		$org_website = get_field( 'organisation_website', $post_id );
		$p_contactme = get_field( 'participant_email', $post_id );

		if ( !empty( $org_name ) ) {
			$this->container->org_name = $org_name;
		}
		if ( ! empty( $org_coords['address'] ) || ( ! empty( $org_coords['lat'] ) && ! empty( $org_coords['lng'] ) ) ) {
			$this->container->org_coords = $org_coords;
		}
		if ( ! empty( $org_city ) ) {
			$this->container->org_city = $org_city;
		}
		if ( ! empty( $org_country ) ) {
			$this->container->org_country = $org_country;
		}
		if ( ! empty( $org_description ) ) {
			$this->container->org_description = $org_description;
		}
		if ( ! empty( $org_website ) ) {
			$this->container->org_website = $org_website;
		}
		if ( !empty( $p_contactme ) ) {
			$this->container->set_contactme( $p_contactme );
		}
	}

	public function set_collaboration() {
		$collaboration = CollaborationController::get_collaboration_by_participant_id( $post_id );
		if ( ! empty( $collaboration ) ) {
			$this->container->collaboration = $collaboration;
		}
	}
}
