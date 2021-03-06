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

		$acf_has_cta_story = get_post_meta( $post_id, 'has_cta_story', true );
		// Set CTA check
		if ( ! empty( $acf_has_cta_story ) ) {
			$this->container->has_cta_story = $acf_has_cta_story;
		}

		$acf_is_active = get_post_meta( $post_id, 'is_active', true );
		// Set active check
		if ( $acf_is_active ) {
			$this->container->is_active = true;
		}

		// Set editorial introduction.
		$acf_editorial_intro = get_post_meta( $post_id, 'editorial_intro', true );
		if ( ! empty( $acf_editorial_intro ) ) {
			$this->container->has_editorial_intro = true;
			$this->container->editorial_intro = new EditorialIntro( $acf_editorial_intro, 'programme_round' );
		}

		$this->retrieve_or_set_programme_round_token();

		// Set form update link
		$this->set_participant_update_form_link();
	}

	private function set_programme_round_token() {
		$post_id = $this->container->post_id;
		$token = sha1( 'token_constant' . $post_id . wp_salt() );
		update_post_meta( $post_id, 'update_token', $token );
		return $token;
	}

	private function retrieve_or_set_programme_round_token() {
		$post_id = $this->container->post_id;
		$acf_update_token = get_post_meta( $post_id, 'update_token', true );
		if ( empty( $acf_update_token ) ) {
			$acf_update_token = $this->set_programme_round_token();
		}
		return $acf_update_token;
	}

	public function get_programme_round_token() {
		$token = $this->retrieve_or_set_programme_round_token();
		if ( ! empty( $token ) ) {
			return $token;
		} else {
			return false;
		}
	}

	public function set_block_paragraph_text() {
		$acf_paragraph = get_post_meta( $this->container->post_id, 'block_paragraph_text', true );
		if ( ! empty( $acf_paragraph ) ) {
			$input = array(
				'text' => '<p>' . $acf_paragraph . '</p>',
			);
			$this->container->block_paragraph_text = BasePattern::pattern_factory( $input, 'paragraph', '', 'emphasisblock', false );
		}

	}

	public function set_cta_colour() {
		$acf_cta_colour = get_post_meta( $this->container->post_id, 'cta_colour', true );
		if ( ! empty( $acf_cta_colour ) ) {
			$this->container->cta_colour = $acf_cta_colour;
		}
	}

	public function get_collaborations() {
		$args = array(
			'post_parent' => $this->container->post_id,
			'post_type' => 'collaboration',
			'numberposts' => 25,
			'post_status' => 'publish',
		);
		return get_children( $args );
	}

	protected function set_participant_update_form_link() {
		$post_id = $this->container->post_id;
		$link = get_post_meta( $post_id, 'participant_update_form_link', true );
		if ( ! empty( $link ) ) {
			$this->container->set_update_form_link( $link );
		}
	}

}
