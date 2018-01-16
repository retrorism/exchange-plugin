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

if ( function_exists('acf_add_options_page' ) ) {

	$page_settings = acf_add_options_sub_page(array(
		'page_title' 	=> 'Page Settings',
		'menu_title' 	=> 'Pages',
		'parent_slug' 	=> EXCHANGE_PLUGIN,
		'capability' 	=> 'edit_posts',
		'redirect' 	    => false,
	));
}

// Hook into collaboration / participant form update to change the update links.
add_action( 'update_option_options_collaboration_update_form', 'exchange_update_collaboration_form_links_on_change', 10, 2);
add_action( 'update_option_options_participant_update_form', 'exchange_update_participant_form_links_on_change', 10, 2);

function exchange_update_collaboration_form_links_on_change( $old_option_value, $new_option_value ) {
	if ( $old_option_value === $new_option_value ) {
		return;
	}
	$args = array(
		'post_type' => 'collaboration',
		'posts_per_page' => -1,
	);
	$collaborations_query = new WP_Query( $args );
	if ( $collaborations_query->have_posts() ) {
		foreach ( $collaborations_query->posts as $collaboration ) {
			exchange_add_update_form_link( $collaboration->ID, $collaboration );
		}
	}
	wp_reset_query();
}

function exchange_update_participant_form_links_on_change( $old_option_value, $new_option_value ) {
	if ( $old_option_value === $new_option_value ) {
		return;
	}
	$args = array(
		'post_type' => 'participant',
		'posts_per_page' => -1,
	);
	$participants_query = new WP_Query( $args );
	if ( $participants_query->have_posts() ) {
		foreach( $participants_query->posts as $participant ) {
			exchange_add_update_form_link( $participant->ID, $participant );
		}
	}
	wp_reset_query();
}

add_action( 'save_post', 'exchange_add_update_form_link', 10, 3 );

function exchange_add_update_form_link( $post_id, $post_obj ) {
	$type = $post_obj->post_type;
	if ( ! in_array( $type, $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['POST_TYPES']['available-for-form-updates'] ) ) {
		return;
	}
	$update = false;
	// Gather token / form info for token verification.
	$form_id = get_option('options_' . $type . '_update_form');
	$form_link = get_field( $type . '_update_form_link', $post_id );
	$field = $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['ACF']['fields'][ $type . '-update-link'];
	if ( 'participant' === $type ) {
		$coll = CollaborationController::get_collaboration_by_participant_id( $post_id );
	} elseif ( 'collaboration' === $type ) {
		$coll = BaseController::exchange_factory( $post_id );
	}
	if ( ! $coll instanceof Collaboration ) {
		return;
	}
	if ( $coll->programme_round instanceof Programme_Round ) {
		$pr_token = $coll->programme_round->controller->get_programme_round_token();
	}
	if ( ! isset( $pr_token ) ) {
		return;
	}
	// See if anything needs to be changed.
	$form_token = sha1( $pr_token . $form_id . $post_id );
	if ( ! empty( $form_link ) ) {
		$parts = parse_url( $form_link );
		parse_str( $parts[ 'query' ], $query );
		if ( $query['update_token'] !== $form_token || $query['update_id'] !== $post_id ) {
			$update = true;
		} else {
			return;
		}
	}
	// Update or save the link to the post.
	$update_page_id = get_option('options_' . $type . '_update_form_page');
	$update_page_url = get_permalink( $update_page_id );
	$link = $update_page_url . '?update_token=' . $form_token . '&update_id=' . $post_id;
	if ( $update ) {
		update_post_meta( $post_id, $type . '_update_form_link', $link );
	} else {
		update_field( $field, $link, $post_id );
	}
}

add_action( 'save_post', 'exchange_update_location_transients_on_save', 11, 2 );

function exchange_update_location_transients_on_save( $post_id, $post_obj ) {
	$type = $post_obj->post_type;
	if ( $type !== 'participant' ) {
		return;
	}
	$stored_locations = get_transient('collaboration_locations');
	
	if ( empty( $stored_locations ) ) {
		$stored_locations = array();
	}
	if ( ! is_array( $stored_locations ) ) {
		return;
	}
	$coll = CollaborationController::get_collaboration_by_participant_id( $post_id, 'transient' );
	if ( ! $coll instanceof Collaboration ) {
		return;
	}
	if ( ! $coll->has_locations ) {
		return;
	}
	$stored_locations[ $coll->post_id ] = $coll->locations;
	set_transient( 'collaboration_locations', $stored_locations, 365 * 24 * HOUR_IN_SECONDS );
}


