<?php
/**
 * @package Exchange-Plugin
 * @version 0.1.0
 */
/*
Plugin Name: Tandem
Plugin URI: http://www.badjo.nl/plugins/exchange-plugin/
Description: Plugin for Tandem Exchange WordPress theme
Author: Bart Bloemers
Version: 0.1
Author URI: http://www.badjo.nl/
*/


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

/* Runs on plugin is activated */
register_activation_hook(TANDEM_FILE, 'tandem_activate');

/* Runs on plugin deactivation */
register_deactivation_hook(TANDEM_FILE, 'tandem_deactivate');


/**
 * Runs on activation of the plugin.
 */
function tandem_activate() {
 	return;
}

/**
 * Runs on plugin deactivation
*/
function tandem_deactivate() {
	return;
}

//Register theme as taxonomy
function tandem_create_tax_topic() {
    register_taxonomy(
        'topic',  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
        array('collaboration','story'),        //post type name
        array(
            'hierarchical' => false,
            'label' => 'Topics',  //Display name
            'show_ui' => false,
						'query_var' => true,
            'rewrite' => array(
            		'slug' => 'topics', // This controls the base slug that will display before each term
              	'with_front' => false // Don't display the category base before
            )
        )
    );
}
add_action( 'init', 'tandem_create_tax_topic');


//Register methodologies as taxonomy
function tandem_create_tax_methodology() {
    register_taxonomy(
        'methodology',  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
        array('collaboration'),        //post type name
        array(
            'hierarchical' => false,
            'label' => 'Methodologies',  //Display name
            'show_ui'           => false,
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'methodologies', // This controls the base slug that will display before each term
                'with_front' => false // Don't display the category base before
            )
        )
    );
}

//Register discipline as taxonomy
function tandem_create_tax_discipline() {
    register_taxonomy(
        'discipline',  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
        array('collaboration'),    //post type name
        array(
            'hierarchical' => false,
            'label' => 'Disciplines',  //Display name
            'show_ui'           => false,
			'query_var' => true,
            'rewrite' => array(
                'slug' => 'disciplines', // This controls the base slug that will display before each term
                'with_front' => false // Don't display the category base before
            )
        )
    );
}

//Register output as taxonomy
function tandem_create_tax_output() {
    register_taxonomy(
        'output',  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
        'collaboration',    //post type name
        array(
            'hierarchical' => false,
            'label' => 'Outputs',  //Display name
            'show_ui'           => false,
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'outputs', // This controls the base slug that will display before each term
                'with_front' => false // Don't display the category base before
            )
        )
    );
}

//Register language as taxonomy
function tandem_create_tax_language() {
    register_taxonomy(
        'language',  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
        'story',    //post type name
        array(
            'hierarchical' => false,
            'label' => 'Language',  //Display name
            'show_ui' => true,
            'query_var' => true,
            'rewrite' => array(
            'slug' => 'languages', // This controls the base slug that will display before each term
            'with_front' => false // Don't display the category base before
            )
        )
    );
}

//Register Story as Post Type
function tandem_create_story() {

	// set up labels
	$labels = array(
 		'name' => 'Stories',
    	'singular_name' => 'Story',
    	'add_new' => 'Add new Story',
    	'add_new_item' => 'Add new Story',
    	'edit_item' => 'Edit Story',
    	'new_item' => 'New Story',
    	'all_items' => 'All Stories',
    	'view_item' => 'View Story',
    	'search_items' => 'Search Stories',
    	'not_found' =>  'No Stories Found',
    	'not_found_in_trash' => 'No Stories found in Trash',
    	'parent_item_colon' => '',
    	'menu_name' => 'Stories',
    );
    //register post type
	register_post_type( 'story', array(
		'labels' => $labels,
		'has_archive' => true,
		'menu_icon' => 'dashicons-book',
		'menu_position' => 2,
 		'public' => true,
		'supports' => array( 'title'),
		//'taxonomies' => array( 'category','post_tag'),
		'exclude_from_search' => false,
		'capability_type' => 'post',
		'rewrite' => array( 'slug' => 'stories' ),
		)
	);

}

//Register participant as Post Type
function tandem_create_participant() {

	// set up labels
	$labels = array(
 		'name' => 'Participants',
    	'singular_name' => 'Participant',
    	'add_new' => 'Add new participant',
    	'add_new_item' => 'Add new participant',
    	'edit_item' => 'Edit participant',
    	'new_item' => 'New participant',
    	'all_items' => 'All participants',
    	'view_item' => 'View participant',
    	'search_items' => 'Search participants',
    	'not_found' =>  'No participants Found',
    	'not_found_in_trash' => 'No participant found in Trash',
    	'parent_item_colon' => '',
    	'menu_name' => 'Participants',
    );
    //register post type
	register_post_type( 'participant', array(
		'labels' => $labels,
		'has_archive' => false,
		'menu_icon' => 'dashicons-groups',
		'menu_position' => 3,
 		'public' => true,
    	//'title','editor','author','thumbnail','excerpt','trackbacks', 'custom-fields','comments','revisions','page-attributes','post-formats'
		'supports' => array( 'title'),
		'exclude_from_search' => true,
		'capability_type' => 'post',
		'rewrite' => array( 'slug' => 'participant' ),
		)
	);
}

