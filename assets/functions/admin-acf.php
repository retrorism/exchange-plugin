<?php
/**
 * Admin functions connected to ACF functionality / fields
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 08/03/2016
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
//add_action( 'acf/save_post', 'save_post_with_each_acf_update', 20 );
add_action( 'acf/input/admin_head', 'exchange_change_acf_color_picker' );
//add_action( 'acf/validate_value/key=field_570b7d8c359c2', 'exchange_save_location_tax_to_story', 10, 4);


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
	$location = $_POST['acf']['field_56b9ba1fceb87']; // Organisation_city

	if ( isset( $location ) ) {
		// Add these location, note the last argument is true.
		$term_taxonomy_ids = wp_set_object_terms( $post_id, $location, 'location', false );

	}
}

// @TODO 'Unhack the -locations- tax field which refuses to update the terms.
function exchange_save_location_tax_to_story( $valid, $value, $field, $input ) {
	if ( $_POST['post_type'] == 'story' ) {
		$terms = array();
		foreach( $value as $term ) {
			$term_object = get_term_by('ID', intval($term), 'location');
			if ( is_object( $term_object ) && get_class( $term_object) === 'WP_Term' ) {
				$terms[] = $term_object->slug;
			}
		}
		if ( count( $terms ) > 0 ) {
			$val = wp_set_object_terms ( $_POST['post_ID'], $terms, 'location', false );
		}
	}
	return true;
}

// @TODO Add selector for storyteller / relationship with collab.


// function save_post_with_each_acf_update( $post_id ) {
//
// 	$post_type = get_post_type( $post_id );
// 	if ( 'participant' === $post_type ) {
// 		$location = get_field( 'organisation_city', $post_id );
// 		if ( isset( $location ) ) {
// 			// Add these location, note the last argument is true.
// 			$term_taxonomy_ids = wp_set_object_terms( $post_id, $location, 'location', false );
// 		}
// 	} elseif ( 'story' === $post_type ) {
// 		$story_teller = get_field( 'story_teller', $post_id );
// 		$location = $story_teller->organisation_city;
//
// 		$selected = get_field( 'add_special_tags' , $post_id );
// 		if ( is_array( $selected ) && in_array( 'location' , $selected, true ) && isset( $location ) ) {
// 			// Add location, note the last argument is false.
// 			$term_taxonomy_ids = wp_set_object_terms( $post_id, $location, 'location', false );
// 		} elseif ( ! is_array( $selected ) || ! in_array( 'location' , $selected, true ) ) {
// 			// Remove location.
// 			wp_set_object_terms( $post_id, null, 'location' );
// 		}
// 	}
// }

function exchange_change_acf_color_picker() {

	$client_colors_bg_array = array();
	$client_colors_accents_array = array();
	$client_colors_boxes_array = array();

	foreach ( $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['COLOUR_PICKERS']['bg'] as $value ) {
		$client_colors_bg_array[] = '#'.$value;
	}

	foreach ( $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['COLOUR_PICKERS']['accents'] as $value ) {
		$client_colors_accents_array[] = '#'.$value;
	}

	foreach ( $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['COLOUR_PICKERS']['boxes'] as $value ) {
		$client_colors_boxes_array[] = '#'.$value;
	}

	$client_colors_bg_jquery = json_encode( $client_colors_bg_array );
	$client_colors_accents_jquery = json_encode( $client_colors_accents_array );
	$client_colors_boxes_jquery = json_encode( $client_colors_boxes_array );

	echo "<script>
		(function($){
		acf.add_action('ready append', function() {
		  acf.get_fields({ type : 'color_picker'}).each(function() {
			var acfpalette = " . $client_colors_bg_jquery . ";
			if ( $(this).find('.wp-color-picker').parents('*[data-name=\"pquote_colour\"]').length > 0 ) {
			  var acfpalette = ". $client_colors_bg_jquery . ";
			}
			if ( $(this).find('.wp-color-picker').parents('*[data-name=\"cta_colour\"]').length > 0 ) {
			  var acfpalette = ". $client_colors_boxes_jquery .";
			}
			if ( $(this).find('.wp-color-picker').parents('*[data-name=\"post-it_colour\"]').length > 0 ) {
			  var acfpalette = " . $client_colors_bg_jquery . ";
			  console.log( $(this) );
			}
			if ( $(this).find('.wp-color-picker').parents('*[data-name=\"tape_colour\"]').length > 0 ) {
			  var acfpalette = ". $client_colors_accents_jquery .";
			}
			if ( $(this).find('.wp-color-picker').parents('*[data-name=\"box_colour\"]').length > 0 ) {
			  var acfpalette = ". $client_colors_boxes_jquery . ";
			}
			$(this).iris({
			  color: $(this).find('.wp-color-picker').val(),
			  mode: 'hsv',
			  palettes: acfpalette,
			  change: function(event, ui) {
				$(this).find('.wp-color-result').css('background-color', ui.color.toString());
				$(this).find('.wp-color-picker').val(ui.color.toString());
			  }
			});
		  });
		});
	})(jQuery);
	</script>";
}