function exchange_iterate_filter( $input, $post_id, $indices, $last_iter = '', $last_iter_type = 'story_elements' ) {

    foreach ( $input as $key => $val ) {
		if ( is_array( $val ) ) {
			$el_type = '_';
			// Let's find out in what kind of array we are currently.
			if ( 'field_56cf1e8d69ac5' === $key ) {
				$last_iter = 'section_index';
				// Depth 1: field_56cf1e8d69ac5 = section
			} elseif ( 'field_57ad96b367287' === $key ) {
				// Depth 2: field_57ad96b367287 = content
				$last_iter = 'content_index';
			} elseif ( 'field_57ad96b367288' === $key ) {
				// Depth 3: field_57ad96b367288 = story_elements
				$last_iter = 'element_index';
				$last_iter_type = 'story_elements';
			} elseif ( 'field_574c142388c64' === $key ) {
				// Depth 3: field_57ad98f1884a4 = grid_elements
				// Depth 4 = interviews, emphasisblocks, etc.
				$last_iter = 'element_index';
				$last_iter_type = 'select_grid_items';
			} elseif ( is_int( $key ) ) {
				$indices[ $last_iter ] = $key;
			}

			$input[ $key ] = exchange_iterate_filter( $val, $post_id, $indices, $last_iter, $last_iter_type );
		}

		if ( empty( $val ) ) {
			// var_dump( $key );
			// var_dump( $val );
			// Only iterate into previous version when the new value is empty,
			// in order to see if there's an old value that needs to be overwritten.
			$field = get_field_object( $key, $post_id, false, false );
			if ( ! empty( $field ) && isset( $field['name'] ) ) {
				if ( array_key_exists( $key, $_POST['acf'] ) ) {
					// Non-prefixed ACF post_meta, like taxonomies, editorial info, cta info.
					$meta_name = $field['name'];
				} elseif ( $indices['element_index'] > -1 ) {
					// echo 'this is a deep lookup';
					// var_dump( $indices );
					// var_dump( $el_type );
					// ACF post_meta with a 'sections_n_contents_n_[type]_elements_' prefix (story-elements and grid-elements)
					$meta_name = 'sections_' . $indices['section_index'] . '_contents_' . $indices['content_index']
						. '_' . $last_iter_type . '_' . $indices['element_index'] . '_' . $field['name'];
				} elseif ( $indices['content_index'] > -1 ) {
					// ACF post_meta with a 'sections_n_contents_n_ prefix (like map, form, contact, gridinfo or storyelementinfo )
					$meta_name = 'sections_' . $indices['section_index'] . '_contents_' . $indices['content_index'] . '_' . $field['name'];

				} elseif ( $indices['section_index'] > -1 ) {
					// ACF post_meta with a 'sections_n_ prefix (like bg colours, headerinfo and contents).
					$meta_name = 'sections_' . $indices['section_index'] . '_' . $field['name'];
				}
				if ( isset( $meta_name ) ) {
					$previous_value = get_post_meta( $post_id, $meta_name, true );
					// var_dump( $previous_value );
					// Only unset the key's value if the previous value was also empty or not set.
					if ( isset( $meta_name) && empty( $previous_value ) ) {
						// echo "yup";
						unset( $input[ $key ] );
						delete_post_meta( $post_id, $meta_name );
					}
				}
			}
		}
    }
	return $input;
}

function exchange_remove_empty_acf_meta_at_save( $post_id ) {
	// bail early if no ACF data
    if( empty( $_POST['acf'] ) ) {
        return;
    }

	// var_dump( $_POST['acf']['field_56cf1e8d69ac5'] );
	// throw new Exception("Testing {1:What are we testing?}");

	if ( ! in_array( get_post_type( $post_id ), array( 'story','page' ) ) ) {
		return;
	}
	$fields = $_POST['acf'];

	$indices = array(
		'section_index'    => -1,
		'content_index'    => -1,
		'element_index'    => -1,
		'subelement_index' => -1,
	);
	$clean_fields = exchange_iterate_filter( $fields, $post_id, $indices );
	$max_sections = count( $clean_fields['field_56cf1e8d69ac5'] );
	$all_meta_keys = array_keys( get_post_custom( $post_id ) );
	foreach ( $all_meta_keys as $k ) {
		if ( preg_match( '/(_*sections_)([0-9]+)(\w+)/', $k, $m ) ) {
			// Remove irrelevant sections from database for this revision.
			if ( ! $m[2] < $max_sections ) {
				delete_post_meta( $post_id, $m[0] );
			}
		}
	}

	$_POST['acf'] = $clean_fields;
}

add_action('acf/save_post', 'exchange_remove_empty_acf_meta_at_save', 1);
