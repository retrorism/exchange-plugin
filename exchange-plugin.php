<?php
/**
 * @package Exchange-Plugin
 * @version 0.1
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




//Register theme as taxomy
function tandem_create_tax_topic() {
    register_taxonomy(
        'topic',  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
        array('project','story'),        //post type name
        array(
            'hierarchical' => false,
            'label' => 'Topics',  //Display name
            'show_ui'           => false,
			'query_var' => true,
            'rewrite' => array(
                'slug' => 'topics', // This controls the base slug that will display before each term
                'with_front' => false // Don't display the category base before
            )
        )
    );
}
add_action( 'init', 'tandem_create_tax_topic');


//Register methodologies as taxomy
function tandem_create_tax_methodology() {
    register_taxonomy(
        'methodology',  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
        array('project'),        //post type name
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
add_action( 'init', 'tandem_create_tax_methodology');


//Register discipline as taxomy
function tandem_create_tax_discipline() {
    register_taxonomy(
        'discipline',  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
        array('project'),    //post type name
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
add_action( 'init', 'tandem_create_tax_discipline');

//Register output as taxomy
function tandem_create_tax_output() {
    register_taxonomy(
        'output',  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
        'project',    //post type name
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
add_action( 'init', 'tandem_create_tax_output');

//Register language as taxomy
function tandem_create_tax_language() {
    register_taxonomy(
        'language',  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
        'story',    //post type name
        array(
            'hierarchical' => false,
            'label' => 'Language',  //Display name
            'show_ui'           => false,
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'languages', // This controls the base slug that will display before each term
                'with_front' => false // Don't display the category base before
            )
        )
    );
}
add_action( 'init', 'tandem_create_tax_language');



//Register programme_round as Post Type
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
 		'public' => true,
    	//'title','editor','author','thumbnail','excerpt','trackbacks', 'custom-fields','comments','revisions','page-attributes','post-formats'
		'supports' => array( 'title','editor'),
		'exclude_from_search' => false,
		'capability_type' => 'post',
		'rewrite' => array( 'slug' => 'programme-rounds' ),
		)
	);
}

add_action( 'init', 'tandem_create_programme_round' );


//Register Project as Post Type
function tandem_create_project() {

	// set up labels
	$labels = array(
 		'name' => 'Projects',
    	'singular_name' => 'Project',
    	'add_new' => 'Add new Project',
    	'add_new_item' => 'Add new Project',
    	'edit_item' => 'Edit Project',
    	'new_item' => 'New Project',
    	'all_items' => 'All Projects',
    	'view_item' => 'View Project',
    	'search_items' => 'Search Projects',
    	'not_found' =>  'No Projects Found',
    	'not_found_in_trash' => 'No Projects found in Trash',
    	'parent_item_colon' => '',
    	'menu_name' => 'Projects',
    );
    //register post type
	register_post_type( 'project', array(
		'labels' => $labels,
		'has_archive' => true,
		'menu_icon' => 'dashicons-editor-paste-text',
 		'public' => true,
    	//'title','editor','author','thumbnail','excerpt','trackbacks', 'custom-fields','comments','revisions','page-attributes','post-formats'
		'supports' => array( 'title', 'thumbnail','revisions'),
		//'taxonomies' => array( 'output', 'theme', 'discipline' ),
		'exclude_from_search' => false,
		'capability_type' => 'post',
		'rewrite' => array( 'slug' => 'projects' ),
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
		'has_archive' => true,
		'menu_icon' => 'dashicons-groups',
 		'public' => true,
    	//'title','editor','author','thumbnail','excerpt','trackbacks', 'custom-fields','comments','revisions','page-attributes','post-formats'
		'supports' => array( 'title'),
		'exclude_from_search' => false,
		'capability_type' => 'post',
		'rewrite' => array( 'slug' => 'participant' ),
		)
	);
}

add_action( 'init', 'tandem_create_participant' );



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
 		'public' => true,
		'supports' => array( 'title'),
		//'taxonomies' => array( 'category','post_tag'),
		'exclude_from_search' => false,
		'capability_type' => 'post',
		'rewrite' => array( 'slug' => 'stories' ),
		)
	);


}


add_action( 'init', 'tandem_create_project' );

add_action( 'init', 'tandem_create_story' );



/* Hook meta box to just the 'story' post type. */
add_action( 'add_meta_boxes_story', 'tandem_add_meta_boxes_for_story' );


/* Hook meta box to just the 'story' post type. */
add_action( 'add_meta_boxes_project', 'tandem_add_meta_boxes_for_project' );

/* Creates  meta box for a story. */
function tandem_add_meta_boxes_for_story( $post ) {

    add_meta_box(
        'tandem-story-parent',
        __( 'Projects', 'example-textdomain' ),
        'tandem_story_parent_meta_box',
        $post->post_type,
        'side',
        'core'
    );

}

/* Creates the meta box for projecgt. */
function tandem_add_meta_boxes_for_project( $post ) {


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
            'post_type'   => 'project',
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
   		echo 'You have first to select projects';
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

	add_submenu_page(
		'edit.php?post_type=story',
		'Languages',
		'Languages',
		'edit_posts',
		'edit-tags.php?taxonomy=language&post_type=story',
		false );

	add_submenu_page(
		'edit.php?post_type=project',
		'Topics',
		'Topics',
		'edit_posts',
		'edit-tags.php?taxonomy=topic&post_type=project',
		false );

	add_submenu_page(
		'edit.php?post_type=project',
		'Disciplines',
		'Disciplines',
		'edit_posts',
		'edit-tags.php?taxonomy=discipline&post_type=project',
		false );

	add_submenu_page(
		'edit.php?post_type=project',
		'Methodologies',
		'Methodologies',
		'edit_posts',
		'edit-tags.php?taxonomy=methodologies&post_type=project',
		false );

	add_submenu_page(
		'edit.php?post_type=project',
		'Outputs',
		'Outputs',
		'edit_posts',
		'edit-tags.php?taxonomy=output&post_type=project',
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
