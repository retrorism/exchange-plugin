<?php
/**
 * Plugin for Tandem Exchange WordPress theme
 *
 * @link    http//github.com/retrorism/exchange
 * @package Exchange Plugin
 * @version 0.1.0
 *
 * Plugin Name: Tandem
 * Plugin URI:  http://www.badjo.nl/plugins/exchange-plugin/
 * Description: This plugin adds all necessary functionality for the Exchange Theme
 * Author:      Bart Bloemers
 * Author:      Willem Prins
 * Version:     0.1.0
 * Author URI:  http://www.badjo.nl/
 **/

if ( ! function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

if ( ! defined( 'TANDEM_FILE' ) ) {
	define( 'TANDEM_FILE', __FILE__ );
}

if ( ! defined( 'TANDEM_PATH' ) ) {
	define( 'TANDEM_PATH', plugin_dir_path( TANDEM_FILE ) );
}

if ( ! isset( $GLOBALS['TANDEM_CONFIG'] ) ) {
	$GLOBALS['TANDEM_CONFIG'] = array(
		'COLORS' => array(
			'yellow-tandem' => 'f4c522', /* Tandem styleguide */
			'black-tandem'  => '4c4d53', /* Tandem styleguide */
			'white'         => 'ffffff',
			'salmon-1-web'  => 'fde1c7', /* Section / Box bg webguide */
			'yellow-1-web'  => 'fffbdb', /* Section / Box bg webguide */
			'blue-1-web'	=> 'dceff0', /* Section bg webguide */
			'rose-1-web'	=> 'ff8e78', /* Box bg webguide */
			'blue-2-web'	=> 'dceff0', /* Box bg webguide */
			'yellow-1'      => 'fffac0', /* Sticky Notes styleguide */
			'yellow-2'      => 'f0c063',
			'yellow-3'      => 'eba847', /* Accents on yellow */
			'yellow-4'      => 'e27f20',
			'salmon-1'      => 'f7e6ce',
			'salmon-2'      => 'f0c590',
			'salmon-3'      => 'eaab73',
			'salmon-4'      => 'e07856', /* Accents on orange */
			'blue-1'        => 'bcdde9',
			'blue-2'        => '93c9e4',
			'blue-3'        => '0f9fd6', /* Accents on blue */
			'blue-4'        => '1F588E',
		),
		'IMAGES' => array(
			/* 'hq-norm' => 381024,  756 * 504 */
			'hq-norm'       => 393216, /* 768 * 512 */
			'size-in-story' => 'medium_large',
		),
	);
}

/* Runs on plugin is activated */
register_activation_hook( TANDEM_FILE, 'tandem_activate' );

/* Runs on plugin deactivation */
register_deactivation_hook( TANDEM_FILE, 'tandem_deactivate' );


/**
 * Runs on activation of the plugin.
 **/
function tandem_activate() {
	return;
}

/**
 * Runs on plugin deactivation.
 **/
function tandem_deactivate() {
	return;
}

// Register theme as taxonomy.
function tandem_create_tax_topic() {
	register_taxonomy(
		'topic',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		array( 'collaboration', 'story' ), // Post type name.
		array(
			'hierarchical' => false,
			'label'        => 'Topics',  // Display name.
			'show_ui'      => false,
			'query_var'    => true,
			'rewrite'      => array(
				'slug'       => 'topics', // This controls the base slug that will display before each term.
				'with_front' => false, // Don't display the category base before.
			),
		)
	);
}
add_action( 'init', 'tandem_create_tax_topic' );


// Register methodologies as taxonomy.
function tandem_create_tax_methodology() {
	register_taxonomy(
		'methodology',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		array( 'collaboration' ), // Post type name.
		array(
			'hierarchical' => false,
			'label'        => 'Methodologies',  // Display name.
			'show_ui'      => false,
			'query_var'    => true,
			'rewrite'      => array(
				'slug'       => 'methodologies', // This controls the base slug that will display before each term
				'with_front' => false, // Don't display the category base before.
			),
		)
	);
}

// Register discipline as taxonomy.
function tandem_create_tax_discipline() {
	register_taxonomy(
		'discipline',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		array( 'collaboration' ),	// Post type name.
		array(
			'hierarchical' => false,
			'label'        => 'Disciplines',  // Display name.
			'show_ui'      => false,
			'query_var'    => true,
			'rewrite'      => array(
				'slug'       => 'disciplines', // This controls the base slug that will display before each term.
				'with_front' => false, // Don't display the category base before.
			),
		)
	);
}

// Register output as taxonomy.
function tandem_create_tax_output() {
	register_taxonomy(
		'output', // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		'collaboration', // Post type name.
		array(
			'hierarchical' => false,
			'label'        => 'Outputs',  // Display name.
			'show_ui'      => false,
			'query_var'    => true,
			'rewrite'      => array(
				'slug'       => 'outputs', // This controls the base slug that will display before each term.
				'with_front' => false, // Don't display the category base before.
			),
		)
	);
}

// Register language as taxonomy.
function tandem_create_tax_language() {
	register_taxonomy(
		'language',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		'story',	// Post type name.
		array(
			'hierarchical' => false,
			'label'        => 'Language',  // Display name.
			'show_ui'      => false,
			'query_var'    => true,
			'rewrite'      => array(
				'slug'       => 'languages', // This controls the base slug that will display before each term.
				'with_front' => false, // Don't display the category base before.
			),
		)
	);
}

// Register location as taxonomy.
function tandem_create_tax_location() {
	register_taxonomy(
		'location',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
		array( 'participant', 'story' ), // Post type name.
		array(
			'hierarchical' => false,
			'label'        => 'Location',  // Display name.
			'show_ui'      => false,
			'query_var'    => true,
			'rewrite'      => array(
				'slug'       => 'locations', // This controls the base slug that will display before each term.
				'with_front' => false, // Don't display the category base before.
			),
		)
	);
}

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
		'menu_position'       => 2,
		'public'              => true,
		'exclude_from_search' => false,
		'capability_type'     => 'post',
		'supports'            => array( 'title', 'thumbnail', 'revisions' ),
		'rewrite'             => array( 'slug' => 'stories' ),
		// Removed 'taxonomies' => array( 'category','post_tag').
	) );
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
		'parent_item_colon'  => '',
		'menu_name'          => 'Participants',
	);

	// Register post type.
	register_post_type( 'participant', array(
		'labels'              => $labels,
		'has_archive'         => false,
		'menu_icon'           => 'dashicons-groups',
		'menu_position'       => 3,
		'public'              => true,
		// Other items that are available for this array: 'title','editor','author','thumbnail','excerpt','trackbacks', 'custom-fields','comments','revisions','page-attributes','post-formats'.
		'supports'            => array( 'title' ),
		'exclude_from_search' => true,
		'capability_type'     => 'post',
		'rewrite'             => array( 'slug' => 'participant' ),
		)
	);
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
		'parent_item_colon'  => '',
	);
	// Register post type.
	register_post_type( 'collaboration', array(
		'labels' => $labels,
		'has_archive' => true,
		'menu_icon' => 'dashicons-editor-paste-text',
		'menu_position' => 4,
		'public' => true,
		// Other items that are available for this array: 'title','editor','author','thumbnail','excerpt','trackbacks', 'custom-fields','comments','revisions','page-attributes','post-formats'.
		'supports' => array( 'title', 'thumbnail', 'revisions' ),
		// Removed: 'taxonomies' => array( 'output', 'theme', 'discipline' ).
		'exclude_from_search' => false,
		'capability_type' => 'post',
		'rewrite' => array( 'slug' => 'collaborations' ),
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
		'menu_position'       => 5,
		'public'              => true,
		// Supports can hold: 'title','editor','author','thumbnail','excerpt','trackbacks', 'custom-fields','comments','revisions','page-attributes','post-formats'.
		'supports'            => array( 'title','editor' ),
		'exclude_from_search' => false,
		'capability_type'     => 'post',
		'rewrite'             => array( 'slug' => 'programme-rounds' ),
		)
	);
}

