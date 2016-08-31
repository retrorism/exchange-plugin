<?php
/**
 * Functions for taxonomy creation.
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

/* Hook taxonomy creation to init. */
add_action( 'init', 'exchange_connect_default_taxonomies' );
add_action( 'init', 'exchange_modify_post_tag', 11 );
add_action( 'init', 'exchange_fix_tag_labels' );
add_action( 'init', 'exchange_create_tax_language' );
add_action( 'init', 'exchange_create_tax_tandem' );
add_action( 'init', 'exchange_create_tax_location' );
add_action( 'init', 'exchange_create_tax_topic' );
add_action( 'init', 'exchange_create_tax_discipline' );
add_action( 'init', 'exchange_create_tax_methodology' );
add_action( 'init', 'exchange_create_tax_project_output' );


add_action( 'save_post_programme_round', 'exchange_create_tax_for_programme_round', 10, 3 );
add_action( 'save_post_collaboration', 'exchange_set_post_tag_from_parent_id', 10, 3 );
add_action( 'save_post_story', 'exchange_set_attachments_post_tag', 10, 4 );
add_action( 'save_post_collaboration', 'exchange_set_attachments_post_tag', 10, 4 );
add_action( 'save_post_programme_round', 'exchange_set_attachments_post_tag', 10, 4 );
add_action( 'attachment_updated', 'exchange_set_attachment_media_tags', 10, 3 );

// add_filter( 'pre_option_tag_base', 'exchange_change_tag_base' );
// function exchange_change_tag_base( $value ) {
//
//    // let's change our tag slug to ravings
//    // this will change the permalink to http://wpdreamer.com/ravings/custom-post-types/
//    return 'programme-rounds';
//
// }

function exchange_connect_default_taxonomies() {
	register_taxonomy_for_object_type( 'category', 'story' );
	// register_taxonomy_for_object_type( 'post_tag', 'story' );
	// register_taxonomy_for_object_type( 'post_tag', 'collaboration' );
	// register_taxonomy_for_object_type( 'post_tag', 'programme_round' );
}

function exchange_modify_post_tag() {
    // get the arguments of the already-registered taxonomy
    $programme_round_args = get_taxonomy( 'post_tag' ); // returns an object

    // make changes to the args
    // in this example there are three changes
    // again, note that it's an object
	$programme_round_args->query_var = 'programme-round';
    $programme_round_args->rewrite['slug'] = 'programme-round';
    $programme_round_args->rewrite['with_front'] = 1;
	$programme_round_args->rewrite['show_ui'] = 0;

    // re-register the taxonomy
    register_taxonomy( 'post_tag', array('story','collaboration','programme_round'), (array) $programme_round_args);
}


function exchange_modify_post_tag_query( $query ) {
	$programme_round = get_query_var('programme-round');
	if ( ! $query->is_main_query() || empty( $programme_round ) ) {
		return;
	}
	$post_type = get_query_var( 'post_type' );
	$query->query_vars['tag_slug__in'][] = $programme_round;
	if ( empty( $post_type ) ) {
		$query->set( 'post_type', array( 'collaboration','story' ) );
	//elseif ( is_post_type_archive('collaboration') || is_post_type_archive('story') ) {
	}
}

add_action( 'pre_get_posts', 'exchange_modify_post_tag_query' );

function exchange_query_vars_filter($vars) {
  $vars[] = 'programme-round';
  return $vars;
}
add_filter( 'query_vars', 'exchange_query_vars_filter' );

function exchange_url_rewrite_templates() {
	$post_type = get_query_var( 'post_type' );
	if ( ! empty( $post_type ) && ! is_array( $post_type ) ) {
		return;
	}
    if ( get_query_var( 'programme-round' ) ) {
        add_filter( 'template_include', function() {
            return get_template_directory() . '/archive.php';
        });
    }
}

add_action( 'template_redirect', 'exchange_url_rewrite_templates' );


// Register language as taxonomy.
function exchange_create_tax_tandem() {
	register_taxonomy(
		'tandem',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		array( 'story', 'page' ), // Post type name.
		array(
			'hierarchical' => false,
			'sort'         => true,
			'label'        => 'Tandem Tags',  // Display name.
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_quick_edit' => false,
			'meta_box_cb'  => false,
			'public'       => true,
			'query_var'    => true,
			'rewrite'      => array(
				'slug'       => 'tandem', // This controls the base slug that will display before each term.
				'with_front' => false, // Don't display the category base before.
			),
			'show_in_rest'       => true,
			'rest_base'          => 'tandem',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
			'labels'       => array(
				'add_new_item' => 'Add new Tandem Tag',
			),
		)
	);
}