//Register Collaboration as Post Type
function tandem_create_collaboration() {

	// set up labels
	$labels = array(
 		'name' => 'Collaborations',
    	'singular_name' => 'Collaboration',
    	'add_new' => 'Add new Collaboration',
    	'add_new_item' => 'Add new Collaboration',
    	'edit_item' => 'Edit Collaboration',
    	'new_item' => 'New Collaboration',
    	'all_items' => 'All Collaborations',
    	'view_item' => 'View Collaboration',
    	'search_items' => 'Search Collaborations',
    	'not_found' =>  'No Collaborations Found',
    	'not_found_in_trash' => 'No Collaborations found in Trash',
    	'parent_item_colon' => '',
    	'menu_name' => 'Collaborations',
    );
    //register post type
	register_post_type( 'collaboration', array(
		'labels' => $labels,
		'has_archive' => true,
		'menu_icon' => 'dashicons-editor-paste-text',
		'menu_position' => 4,
 		'public' => true,
    	//'title','editor','author','thumbnail','excerpt','trackbacks', 'custom-fields','comments','revisions','page-attributes','post-formats'
		'supports' => array( 'title', 'thumbnail','revisions'),
		//'taxonomies' => array( 'output', 'theme', 'discipline' ),
		'exclude_from_search' => false,
		'capability_type' => 'post',
		'rewrite' => array( 'slug' => 'collaborations' ),
		)
	);

}

//Register Programme round as Post Type
function tandem_create_programme_round() {

	// set up labels
	$labels = array(
 		'name' => 'Programme rounds',
    	'singular_name' => 'Programme round',
    	'add_new' => 'Add new Programme round',
    	'add_new_item' => 'Add new Programme round',
    	'edit_item' => 'Edit Programme round',
    	'new_item' => 'New Programme round',
    	'all_items' => 'All Programme rounds',
    	'view_item' => 'View Programme round',
    	'search_items' => 'Search Programme rounds',
    	'not_found' =>  'No Programme rounds Found',
    	'not_found_in_trash' => 'No Programme rounds found in Trash',
    	'parent_item_colon' => '',
    	'menu_name' => 'Programme rounds',
    );
    //register post type
	register_post_type( 'programme_round', array(
		'labels' => $labels,
		'has_archive' => true,
		'menu_icon' => 'dashicons-chart-pie',
		'menu_position' => 5,
 		'public' => true,
    	//'title','editor','author','thumbnail','excerpt','trackbacks', 'custom-fields','comments','revisions','page-attributes','post-formats'
		'supports' => array( 'title','editor'),
		'exclude_from_search' => false,
		'capability_type' => 'post',
		'rewrite' => array( 'slug' => 'programme-rounds' ),
		)
	);
}

/* Hook taxonomy creation to init. */
add_action( 'init', 'tandem_create_tax_methodology');
add_action( 'init', 'tandem_create_tax_discipline');
add_action( 'init', 'tandem_create_tax_output');
add_action( 'init', 'tandem_create_tax_language');

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
      __( 'Collaborations', 'example-textdomain' ),
      'tandem_story_parent_meta_box',
      $post->post_type,
      'side',
      'core'
  );
}

/* Creates the meta box for projecgt. */
function tandem_add_meta_boxes_for_collaboration( $post ) {
  add_meta_box(
    'tandem-programme_round-parent',
    __( 'Programme round', 'example-textdomain' ),
		'tandem_programme_rounds_parent_meta_box',
    $post->post_type,
    'side',
    'core'
  );

}

/* Displays the meta box. */
function tandem_story_parent_meta_box( $post ) {

    $parents = get_posts(
        array(
            'post_type'   => 'collaboration',
            'orderby'     => 'title',
            'order'       => 'ASC',
            'numberposts' => -1
        )
    );

    if ( !empty( $parents ) ) {

        echo '<select name="parent_id" class="widefat">'; // !Important! Don't change the 'parent_id' name attribute.

        foreach ( $parents as $parent ) {
            printf( '<option value="%s"%s>%s</option>', esc_attr( $parent->ID ), selected( $parent->ID, $post->post_parent, false ), esc_html( $parent->post_title ) );
        }

        echo '</select>';
    } else {
   		echo 'You have first to select collaborations';
    }
}

/* Display meta box proramme rounds. */
function tandem_programme_rounds_parent_meta_box( $post ) {

    $parents = get_posts(
        array(
            'post_type'   => 'programme_round',
            'orderby'     => 'title',
            'order'       => 'ASC',
            'numberposts' => -1
        )
    );

    if ( !empty( $parents ) ) {

        echo '<select name="parent_id" class="widefat">'; // !Important! Don't change the 'parent_id' name attribute.

        foreach ( $parents as $parent ) {
            printf( '<option value="%s"%s>%s</option>', esc_attr( $parent->ID ), selected( $parent->ID, $post->post_parent, false ), esc_html( $parent->post_title ) );
        }

        echo '</select>';
    } else {
   		echo 'You have first add a programme round';
    }
}



add_action('admin_menu', 'tandem_register_custom_submenu_page');

function tandem_register_custom_submenu_page() {

	// add_submenu_page(
	// 	'edit.php?post_type=story',
	// 	'Languages',
	// 	'Languages',
	// 	'edit_posts',
	// 	'edit-tags.php?taxonomy=language&post_type=story',
	// 	false );

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

}



/**
 * Auto load our class files
 *
 * @param   string  $class  Class name
 * @return    void
 */
function tandem_auto_load( $class ) {
	static $classes = null;

	if ( $classes === null ) {
		$classes = array(
			'participant'  => TANDEM_PATH . 'includes/class_participant.php',
			'story'  => TANDEM_PATH . 'includes/class_story.php',
			'basecontroller'  => TANDEM_PATH . 'controllers/controller-base.php',
			'storycontroller'  => TANDEM_PATH . 'controllers/controller-story.php',
		);
	}

	$cn = strtolower( $class );

	if ( isset( $classes[$cn] ) ) {
		require_once( $classes[$cn] );
	}
}

if( function_exists( 'tandem_auto_load' ) ) {
	spl_autoload_register( 'tandem_auto_load' );
}