/* Hook taxonomy creation to init. */
add_action( 'init', 'tandem_create_tax_methodology' );
add_action( 'init', 'tandem_create_tax_discipline' );
add_action( 'init', 'tandem_create_tax_output' );
add_action( 'init', 'tandem_create_tax_language' );
add_action( 'init', 'tandem_create_tax_location' );

/* Hook post creation to init. */
add_action( 'init', 'tandem_create_story' );
add_action( 'init', 'tandem_create_participant' );
add_action( 'init', 'tandem_create_collaboration' );
add_action( 'init', 'tandem_create_programme_round' );

/* Hook meta boxes to the 'story' and 'collaboration' post types. */
add_action( 'add_meta_boxes_story', 'tandem_add_meta_boxes_for_story' );
add_action( 'add_meta_boxes_collaboration', 'tandem_add_meta_boxes_for_collaboration' );

/* Creates  meta box for a story. */
function tandem_add_meta_boxes_for_story( $post ) {
	add_meta_box(
		'tandem-story-parent',
		__( 'Collaborations', 'exchange-plugin' ),
		'tandem_story_parent_meta_box',
		$post->post_type,
		'side',
		'core'
	);
}

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
		'numberposts' => -1,
	);

	$parent_query = new WP_Query( args );
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


add_action( 'admin_menu', 'tandem_register_custom_submenu_page' );

