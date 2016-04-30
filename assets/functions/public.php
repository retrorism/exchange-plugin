<?php
/**
 * Public functions
 *
 * Contains public facing action hooks (for use in templates and patterns)
 *
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
};

add_action('init','exchange_add_timber_views_path');

/**
 * Setup locations for Timber views.
 *
 * As described here: https://github.com/timber/timber/issues/248
 *
 * @return void
 */
function exchange_add_timber_views_path() {
	Timber::$locations = array(
		get_stylesheet_directory() . '/views',
		get_template_directory() . '/views',
		get_stylesheet_directory() . '/widgetviews',
		get_template_directory() . '/widgetviews',
		EXCHANGE_PLUGIN_PATH . '/views',
	);
}

function exchange_hex_to_slug( $hex ) {
	$color_array = $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['COLOURS'];
	$clean_hex = str_replace( '#','',strtolower( $hex ) );
	if ( is_array( $color_array ) && in_array( $clean_hex,$color_array ) ){
		$retval = array_keys( $color_array,$clean_hex,TRUE );
		if ( ! empty( $retval ) ) {
		  return $retval[0];
		}
	}
	return 'default';
}

function exchange_create_link( $obj ) {
	if ( BaseController::is_correct_content_type( $obj ) ) {
		$url = $obj->link;
		$title = $obj->title;
	}
	if ( ! empty( $url ) && ! empty( $title ) ) {
		$output = '<a href="' . $url . '" title="' .sprintf( esc_html__( 'Navigate to %s', EXCHANGE_PLUGIN ), esc_attr( $title ) ).'">' . $title . '</a>';
		return $output;
	} else {
		return false;
	}
}
