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


function tandem_hex_to_slug( $hex ) {
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

function tandem_create_link( $obj ) {
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
