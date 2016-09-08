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
add_action( 'admin_menu', 'exchange_add_and_remove_menu_options' );
add_action( 'admin_menu', 'exchange_register_settings');
add_action( 'admin_menu', 'exchange_add_options_page');
add_action( 'admin_init', 'exchange_set_admin_menu_separator' );

/* Hook meta boxes to the 'story' and 'collaboration' post types. */
// add_action( 'add_meta_boxes_story', 'tandem_add_meta_boxes_for_story' );
add_action( 'add_meta_boxes_collaboration', 'exchange_add_meta_boxes_for_collaboration' );

function tandem_admin_enqueue_scripts() {
	wp_enqueue_script( 'tandem-admin-js', plugin_dir_url( EXCHANGE_PLUGIN_FILE )  . '/assets/js/tandem_admin.js', array(), '0.1.0', true );
}

/* https://github.com/tommcfarlin/WordPress-Custom-Menu-Separator */
function exchange_set_admin_menu_separator() {
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

function exchange_add_and_remove_menu_options() {
	if ( ! current_user_can( 'edit_files') ) {
		remove_menu_page( 'edit.php' ); // Remove Posts editor from menu for editors.
		remove_menu_page( 'edit-comments.php' ); // Comments from menu for editors.
		remove_submenu_page( 'upload.php', 'edit-tags.php?taxonomy=post_tag&amp;post_type=attachment' );
	}

	remove_meta_box( 'categorydiv', 'story', 'side');
	remove_meta_box( 'tagsdiv-post_tag', 'collaboration', 'side');
	remove_meta_box( 'tagsdiv-post_tag', 'story', 'side');
	remove_meta_box( 'tagsdiv-post_tag', 'attachment', 'side');
	remove_meta_box( 'tagsdiv-post_tag', 'programme_round', 'side' );
	remove_submenu_page( 'edit.php?post_type=collaboration', 'edit-tags.php?taxonomy=post_tag&amp;post_type=collaboration' );
	remove_submenu_page( 'edit.php?post_type=story', 'edit-tags.php?taxonomy=post_tag&amp;post_type=story' );
}

/* Creates the meta box for project. */
function exchange_add_meta_boxes_for_collaboration( $post ) {
	add_meta_box(
		'tandem-programme_round-parent',
		__( 'Programme round', 'exchange-plugin' ),
		'exchange_programme_rounds_parent_meta_box',
		$post->post_type,
		'side',
		'core'
	);
}

/* Display meta box proramme rounds. */
function exchange_programme_rounds_parent_meta_box( $post ) {
	$parents = BaseController::get_all_from_type( 'programme_round' );
	if ( ! empty( $parents ) ) {

		$output = '<select name="parent_id" class="widefat">'; // !Important! Don't change the 'parent_id' name attribute.
		$output .= '<option value="null">None</option>';
		foreach ( $parents as $parent ) {
			$output .= sprintf( '<option value="%s"%s>%s</option>', esc_attr( $parent->ID ), selected( $parent->ID, $post->post_parent, false ), esc_html( $parent->post_title ) );
		}

		$output .= '</select>';
	} else {
		$output = __( 'You have to add a programme round first', 'exchange-plugin' );
	}
	echo $output;
}
