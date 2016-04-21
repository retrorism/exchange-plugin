<?php
/**
 * Programme Round Controller
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

/**
 * Programme Round Controller class
 *
 * This controller contains all collaboration logic
 *
 * @since 0.1.0
 **/
class Programme_RoundController extends BaseController {

	public function map_programme_round_full( $programme_round, $post_obj ) {

		$post_id = $post_obj->ID;

		if ( ! ( $post_id >= 1 ) || ! ( 'programme_round' === $post_obj->post_type  ) ) {
			unset( $programme_round );
			throw new Exception( 'This is not a valid programme round' );
		}

		// Dump ACF variables.
		$acf = get_fields( $post_id );
		$link = get_permalink( $post_id );

		if ( !empty( $postobj->post_title ) ) {
			$programme_round->title = $postobj->post_title;
		}

		if ( !empty( $link ) ) {
			$programme_round->link = $link;
		}
	}

}
