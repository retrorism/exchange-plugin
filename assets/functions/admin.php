<?php
/**
 * Admin functions
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

/* Hook admin scripts to admin_enqueue scripts */
//add_action( 'admin_enqueue_scripts', 'tandem_admin_enqueue_scripts' );

/* Register admin menu pages upon loading admin menu */
add_action( 'admin_menu', 'tandem_add_and_remove_menu_options' );
add_action( 'admin_menu', 'tandem_register_settings');
add_action( 'admin_menu', 'tandem_add_options_page');
add_action( 'admin_init', 'set_admin_menu_separator' );

/* Hook meta boxes to the 'story' and 'collaboration' post types. */
// add_action( 'add_meta_boxes_story', 'tandem_add_meta_boxes_for_story' );
add_action( 'add_meta_boxes_collaboration', 'tandem_add_meta_boxes_for_collaboration' );

function tandem_admin_enqueue_scripts() {
	wp_enqueue_script( 'tandem-admin-js', plugin_dir_url( EXCHANGE_PLUGIN_FILE )  . '/assets/js/tandem_admin.js', array(), '0.1.0', true );
}

/* https://github.com/tommcfarlin/WordPress-Custom-Menu-Separator */
function set_admin_menu_separator() {
	global $menu;
	// Replace Media Upload item nothing and surround the Pages item with separators.
	$separators = array( 19 );
	foreach ( $separators as $sep_position ) {
		$menu [ $sep_position ] = array(
			0	=>	'',							// The text of the menu item
			1	=>	'read',						// Permission level required to view the item
			2	=>	'separator' . $sep_position,	// The ID of the menu item
			3	=>	'',							// Empty by default.
			4	=>	'wp-menu-separator'			// Custom class names for the menu item
		);
	};
	unset( $menu[ 10 ] );
	$menu[ 3 ] = array(
		0 => 'Media',
		1 => 'upload_files',
		2 => 'upload.php',
		3 => '',
		4 => 'menu-top menu-icon-media menu-top-first',
		5 => 'menu-media',
		6 => 'dashicons-admin-media',
	);
	ksort( $menu );
}

function tandem_add_and_remove_menu_options() {
	if ( ! current_user_can( 'edit_files') ) {
		remove_menu_page( 'edit.php' ); // Remove Posts editor from menu for editors.
		remove_menu_page( 'edit-comments.php' ); // Comments from menu for editors.
		remove_submenu_page( 'upload.php', 'edit-tags.php?taxonomy=post_tag&amp;post_type=attachment' );
	}

	remove_meta_box( 'categorydiv', 'story', 'side');
	//remove_meta_box( 'tagsdiv-post_tag', 'collaboration', 'side');
	remove_meta_box( 'tagsdiv-post_tag', 'story', 'side');
	remove_meta_box( 'tagsdiv-post_tag', 'attachment', 'side');
	remove_submenu_page( 'edit.php?post_type=collaboration', 'edit-tags.php?taxonomy=post_tag&amp;post_type=collaboration' );
	remove_submenu_page( 'edit.php?post_type=story', 'edit-tags.php?taxonomy=post_tag&amp;post_type=story' );


 // end set_admin_menu_separator
	// add_submenu_page(
	// 	'edit.php?post_type=story',
	// 	'Languages',
	// 	'Languages',
	// 	'edit_posts',
	// 	'edit-tags.php?taxonomy=language&post_type=story'
	// );

	// add_submenu_page(
	// 	'edit.php?post_type=collaboration',
	// 	'Topics',
	// 	'Topics',
	// 	'edit_posts',
	// 	'edit-tags.php?taxonomy=topic&post_type=collaboration',
	// false );

	// add_submenu_page(
	// 	'edit.php?post_type=collaboration',
	// 	'Disciplines',
	// 	'Disciplines',
	// 	'edit_posts',
	// 	'edit-tags.php?taxonomy=discipline&post_type=collaboration',
	// false );

	// add_submenu_page(
	// 	'edit.php?post_type=collaboration',
	// 	'Methodologies',
	// 	'Methodologies',
	// 	'edit_posts',
	// 	'edit-tags.php?taxonomy=methodologies&post_type=collaboration',
	// false );

	// add_submenu_page(
	// 	'edit.php?post_type=collaboration',
	// 	'Outputs',
	// 	'Outputs',
	// 	'edit_posts',
	// 	'edit-tags.php?taxonomy=output&post_type=collaboration',
	// false );

	// add_submenu_page(
	// 	'edit.php?post_type=participant',
	// 	'Locations',
	// 	'Locations',
	// 	'edit_posts',
	// 	'edit-tags.php?taxonomy=location&post_type=participant',
	// false );
}


// /* Creates  meta box for a story. */
// function tandem_add_meta_boxes_for_story( $post ) {
// 	add_meta_box(
// 		'tandem-story-parent',
// 		__( 'Collaboration?', 'exchange-plugin' ),
// 		'tandem_story_parent_meta_box',
// 		$post->post_type,
// 		'side',
// 		'core'
// 	);
// }

/* Creates the meta box for project. */
function tandem_add_meta_boxes_for_collaboration( $post ) {
	add_meta_box(
		'tandem-programme_round-parent',
		__( 'Programme round', 'exchange-plugin' ),
		'tandem_programme_rounds_parent_meta_box',
		$post->post_type,
		'side',
		'core'
	);
}


/* Displays the meta box. */
function tandem_story_parent_meta_box( $post ) {

	$args = array(
		'post_type'   => 'collaboration',
		'orderby'     => 'title',
		'order'       => 'ASC',
		'numberposts' => -1,
		'posts_per_page' => -1,
	);

	$parent_query = new WP_Query( $args );
	$parents = $parent_query->posts;

	if ( ! empty( $parents ) ) {

		$output = '<select name="parent_id" class="widefat">'; // !Important! Don't change the 'parent_id' name attribute.

		foreach ( $parents as $parent ) {
			$output .= sprintf( '<option value="%s"%s>%s</option>', esc_attr( $parent->ID ), selected( $parent->ID, $post->post_parent, false ), esc_html( $parent->post_title ) );
		}

		$output .= '</select>';
	} else {
		$output = __( 'You have to add a collaboration first', 'exchange-plugin' );
	}

	echo $output;
}

/* Display meta box proramme rounds. */
function tandem_programme_rounds_parent_meta_box( $post ) {

	$args = array(
		'post_type'   => 'programme_round',
		'orderby'     => 'title',
		'order'       => 'ASC',
		'posts_per_page' => -1,
	);

	$parent_query = new WP_Query( $args );
	$parents = $parent_query->posts;

	if ( ! empty( $parents ) ) {

		$output = '<select name="parent_id" class="widefat">'; // !Important! Don't change the 'parent_id' name attribute.
		foreach ( $parents as $parent ) {
			$output .= sprintf( '<option value="%s"%s>%s</option>', esc_attr( $parent->ID ), selected( $parent->ID, $post->post_parent, false ), esc_html( $parent->post_title ) );
		}

		$output .= '</select>';
	} else {
		$output = __( 'You have to add a programme round first', 'exchange-plugin' );
	}

	echo $output;
}
