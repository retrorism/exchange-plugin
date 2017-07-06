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

add_action( 'init', 'exchange_connect_default_taxonomies' );
add_action( 'init', 'exchange_modify_post_tag' );
add_action( 'init', 'exchange_create_taxonomies' );

add_action( 'save_post_programme_round', 'exchange_create_tax_for_programme_round', 10, 3 );
add_action( 'save_post_collaboration', 'exchange_set_post_tag_from_parent_id', 10, 3 );
add_action( 'save_post_story', 'exchange_set_attachments_post_tag', 10, 4 );
add_action( 'save_post_collaboration', 'exchange_set_attachments_post_tag', 10, 4 );
add_action( 'save_post_programme_round', 'exchange_set_attachments_post_tag', 10, 4 );
add_action( 'attachment_updated', 'exchange_set_attachment_media_tags', 10, 3 );

add_action( 'pre_get_posts', 'exchange_modify_post_tag_query' );

function exchange_connect_default_taxonomies() {
	register_taxonomy_for_object_type( 'category', 'story' );
}

function exchange_modify_post_tag() {
	
	// Maintain the built-in rewrite functionality of WordPress tags
	global $wp_rewrite;

	$rewrite =  array(
		'hierarchical'               => false, // Maintains tag permalink structure
		'slug'                       => get_option('tag_base') ? get_option('tag_base') : 'programme-round',
		'with_front'                 => ! get_option('tag_base') || $wp_rewrite->using_index_permalinks(),
		'ep_mask'                    => EP_TAGS,
	);

	// Redefine tag labels (or leave them the same)
	$labels = array(
	    'name'                       => 'Programme Round (Tags)',
	    'menu_name'                  => 'Programme Round (Tags)',
	    'singular_name'              => 'Programme Round (Tag)',
	    'search_items'               => 'Search Programme Round (Tags)',
	    'popular_items'              => 'Popular Programme Round (Tags)',
	    'all_items'                  => 'All Programme Round (Tags)',
	    'parent_item'                => 'Programme',
	    'parent_item_colon'          => 'Programme:',
	    'edit_item'                  => 'Edit Programme Round (Tag)',
	    'update_item'                => 'Update Programme Round (Tag)',
	    'add_new_item'               => 'Add new Programme Round (Tag)',
	    'new_item_name'              => 'New Programme Round (Tag) Name',
	    'separate_items_with_commas' => 'Separate Programme Round (Tags) with commas',
	    'add_or_remove_items'        => 'Add or remove Programme Round (Tags)',
	    'choose_from_most_used'      => 'Choose from the most used Programme Round (Tags)',
	);

	// Override structure of built-in WordPress tags
	register_taxonomy( 'post_tag', array('story','collaboration','programme_round'), array(
		'hierarchical'               => true, // Was false, now set to true
		'query_var'                  => 'programme-round',
		'labels'                     => $labels,
		'rewrite'                    => $rewrite,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'_builtin'                   => true,
	) );
}

/**
 * Adjust post_tag term query to return all relevant post types.
 *
 * @param array $query Global $query object.
 * @return return void if the query does not contain a post_tag.
 */
function exchange_modify_post_tag_query( $query ) {
	$pr = get_query_var( 'programme-round' );
	if ( ! $query->is_main_query() || empty( $pr ) ) {
		return;
	}
	if ( is_string( $pr ) ) {
		$args = array(
			'taxonomy' => 'post_tag',
			'slug' => $pr,
			'number' => 1
		);
		$pr_obj = get_terms( $args );
		if ( $pr_obj[0] instanceof WP_Term ) {
			$pr_children = get_term_children( intval( $pr_obj[0]->term_id ), 'post_tag' );
		}
	}
	$post_type = get_query_var( 'post_type' );
	if ( $pr_children ) {
		$query->query_vars['tag__in'] = $pr_children;
	}
	$query->query_vars['tag__in'][] = $pr;
	if ( empty( $post_type ) ) {
		$query->set( 'post_type', array( 'collaboration','story' ) );
		// elseif ( is_post_type_archive('collaboration') || is_post_type_archive('story') ) {
	}
}

function exchange_query_vars_filter($vars) {
	$vars[] = 'programme-round';
	return $vars;
}
add_filter( 'query_vars', 'exchange_query_vars_filter' );

