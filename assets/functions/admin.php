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
add_action( 'admin_init', 'exchange_set_admin_menu_separator' );

/* Hook meta boxes to the 'story' and 'collaboration' post types. */
// add_action( 'add_meta_boxes_story', 'tandem_add_meta_boxes_for_story' );
add_action( 'add_meta_boxes_collaboration', 'exchange_add_meta_box_for_collaboration' );
add_action( 'add_meta_boxes_story', 'exchange_add_meta_box_for_story' );


function tandem_admin_enqueue_scripts() {
	wp_enqueue_script( 'tandem-admin-js', plugin_dir_url( EXCHANGE_PLUGIN_FILE )  . '/assets/js/tandem_admin.js', array(), '0.1.0', true );
}


function exchange_add_tokenlist_widget() {

	wp_add_dashboard_widget(
		'exchange_tokenlist_widget',         // Widget slug.
		'Update links for each Programme Round:',         // Title.
		'exchange_tokenlist_widget' // Display function.
	);
}

add_action( 'wp_dashboard_setup', 'exchange_add_tokenlist_widget' );

/**
 * Create the function to output the contents of our Dashboard Widget.
 */
function exchange_tokenlist_widget() {
	$pr_set = BaseController::get_all_from_type( 'programme_round' );
	if ( empty( $pr_set ) ) {
		return;
	}
	$output = '<table width="100%"><tr><th align="left">' . __( 'Programme Round', EXCHANGE_PLUGIN ) . '</th>';
	$output .= '<th align="left">' . __( 'Token Link', EXCHANGE_PLUGIN ) . '</th>';
	$length = count( $pr_set );
	for ( $i = 0; $i < $length; $i++ ) {
		$token = get_post_meta( $pr_set[$i]->ID, 'update_token', true );
		$output .= '<tr><td width="40%">' . $pr_set[$i]->post_title . '</td>';
		$output .= '<td width="60%"><input style="width:100%;" type="text" value="' . get_home_url() . '/?pr=' . urlencode( $token ) . '"></td></tr>';
	}
	$output .= '</table>';
	echo $output;
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
function exchange_add_meta_box_for_collaboration( $post ) {
	add_meta_box(
		'tandem-programme_round-parent',
		__( 'Programme round', 'exchange-plugin' ),
		'exchange_programme_rounds_parent_meta_box',
		$post->post_type,
		'side',
		'core'
	);
}

/* Creates the meta box for stories that are submitted via a form */
function exchange_add_meta_box_for_story( $post ) {
	add_meta_box(
		'tandem-story-form-entry',
		__( 'Form Entry', 'exchange-plugin' ),
		'exchange_story_form_entry_meta_box',
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

/* Display meta box proramme rounds. */
function exchange_story_form_entry_meta_box( $post ) {
	$output = '';
	$form_entry_id = get_post_meta( $post->ID, 'form_entry_id', true );
	if ( ! empty( $form_entry_id ) && is_numeric( $form_entry_id ) ) {
		$output .= '<div class="form_entry_id-wrapper">';
		$story_form_id = get_option('options_story_update_form');
		if ( ! empty( $story_form_id ) ) {
			$admin_link = admin_url( '/admin.php?page=gf_entries&view=entry&id=' . $story_form_id . '&lid=' . $form_entry_id );
			$output .= '<a href="' . $admin_link . '">' . $form_entry_id . '</a>';
		} else {
			$output .= $form_entry_id;
		}
		$output .= '</div>';
	}
	echo $output;

}
