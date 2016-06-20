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


function exchange_hex_to_slug( $hex ) {
	$color_array = $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['COLOURS'];
	$clean_hex = str_replace( '#','',strtolower( $hex ) );
	if ( is_array( $color_array ) && in_array( $clean_hex,$color_array ) ){
		$retval = array_keys( $color_array,$clean_hex,TRUE );
		if ( ! empty( $retval ) ) {
		  return $retval[0];
		}
	}
	return 'custom-colour';
}


/**
 * Pick black or white to contrast with chosen hexcolor
 *
 * @param string $hex Colour value

 * @return string black or white
 */
function exchange_get_contrast_YIQ( $hex ) {
	$r = hexdec( substr( $hex,0,2 ) );
	$g = hexdec( substr( $hex,2,2 ) );
	$b = hexdec( substr( $hex,4,2 ) );
	$yiq = ( ( $r*299 )+( $g*587 )+( $b*114 ) )/1000;
	return ( $yiq >= 128 ) ? 'white' : 'black';
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
		$output  = '<a href="' . $url . '" title="' .sprintf( esc_html__( 'Navigate to %s', EXCHANGE_PLUGIN ), esc_attr( $title ) ).'">';
		if ( $with_text ) {
			$output .= $title . '</a>';
		}
		return $output;
	} else {
		return false;
	}
}

/**
 * Get focus points for this image
 *
 * Add modifiers array with data properties if a focus point has been set.
 *
 * @param array $thumb ACF Image array
 * @return array $focus_points;
 */
function exchange_get_focus_points( $thumb ) {
	if ( ! class_exists('TstPostOptions') ) {
		return;
	}
	$focus_position = get_post_meta( $thumb['ID'], 'theiaSmartThumbnails_position', false );
	if ( empty( $focus_position ) ) {
		return;
	}
	$h = is_array( $focus_position[0] ) ? $focus_position[0][1] : null;
	$w = is_array( $focus_position[0] ) ? $focus_position[0][0] : null;
	if ( ! empty( $h ) && ! empty( $w ) ) {
		$focus_points['focus_h'] = $h;
		$focus_points['focus_w'] = $w;
		return $focus_points;
	}
}

/**
 * Determines if a post, identified by the specified ID, exist
 * within the WordPress database.
 *
 * Note that this function uses the 'acme_' prefix to serve as an
 * example for how to use the function within a theme. If this were
 * to be within a class, then the prefix would not be necessary.
 * Via: https://tommcfarlin.com/wordpress-post-exists-by-id/
 *
 * @param    int    $id    The ID of the post to check
 * @return   bool          True if the post exists; otherwise, false.
 * @since    1.0.0
 */
function exchange_post_exists( $id ) {
  return is_string( get_post_status( $id ) );
}