function exchange_url_rewrite_templates_for_post_type() {
	$post_type = get_query_var( 'post_type' );
	if ( ! empty( $post_type ) && ! is_array( $post_type ) ) {
		return;
	}
	if ( get_query_var( 'programme-round' ) ) {
		add_filter( 'template_include', function() {
			$archive_template = locate_template( array( 'archive.php' ) );
			if ( '' !== $archive_template ) {
				return $archive_template;
			}
			return $archive_template;
		});
	}
}

add_action( 'template_redirect', 'exchange_url_rewrite_templates_for_post_type' );

function exchange_create_taxonomies() {

	// Register language as taxonomy.
	register_taxonomy(
		'tandem',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		array( 'story', 'page' ), // Post type name.
		array(
			'hierarchical' => false,
			'sort'         => true,
			'labels'       => array(
				'name'          => __( 'Tandem Tag', EXCHANGE_PLUGIN ),
				'singular_name' => __( 'Tandem Tag', EXCHANGE_PLUGIN ),
				'menu_name'     => __( 'Tandem Tags', EXCHANGE_PLUGIN ), 
				'add_new_item'  => __( 'Add new Tandem Tag', EXCHANGE_PLUGIN ), 
			),
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
		)
	);

	// Register language as taxonomy.
	register_taxonomy(
		'language',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		array( 'story', 'page' ), // Post type name.
		array(
			'hierarchical' => false,
			'sort'         => true,
			'labels'       => array(
				'name'          => __( 'Language', EXCHANGE_PLUGIN ),
				'singular_name' => __( 'Language', EXCHANGE_PLUGIN ),
				'menu_name'     => __( 'Languages', EXCHANGE_PLUGIN ),  
				'add_new_item'  => __( 'Add new language tag', EXCHANGE_PLUGIN ),
			),
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
		)
	);

	// Register theme as taxonomy.
	register_taxonomy(
		'topic',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		array( 'story', 'collaboration', 'page' ), // Post type name.
		array(
			'hierarchical' => false,
			'sort'         => true,
			'labels'       => array(
				'name'          => __( 'Topic', EXCHANGE_PLUGIN ),
				'singular_name' => __( 'Topic', EXCHANGE_PLUGIN ),
				'menu_name'     => __( 'Topics', EXCHANGE_PLUGIN ),  
			),
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
		)
	);

	// Register location as taxonomy.
	register_taxonomy(
		'location',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		array( 'story', 'collaboration', 'page' ), // Post type name.
		array(
			'hierarchical' => false,
			'sort'         => true,
			'labels'       => array(
				'name'          => __( 'Location', EXCHANGE_PLUGIN ),
				'singular_name' => __( 'Location', EXCHANGE_PLUGIN ),
				'menu_name'     => __( 'Locations', EXCHANGE_PLUGIN ),  
			),			
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
		)
	);

	// Register methodologies as taxonomy.
	register_taxonomy(
		'methodology',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		array( 'story', 'collaboration', 'page' ), // Post type name.
		array(
			'hierarchical' => false,
			'sort'         => true,
			'labels'       => array(
				'name'          => __( 'Methodology', EXCHANGE_PLUGIN ),
				'singular_name' => __( 'Methodology', EXCHANGE_PLUGIN ),
				'menu_name'     => __( 'Methodologies', EXCHANGE_PLUGIN ),  
			),			
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
		)
	);

	// Register discipline as taxonomy.
	register_taxonomy(
		'discipline',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		array( 'story', 'collaboration', 'page' ), // Post type name.
		array(
			'hierarchical' => false,
			'sort'         => true,
			'labels'       => array(
				'name'          => __( 'Discipline', EXCHANGE_PLUGIN ),
				'singular_name' => __( 'Discipline', EXCHANGE_PLUGIN ),
				'menu_name'     => __( 'Disciplines', EXCHANGE_PLUGIN ),  
			),
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
		)
	);
	register_taxonomy_for_object_type( 'discipline', 'collaboration' );

	// Register output as taxonomy.
	register_taxonomy(
		'project_output', // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		array( 'story', 'collaboration', 'page' ), // Post type name.
		array(
			'hierarchical' => false,
			'sort'         => true,
			'labels'       => array(
				'name'          => __( 'Project Output', EXCHANGE_PLUGIN ),
				'singular_name' => __( 'Project Output', EXCHANGE_PLUGIN ),
				'menu_name'     => __( 'Project Outputs', EXCHANGE_PLUGIN ),  
			),			
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
