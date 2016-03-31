<?php
/**
 * Functions for taxonomy creation
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
add_action( 'init', 'tandem_create_tax_methodology' );
add_action( 'init', 'tandem_create_tax_discipline' );
add_action( 'init', 'tandem_create_tax_output' );
add_action( 'init', 'tandem_create_tax_language' );
add_action( 'init', 'tandem_create_tax_location' );
add_action( 'init', 'tandem_create_tax_topic' );

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
			'label'        => 'Languages',  // Display name.
			'show_ui'      => true,
			'query_var'    => true,
			'rewrite'      => array(
				'slug'       => 'languages', // This controls the base slug that will display before each term.
				'with_front' => false, // Don't display the category base before.
			),
			'labels'       => array(
				'add_new_item' => 'Add new language tag',
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
