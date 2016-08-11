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
add_action( 'admin_enqueue_scripts', 'exchange_plugin_admin_scripts' );


function exchange_plugin_admin_scripts() {
	wp_enqueue_script( 'admin-js', plugins_url( EXCHANGE_PLUGIN . '/assets/js/exchange_admin.js' ), array( 'jquery' ), '', false );
};

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

if( function_exists('acf_add_options_page') ) {

	$forms_page = acf_add_options_sub_page(array(
		'page_title' 	=> 'Page Settings',
		'menu_title' 	=> 'Pages',
		'parent_slug' 	=> EXCHANGE_PLUGIN,
		'capability' 	=> 'edit_posts',
		'redirect' 	=> false
	));

	$stories_page = acf_add_options_sub_page(array(
		'page_title' 	=> 'Story Overview Settings',
		'menu_title' 	=> 'Stories Settings',
		'parent_slug' 	=> EXCHANGE_PLUGIN,
		'capability' 	=> 'edit_posts',
		'redirect' 	=> false
	));

}


add_action( 'save_post', 'exchange_add_update_form_link', 10, 3 );

function exchange_add_update_form_link( $post_ID, $post_obj ) {
	$update = false;
	$type = $post_obj->post_type;

	// Gather token / form info for token verification.
	$form_id = get_option('options_' . $type . '_update_form');
	$form_link = get_field( $type . '_update_form_link', $post_ID );
	if ( 'participant' === $type ) {
		$field = 'field_57a0a3eff2d3c';
		$coll = CollaborationController::get_collaboration_by_participant_id( $post_ID );
	} elseif ( 'collaboration' === $type ) {
		$field = 'field_57a0a397c1cd6';
		$coll = BaseController::exchange_factory( $post_ID );
	}
	if ( is_object( $coll ) && is_a( $coll->programme_round, 'Programme_Round' ) ) {
		$pr_token = $coll->programme_round->controller->get_programme_round_token();
	}
	if ( ! isset( $pr_token ) ) {
		return;
	}

	// See if anything needs to be changed.
	$form_token = sha1( $pr_token . $form_id );
	if ( ! empty( $form_link ) ) {
		$parts = parse_url( $form_link );
		parse_str( $parts[ 'query' ], $query );
		if ( $query['update_token'] !== $form_token || $query['update_id'] !== $post_ID ) {
			$update = true;
		} else {
			return;
		}
	}
	// Update or save the link to the post.
	$page_id = get_option('options_' . $type . '_update_form_page');
	$page_url = get_permalink( $page_id );
	$link = $page_url . '?update_token=' . $form_token . '&update_id=' . $post_ID;
	if ( $update ) {
		update_post_meta( $post_ID, $type . '_update_form_link', $link );
	} else {
		update_field( $field, $link, $post_ID );
	}
}