// Register language as taxonomy.
function exchange_create_tax_language() {
	register_taxonomy(
		'language',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		array( 'story', 'page' ), // Post type name.
		array(
			'hierarchical' => false,
			'sort'         => true,
			'label'        => 'Languages',  // Display name.
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_quick_edit' => false,
			'meta_box_cb'  => false,
			'public'       => true,
			'query_var'    => true,
			'rewrite'      => array(
				'slug'       => 'language', // This controls the base slug that will display before each term.
				'with_front' => false, // Don't display the category base before.
			),
			'show_in_rest'       => true,
  			'rest_base'          => 'language',
  			'rest_controller_class' => 'WP_REST_Terms_Controller',
			'labels'       => array(
				'add_new_item' => 'Add new language tag',
			),
		)
	);
}

// Register theme as taxonomy.
function exchange_create_tax_topic() {
	register_taxonomy(
		'topic',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		array( 'story', 'collaboration', 'page' ), // Post type name.
		array(
			'hierarchical' => false,
			'sort'         => true,
			'label'        => __( 'Topics', EXCHANGE_PLUGIN ),  // Display name.
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_admin_column' => true,
			'show_in_quick_edit' => false,
			'meta_box_cb'  => false,
			'public'       => true,
			'query_var'    => true,
			'rewrite'      => array(
				'slug'       => 'topic', // This controls the base slug that will display before each term.
				'with_front' => false, // Don't display the category base before.
			),
			'show_in_rest'       => true,
			'rest_base'          => 'topic',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
		)
	);
}


// Register location as taxonomy.
function exchange_create_tax_location() {
	register_taxonomy(
		'location',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		array( 'story', 'collaboration', 'page' ), // Post type name.
		array(
			'hierarchical' => false,
			'sort'         => true,
			'label'        => __( 'Locations', EXCHANGE_PLUGIN ),  // Display name.
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_quick_edit' => true,
			'meta_box_cb'  => false,
			'public'       => true,
			'query_var'    => true,
			'rewrite'      => array(
				'slug'       => 'location', // This controls the base slug that will display before each term.
				'with_front' => true, // Don't display the category base before.
			),
			'show_in_rest'       => true,
			'rest_base'          => 'location',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
		)
	);
}

// Register methodologies as taxonomy.
function exchange_create_tax_methodology() {
	register_taxonomy(
		'methodology',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		array( 'story', 'collaboration', 'page' ), // Post type name.
		array(
			'hierarchical' => false,
			'sort'         => true,
			'label'        => __( 'Methodologies', EXCHANGE_PLUGIN ),  // Display name.
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_quick_edit' => false,
			'meta_box_cb'  => false,
			'public'       => true,
			'query_var'    => true,
			'rewrite'      => array(
				'slug'       => 'methodology', // This controls the base slug that will display before each term
				'with_front' => false, // Don't display the category base before.
			),
			'show_in_rest'       => true,
			'rest_base'          => 'methodology',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
		)
	);
}

// Register discipline as taxonomy.
function exchange_create_tax_discipline() {
	register_taxonomy(
		'discipline',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		array( 'story', 'collaboration', 'page' ), // Post type name.
		array(
			'hierarchical' => false,
			'sort'         => true,
			'label'        => __( 'Disciplines', EXCHANGE_PLUGIN ),  // Display name.
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_quick_edit' => false,
			'meta_box_cb'  => false,
			'choose_from_most_used' => null,
			'public'       => true,
			'query_var'    => true,
			'rewrite'      => array(
				'slug'       => 'discipline', // This controls the base slug that will display before each term.
				'with_front' => false, // Don't display the category base before.
			),
			'show_in_rest'       => true,
			'rest_base'          => 'discipline',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
		)
	);
	register_taxonomy_for_object_type( 'discipline', 'collaboration' );

}

// Register output as taxonomy.
function exchange_create_tax_project_output() {
	register_taxonomy(
		'project_output', // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		array( 'story', 'collaboration', 'page' ), // Post type name.
		array(
			'hierarchical' => false,
			'sort'         => true,
			'label'        => __( 'Output Types', EXCHANGE_PLUGIN ),  // Display name.
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_quick_edit' => false,
			'meta_box_cb'  => false,
			'public'       => true,
			'query_var'    => true,
			'rewrite'      => array(
				'slug'       => 'output', // This controls the base slug that will display before each term.
				'with_front' => false, // Don't display the category base before.
			),
			'show_in_rest'       => true,
			'rest_base'          => 'project_output',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
		)
	);
}


