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
	 **/
	public function map_participant_basics() {

		// Mapping / aliasing title to name.
		$this->container->name = $this->container->title;

		// Mapping organisation data.
		if ( ! current_theme_supports( 'exchange_participant_profiles' ) ) {
			$this->set_organisation_data();
		} else {
			$this->set_participant_details();
			$this->set_featured_image('participant');
		}

		if ( current_theme_supports( 'exchange_participant_types' ) ) {
			$this->set_participant_type();
		}

		// Add update token
		$this->set_participant_update_form_link();
	}

	public function set_organisation_data() {
		$post_id = $this->container->post_id;
		$org_name = get_post_meta( $post_id, 'organisation_name', true );
		$org_short_name = get_post_meta( $post_id, 'organisation_short_name', true );
		$org_coords = get_post_meta( $post_id, 'organisation_location', true );
		$org_city = get_post_meta( $post_id, 'organisation_city', true );
		$org_country = get_post_meta( $post_id, 'organisation_country', true );
		$org_description = get_post_meta( $post_id, 'organisation_description', true );
		$org_website = get_post_meta( $post_id, 'organisation_website', true );
		$p_contactme = get_post_meta( $post_id, 'participant_email', true );

		if ( !empty( $org_name ) ) {
			$this->container->org_name = $org_name;
		}
		if ( !empty( $org_short_name ) ) {
			$this->container->org_short_name = $org_short_name;
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

	public function set_participant_details() {
		$post_id = $this->container->post_id;
		$p_meta = get_post_meta( $post_id, '', true );
		if ( empty( $p_meta ) ) {
			return;
		}
		foreach( $p_meta as $key => $value ) {
			if ( 0 !== strpos( $key, 'participant_' ) ) {
				continue;
			}
			if ( 'participant_email' === $key ) {
				$this->container->set_contactme( $value[0] );
			} else {
				$this->container->details[ $key ] = $value[0];
			}
		}
	}

	protected function set_participant_type() {
		if ( ! current_theme_supports( 'exchange_participant_types' ) ) {
			return;
		}
		$post_id = $this->container->post_id;
		$terms = get_the_terms( $post_id, 'participant_type' );
		if ( ! empty( $terms ) && $terms[0] instanceof WP_Term ) {
			$this->container->participant_type = $terms[0];
		}
	}

	public function set_collaboration() {
		$post_id = $this->container->post_id;
		$collaboration = CollaborationController::get_collaboration_by_participant_id( $post_id );
		if ( ! empty( $collaboration ) ) {
			$this->container->collaboration = $collaboration;
		}
	}

	protected function set_participant_update_form_link() {
		$post_id = $this->container->post_id;
		$link = get_post_meta( $post_id, 'participant_update_form_link', true );
		$this->container->set_update_form_link( $link );
	}
}
