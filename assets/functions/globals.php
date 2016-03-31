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
