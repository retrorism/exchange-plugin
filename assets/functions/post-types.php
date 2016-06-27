<?php
/**
 * Post types creation
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

/* Hook post creation to init. */
add_action( 'init', 'tandem_create_story' );
add_action( 'init', 'tandem_create_collaboration' );
add_action( 'init', 'tandem_create_participant' );
add_action( 'init', 'tandem_create_programme_round' );

// Register Story as Post Type.
function tandem_create_story() {

	// Set up labels.
	$labels = array(
		'name'               => 'Stories',
		'singular_name'      => 'Story',
		'add_new'            => 'Add new Story',
		'add_new_item'       => 'Add new Story',
		'edit_item'          => 'Edit Story',
		'new_item'           => 'New Story',
		'all_items'          => 'All Stories',
		'view_item'          => 'View Story',
		'search_items'       => 'Search Stories',
		'not_found'          => 'No Stories Found',
		'not_found_in_trash' => 'No Stories found in Trash',
		'parent_item_colon'  => '',
		'menu_name'          => 'Stories',
	);

	// Register post type.
	register_post_type( 'story', array(
		'labels'              => $labels,
		'has_archive'         => true,
		'menu_icon'           => 'dashicons-book',
		'menu_position'       => 11, // Dashboard is 2, Separator 4, Posts = 5, Media = 10,
		'public'              => true,
		'exclude_from_search' => false,
		'hierarchical'        => true,
		'capability_type'     => 'post',
		'supports'            => array( 'title','editor','thumbnail','revisions' ),
		'rewrite'             => array( 'slug' => 'stories' ),
		'taxonomies'          => array( 'post_tag','category','location','topic'),
	) );
}



// Register Collaboration as Post Type.
function tandem_create_collaboration() {

	// Set up labels.
	$labels = array(
		'name'               => 'Collaborations',
		'singular_name'      => 'Collaboration',
		'add_new'            => 'Add new Collaboration',
		'add_new_item'       => 'Add new Collaboration',
		'edit_item'          => 'Edit Collaboration',
		'new_item'           => 'New Collaboration',
		'all_items'          => 'All Collaborations',
		'view_item'          => 'View Collaboration',
		'search_items'       => 'Search Collaborations',
		'not_found'          => 'No Collaborations Found',
		'menu_name'          => 'Collaborations',
		'not_found_in_trash' => 'No Collaborations found in Trash',
		'parent_item_colon'  => 'Programme Round',
	);
	// Register post type.
	register_post_type( 'collaboration', array(
		'labels'              => $labels,
		'has_archive'         => true,
		'menu_icon'           => 'dashicons-editor-paste-text',
		'menu_position'       => 12,
		'public'              => true,
		'exclude_from_search' => false,
		// Awkwardly, WordPress routing finds it difficult to deal with parent posts that are of a different post type. So although we can still fill in the parent type.
		// So although we can still fill in the parent_ID columnt, we cannot create routing (easily).
		'hierarchical'        => false,
		'capability_type'     => 'post',
		// Other items that are available for this array: 'title','editor','author','thumbnail','excerpt','trackbacks', 'custom-fields','comments','revisions','page-attributes','post-formats'.
		'supports'            => array( 'title', 'thumbnail', 'editor', 'revisions' ),
		'rewrite'             => array(
			'slug'       => 'collaborations',
			'with_front' => true,
		),
		'taxonomies'          => array( 'post_tag','location','topic','discipline','methodology','output'),
		)
	);

}

// Register participant as Post Type.
function tandem_create_participant() {

	// Set up labels.
	$labels = array(
		'name'               => 'Participants',
		'singular_name'      => 'Participant',
		'add_new'            => 'Add new participant',
		'add_new_item'       => 'Add new participant',
		'edit_item'          => 'Edit participant',
		'new_item'           => 'New participant',
		'all_items'          => 'All participants',
		'view_item'          => 'View participant',
		'search_items'       => 'Search participants',
		'not_found'          => 'No participants Found',
		'not_found_in_trash' => 'No participant found in Trash',
		'menu_name'          => 'Participants',
	);

	// Register post type.
	register_post_type( 'participant', array(
		'labels'              => $labels,
		'has_archive'         => false,
		'menu_icon'           => 'dashicons-groups',
		'menu_position'       => 13,
		'public'              => true,
		// Other items that are available for this array: 'title','editor','author','thumbnail','excerpt','trackbacks', 'custom-fields','comments','revisions','page-attributes','post-formats'.
		'supports'            => array( 'title' ),
		'exclude_from_search' => true,
		'capability_type'     => 'post',
		'rewrite'             => array( 'slug' => 'participant' ),
		)
	);
}

// Register Programme round as Post Type.
function tandem_create_programme_round() {

	// Set up labels.
	$labels = array(
		'name'               => 'Programme rounds',
		'singular_name'      => 'Programme round',
		'add_new'            => 'Add new Programme round',
		'add_new_item'       => 'Add new Programme round',
		'edit_item'          => 'Edit Programme round',
		'new_item'           => 'New Programme round',
		'all_items'          => 'All Programme rounds',
		'view_item'          => 'View Programme round',
		'search_items'       => 'Search Programme rounds',
		'not_found'          => 'No Programme rounds Found',
		'not_found_in_trash' => 'No Programme rounds found in Trash',
		'parent_item_colon'  => '',
		'menu_name'          => 'Programme rounds',
	);
	// Register post type.
	register_post_type( 'programme_round', array(
		'labels'              => $labels,
		'has_archive'         => true,
		'menu_icon'           => 'dashicons-chart-pie',
		'menu_position'       => 14,
		'public'              => true,
		'hierarchical'        => true,
		// Supports can hold: 'title','editor','author','thumbnail','excerpt','trackbacks', 'custom-fields','comments','revisions','page-attributes','post-formats'.
		'supports'            => array( 'title','editor', 'thumbnail' ),
		'exclude_from_search' => false,
		'capability_type'     => 'page',
		'rewrite'             => array( 'slug' => 'programme-rounds' ),
		'taxonomies'          => array( 'post_tag' ),
		)
	);
}

function mmp_rewrite_rules($rules) {
    $newRules  = array();
    $newRules['basename/(.+)/(.+)/(.+)/(.+)/?$'] = 'index.php?custom_post_type_name=$matches[4]'; // my custom structure will always have the post name as the 5th uri segment
    $newRules['basename/(.+)/?$']                = 'index.php?taxonomy_name=$matches[1]';

    return array_merge($newRules, $rules);
}

add_filter('rewrite_rules_array', 'mmp_rewrite_rules');