function exchange_fix_tag_labels()
{
    global $wp_taxonomies;

    // The list of labels we can modify comes from
    //  http://codex.wordpress.org/Function_Reference/register_taxonomy
    //  http://core.trac.wordpress.org/browser/branches/3.0/wp-includes/taxonomy.php#L350
    $wp_taxonomies['post_tag']->labels = (object) array(
        'name' => 'Programme Round (Tags)',
        'menu_name' => 'Programme Round (Tags)',
        'singular_name' => 'Programme Round (Tag)',
        'search_items' => 'Search Programme Round (Tags)',
        'popular_items' => 'Popular Programme Round (Tags)',
        'all_items' => 'All Programme Round (Tags)',
        'parent_item' => null, // Tags aren't hierarchical
        'parent_item_colon' => null,
        'edit_item' => 'Edit Programme Round (Tag)',
        'update_item' => 'Update Programme Round (Tag)',
        'add_new_item' => 'Add new Programme Round (Tag)',
        'new_item_name' => 'New Programme Round (Tag) Name',
        'separate_items_with_commas' => 'Separate Programme Round (Tags) with commas',
        'add_or_remove_items' => 'Add or remove Programme Round (Tags)',
        'choose_from_most_used' => 'Choose from the most used Programme Round (Tags)',
    );

    $wp_taxonomies['post_tag']->label = 'Programme Round (Tag)s';
}

// Add taxonomies by checking against a sluggified $term name.
function add_taxo( $taxonomy, $term, $is_programme_round_media_tag = false ) {
	$args = array();
	$term_id = term_exists( htmlspecialchars( $term ), $taxonomy );
	if ( $term_id > 0 ) {
		//echo "existing term found";
		return $term_id;
	} else {
		if ( $is_programme_round_media_tag ) {
			$parent = get_term_by('name', 'Programme Round (Tag)', 'media_category');
			if ( isset( $parent ) && is_object( $parent ) ) {
				$args['parent'] = $parent->term_id;
			}
		}
		$result = wp_insert_term( htmlspecialchars( $term ), $taxonomy, $args );
	}
}

// Create a new term when a new Programme Round (Tag) is saved.
function exchange_create_tax_for_programme_round( $post_id, $post, $update ) {
	$name = $post->post_title;
	add_taxo( 'post_tag', $name );
};

function exchange_get_post_tag_from_parent_id( $post_id ) {
	$parent_id = wp_get_post_parent_id( $post_id );
	if ( ! empty( $parent_id ) ) {
		$parent_name = get_the_title( $parent_id );
		$parent_name_clean = sanitize_title( $parent_name );
		$term = get_term_by('slug', $parent_name_clean, 'post_tag' );
	}
	return $term;
}

function exchange_check_for_post_tag( $post_id ) {
	$tags = wp_get_post_terms( $post_id, 'post_tag' );
	if ( ! empty( $tags ) ) {
		$tag = exchange_get_post_tag_from_parent_id( $post_id );
	} else {
		$tag = $tags[0];
	}
	if ( exchange_is_term( $tag ) ) {
		return $tag;
	}
}

// Check and / or set the Programme Round (Tag) tag to match the collaborations parent.
function exchange_set_post_tag_from_parent_id( $post_id, $post, $update ) {
	$term = exchange_get_post_tag_from_parent_id( $post_id );
	if ( exchange_is_term( $term ) ) {
		exchange_set_tax_to_match_term( $post_id, 'post_tag', $term );
	}
}

function exchange_set_attachments_post_tag( $post_id, $post, $update ) {
	$attachments = get_children( array(
		'post_parent' => $post_id,
		'post_type' => 'attachment',
		'numberposts' => -1,
		'post_status' => 'any',
	) );
	if ( count( $attachments ) > 0 ) {
		$tag = exchange_check_for_post_tag( $post_id );
		foreach( $attachments as $a ) {
			exchange_set_tax_to_match_term( $a->ID, 'post_tag', $tag );
		}
	}
}

function exchange_set_tax_to_match_term( $post_id, $taxonomy, $term = null ) {
	if ( ! exchange_is_term( $term ) ) {
		return;
	}
	if ( has_term( $term->term_id, 'post_tag', $post_id ) ) {
		return;
	} else {
		$result = wp_set_object_terms( $post_id, $term->term_id, 'post_tag', false );
		return $result;
	}
}

function exchange_is_term( $input ) {
	if ( ! empty( $input ) && is_object( $input ) && 'WP_Term' === get_class( $input ) ) {
		return true;
	}
}
