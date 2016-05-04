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

/**
 * Create link (or simply an opening tag)
 *
 * @param object Exchange object to link to.
 * @param bool @with_text Optional. Add object title as link text, or simply open tag.

 * @return string Anchor tag with appropriate attributes and / or title.
 */
function exchange_create_link( $obj, $with_text = true ) {
	if ( $obj instanceof Exchange ) {
		$url = $obj->link;
		$title = $obj->title;
	}
	if ( ! empty( $url ) && ! empty( $title ) ) {
		$output = '<a href="' . $url . '" title="' .sprintf( esc_html__( 'Navigate to %s', EXCHANGE_PLUGIN ), esc_attr( $title ) ).'">';
		if ( $with_text ) {
			$output .= $title . '</a>';
		}
		return $output;
	} else {
		return false;
	}
}
