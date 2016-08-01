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
 *
 * TODO https://codex.wordpress.org/Rewrite_API/add_rewrite_rule
 **/
class Programme_RoundController extends BaseController {

	public function map_programme_round_basics() {

		$post_id = $this->container->post_id;

		$acf_has_cta = get_field( 'has_cta', $post_id );
		// Set CTA check
		if ( ! empty( $acf_has_cta ) ) {
			$this->container->has_cta = $acf_has_cta;
		}

		$acf_is_active = get_field( 'is_active', $post_id );
		// Set active check
		if ( $acf_is_active ) {
			$this->container->is_active = true;
		}

		// Set editorial introduction.
		$acf_editorial_intro = get_field( 'editorial_intro', $post_id );
		if ( ! empty( $acf_editorial_intro ) ) {
			$this->container->has_editorial_intro = true;
			$this->container->editorial_intro = new EditorialIntro( $acf_editorial_intro, 'programme_round' );
		}
	}
}
