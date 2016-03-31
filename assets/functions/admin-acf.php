<?php
/**
 * Admin functions connected to ACF functionality / fields
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 01/01/2016
 *
 * @package Exchange Plugin
 **/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
};

// Add_action( 'save_post_participant', 'save_post_participant_meta', 10, 3 );
// Vervangen door onderststaande functie.
add_action( 'acf/save_post', 'save_post_with_each_acf_update', 20 );

/**
 * Save post metadata when a post is saved.
 *
 * @param int  $post_id The post ID.
 * @param post $post The post object.
 * @param bool $update Whether this is an existing post being updated or not.
 *
 * @TODO Make ACF Field locations into constants.
 */
function save_post_participant_meta( $post_id, $post, $update ) {

	// $location = get_field('orginsation_location', $post_id );
	// $location = get_field('organisation_city', $post_id );
	// Specific field value.
	$location =  $_POST['acf']['field_56b9ba1fceb87']; // Organisation_city

	if ( isset( $location ) ) {
		// Add these location, note the last argument is true.
		$term_taxonomy_ids = wp_set_object_terms( $post_id, $location, 'location', false );

	}
}

function save_post_with_each_acf_update( $post_id ) {

	$post_type = get_post_type( $post_id );
	if ( 'participant' === $post_type ) {
		$location = get_field( 'organisation_city', $post_id );
		if ( isset( $location ) ) {
			// Add these location, note the last argument is true.
			$term_taxonomy_ids = wp_set_object_terms( $post_id, $location, 'location', false );
		}
	} elseif ( 'story' === $post_type ) {
		$story_teller = get_field( 'story_teller', $post_id );
		$location = $story_teller->organisation_city;

		$selected = get_field( 'add_special_tags' , $post_id );
		if ( is_array( $selected ) && in_array( 'location' , $selected, true ) && isset( $location ) ) {
			// Add location, note the last argument is false.
			$term_taxonomy_ids = wp_set_object_terms( $post_id, $location, 'location', false );
		} elseif ( ! is_array( $selected ) || ! in_array( 'location' , $selected, true ) ) {
			// Remove location.
			wp_set_object_terms( $post_id, null, 'location' );
		}
	}
}
