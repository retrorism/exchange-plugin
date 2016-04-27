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
add_action( 'init', 'exchange_fix_tag_labels' );
add_action( 'init', 'exchange_create_tax_language' );
add_action( 'init', 'exchange_create_tax_location' );
add_action( 'init', 'exchange_create_tax_topic' );
add_action( 'init', 'exchange_create_tax_discipline' );
add_action( 'init', 'exchange_create_tax_methodology' );
add_action( 'init', 'exchange_create_tax_output' );

add_action( 'save_post_programme_round', 'exchange_create_tax_for_programme_round', 10, 3 );
add_action( 'save_post_collaboration', 'exchange_set_post_tag_from_parent_id', 10, 3 );
add_action( 'save_post_story', 'exchange_set_attachments_post_tag', 10, 4 );
add_action( 'save_post_collaboration', 'exchange_set_attachments_post_tag', 10, 4 );
add_action( 'save_post_programme_round', 'exchange_set_attachments_post_tag', 10, 4 );
add_action( 'attachment_updated', 'exchange_set_attachment_media_tags', 10, 3 );


function exchange_connect_default_taxonomies() {
	register_taxonomy_for_object_type( 'category', 'story' );
	register_taxonomy_for_object_type( 'post_tag', 'story' );
	register_taxonomy_for_object_type( 'post_tag', 'collaboration' );
	register_taxonomy_for_object_type( 'post_tag', 'programme_round' );
}

// Register language as taxonomy.
function exchange_create_tax_language() {
	register_taxonomy(
		'language',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		'story',	// Post type name.
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
		array( 'collaboration', 'story' ), // Post type name.
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
				'slug'       => 'topics', // This controls the base slug that will display before each term.
				'with_front' => false, // Don't display the category base before.
			),
		)
	);
}

// Register methodologies as taxonomy.
function exchange_create_tax_methodology() {
	register_taxonomy(
		'methodology',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		array( 'collaboration' ), // Post type name.
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
				'slug'       => 'methodologies', // This controls the base slug that will display before each term
				'with_front' => false, // Don't display the category base before.
			),
		)
	);
}

// Register discipline as taxonomy.
function exchange_create_tax_discipline() {
	register_taxonomy(
		'discipline',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		array( 'collaboration' ),	// Post type name.
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
				'slug'       => 'disciplines', // This controls the base slug that will display before each term.
				'with_front' => false, // Don't display the category base before.
			),
		)
	);
	register_taxonomy_for_object_type( 'discipline', 'collaboration' );

}

// Register output as taxonomy.
function exchange_create_tax_output() {
	register_taxonomy(
		'output', // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		'collaboration', // Post type name.
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
		)
	);
}

// Register location as taxonomy.
function exchange_create_tax_location() {
	register_taxonomy(
		'location',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		array( 'collaboration', 'story' ), // Post type name.
		array(
			'hierarchical' => false,
			'sort'         => true,
			'label'        => __( 'Locations', EXCHANGE_PLUGIN ),  // Display name.
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_quick_edit' => false,
			'meta_box_cb'  => false,
			'public'       => true,
			'query_var'    => true,
			'rewrite'      => array(
				'slug'       => 'locations', // This controls the base slug that will display before each term.
				'with_front' => false, // Don't display the category base before.
			),
		)
	);
}

function exchange_fix_tag_labels()
{
    global $wp_taxonomies;

    // The list of labels we can modify comes from
    //  http://codex.wordpress.org/Function_Reference/register_taxonomy
    //  http://core.trac.wordpress.org/browser/branches/3.0/wp-includes/taxonomy.php#L350
    $wp_taxonomies['post_tag']->labels = (object)array(
        'name' => 'Programme Rounds',
        'menu_name' => 'Programme Rounds',
        'singular_name' => 'Programme Round',
        'search_items' => 'Search Programme Rounds',
        'popular_items' => 'Popular Programme Rounds',
        'all_items' => 'All Programme Rounds',
        'parent_item' => null, // Tags aren't hierarchical
        'parent_item_colon' => null,
        'edit_item' => 'Edit Programme Round',
        'update_item' => 'Update Programme Round',
        'add_new_item' => 'Add new Programme Round',
        'new_item_name' => 'New Programme Round Name',
        'separate_items_with_commas' => 'Separate Programme Rounds with commas',
        'add_or_remove_items' => 'Add or remove Programme Rounds',
        'choose_from_most_used' => 'Choose from the most used Programme Rounds',
    );

    $wp_taxonomies['post_tag']->label = 'Programme Rounds';
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
			$parent = get_term_by('name', 'Programme Round', 'media_category');
			if ( isset( $parent ) && is_object( $parent ) ) {
				$args['parent'] = $parent->term_id;
			}
		}
		$result = wp_insert_term( htmlspecialchars( $term ), $taxonomy, $args );
	}
}

// Create a new term when a new programme round is saved.
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

// Check and / or set the programme round tag to match the collaborations parent.
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