function tandem_register_custom_submenu_page() {

	add_submenu_page(
		'edit.php?post_type=story',
		'Languages',
		'Languages',
		'edit_posts',
		'edit-tags.php?taxonomy=language&post_type=story',
	false );

	add_submenu_page(
		'edit.php?post_type=collaboration',
		'Topics',
		'Topics',
		'edit_posts',
		'edit-tags.php?taxonomy=topic&post_type=collaboration',
	false );

	add_submenu_page(
		'edit.php?post_type=collaboration',
		'Disciplines',
		'Disciplines',
		'edit_posts',
		'edit-tags.php?taxonomy=discipline&post_type=collaboration',
	false );

	add_submenu_page(
		'edit.php?post_type=collaboration',
		'Methodologies',
		'Methodologies',
		'edit_posts',
		'edit-tags.php?taxonomy=methodologies&post_type=collaboration',
	false );

	add_submenu_page(
		'edit.php?post_type=collaboration',
		'Outputs',
		'Outputs',
		'edit_posts',
		'edit-tags.php?taxonomy=output&post_type=collaboration',
	false );

	add_submenu_page(
		'edit.php?post_type=participant',
		'Locations',
		'Locations',
		'edit_posts',
		'edit-tags.php?taxonomy=location&post_type=participant',
	false );
}


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
	$location =  $_POST['acf']['field_56b9ba1fceb87']; // Organisation_city

	if ( isset( $location ) ) {
		// Add these location, note the last argument is true.
		$term_taxonomy_ids = wp_set_object_terms( $post_id, $location, 'location', false );

	}
}
// Vervangen door onderste functie:
//add_action( 'save_post_participant', 'save_post_participant_meta', 10, 3 );
function save_post_with_each_acf_update( $post_id ) {

	$post_type = get_post_type( $post_id );
	if ( 'participant' === $post_type ) {
		$location = get_field( 'organisation_city', $post_id );
		if ( isset( $location ) ) {
			// Add these location, note the last argument is true.
			$term_taxonomy_ids = wp_set_object_terms( $post_id, $location, 'location', false );
		}
	} elseif ( 'story' === $post_type ) {
		$story_teller = get_field( 'story_teller', $post_id );
		$location = $story_teller->organisation_city;

		$selected = get_field( 'add_special_tags' , $post_id );
		if ( is_array( $selected ) && in_array( 'location' , $selected, true ) && isset( $location ) ) {
			// Add location, note the last argument is false.
			$term_taxonomy_ids = wp_set_object_terms( $post_id, $location, 'location', false );
		} elseif ( ! is_array( $selected ) || ! in_array( 'location' , $selected, true ) ) {
			// Remove location.
			wp_set_object_terms( $post_id, null, 'location' );
		}
	}
}

add_action( 'acf/save_post', 'save_post_with_each_acf_update', 20 );


function tandem_admin_enqueue_scripts() {
	wp_enqueue_script( 'tandem-admin-js', plugin_dir_url( TANDEM_FILE )  . 'js/tandem_admin.js', array(), '1.0.0', true );
}

add_action( 'admin_enqueue_scripts', 'tandem_admin_enqueue_scripts' );


/**
 * Auto load our class files.
 *
 * @param  string $class Class name.
 * @return void
 */
function tandem_auto_load( $class ) {
	static $classes = null;

	if ( null === $classes ) {
		$classes = array(
			'participant'  => TANDEM_PATH . 'includes/class-participant.php',
			'story'  => TANDEM_PATH . 'includes/class-story.php',
			'basepattern' => TANDEM_PATH . 'includes/patterns/class-pattern-base.php',
			'paragraph' => TANDEM_PATH . 'includes/patterns/class-paragraph.php',
			'pullquote' => TANDEM_PATH . 'includes/patterns/class-quote-pull.php',
			'blockquote' => TANDEM_PATH . 'includes/patterns/class-quote-block.php',
			'section' => TANDEM_PATH . 'includes/patterns/class-section.php',
			'sectionheader' => TANDEM_PATH . 'includes/patterns/class-header-section.php',
			'subheader' => TANDEM_PATH . 'includes/patterns/class-header-sub.php',
			'image' => TANDEM_PATH . 'includes/patterns/class-image.php',
			'imageduo' => TANDEM_PATH . 'includes/patterns/class-image-duo.php',
			'caption' => TANDEM_PATH . 'includes/patterns/class-caption.php',
			'basecontroller'  => TANDEM_PATH . 'controllers/controller-base.php',
			'storycontroller'  => TANDEM_PATH . 'controllers/controller-story.php',
		);
	}

	$cn = strtolower( $class );

	if ( isset( $classes[ $cn ] ) ) {
		require_once( $classes[ $cn ] );
	}
}

if ( function_exists( 'tandem_auto_load' ) ) {
	spl_autoload_register( 'tandem_auto_load' );
}
