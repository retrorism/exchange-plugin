<?php
/**
 * Registring GLOBALS
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

if ( ! isset( $GLOBALS['EXCHANGE_PLUGIN_CONFIG'] ) ) {
	$GLOBALS['EXCHANGE_PLUGIN_CONFIG'] = array(
		'COLOURS' => array(
			'yellow-tandem' => 'f4c522', /* Tandem styleguide */
			'black-tandem'  => '4c4d53', /* Tandem styleguide */
			'white'         => 'fefefe',
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
			'blue-4'        => '1f588e',
		),
		'IMAGES' => array(
			/* 'hq-norm' => 381024,  756 * 504 */
			'hq-norm'       => 345600, /* 720 * 480 */
			'size-in-story' => 'medium-large',
			'fallback_image_att_id' => 970,
		),
		'TAXONOMIES' => array(
			// Parent programmes (not taxonomized for overall ease ).
			'programmes' => array(
				'C_P'     => 'Tandem Community & Participation',
				'Europe'  => 'Tandem Europe',
				'Shaml'   => 'Tandem Shaml',
				'Turkey'  => 'Tandem Turkey',
				'Ukraine' => 'Tandem Ukraine',
			),
			// Priority in taxonomy types, listed by ACF Field label.
			'display_priority_story' => array(
				1 => 'tandem',
				2 => 'topic',
				3 => 'location',
			),
			'display_priority_collaboration' => array(
				1 => 'topic',
				2 => 'location',
				3 => 'discipline',
				4 => 'methodology',
				5 => 'output',
			),
			// Maximum number of tags on grid items
			'grid_tax_max' => 3,
		),
	);
}

/*
* Source: http://support.advancedcustomfields.com/forums/topic/customise-color-picker-swatches/
*/

// Adds client's custom colors to WYSIWYG editor and ACF color picker.

if ( empty( $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['COLOUR_PICKERS']['bg'] ) ) {
  if ( !empty ( $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['COLOURS'] ) ) {
      $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['COLOUR_PICKERS']['bg'] = array(
        $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['COLOURS']['salmon-1-web'],
        $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['COLOURS']['yellow-1-web'],
        $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['COLOURS']['blue-1-web'],
        $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['COLOURS']['white'],
      );
  }
}

if ( empty( $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['COLOUR_PICKERS']['accents'] ) ) {
  if ( !empty( $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['COLOURS'] ) ) {
      $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['COLOUR_PICKERS']['accents'] = array(
        $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['COLOURS']['salmon-4'],
        $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['COLOURS']['yellow-3'],
        $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['COLOURS']['blue-4'],
        $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['COLOURS']['yellow-tandem'],
		$GLOBALS['EXCHANGE_PLUGIN_CONFIG']['COLOURS']['black-tandem'],
      );
  }
}

if ( empty( $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['COLOUR_PICKERS']['boxes'] ) ) {
  if ( !empty( $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['COLOURS'] ) ) {
      $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['COLOUR_PICKERS']['boxes'] = array(
        $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['COLOURS']['salmon-1-web'],
        $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['COLOURS']['yellow-1-web'],
        $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['COLOURS']['rose-1-web'],
		$GLOBALS['EXCHANGE_PLUGIN_CONFIG']['COLOURS']['white'],


      );
  }
}
