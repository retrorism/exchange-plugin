<?php
/**
 * Functions for taxonomy creation.
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
 * Verify token and return programme_round;
 *
 * @return integer $pr;
 */
function exchange_verify_token() {
	$token = get_query_var( 'pr' );
	if ( empty( $token ) ) {
		return;
	}
	$token_store = get_transient( 'tandem_pr_token_store' );
	$pr_tokens = ! empty( $token_store ) ? $token_store : exchange_retrieve_and_store_pr_tokens();
	if ( empty( $pr_tokens ) ) {
		return;
	}
	$pr = array_search( $token, $pr_tokens );
	if ( ! $pr ) {
		$token_store_renew = exchange_retrieve_and_store_pr_tokens();
		if ( ! empty( $token_store_renew ) ) {
			$pr = array_search( $token, $token_store );
		}
	}
	if ( $pr ) {
		return $pr;
	}
}

function exchange_build_token_form_cta( $pr_obj, $type ) {
	$block = $pr_obj->create_token_form_cta( $type );
	if ( $block instanceof BasePattern ) {
		$griditem = new GridItem( $block, 'token', $grid_mods );
		$griditem->publish();
	}
}

function exchange_retrieve_and_store_pr_tokens() {
	$programme_rounds = BaseController::get_all_from_type( 'programme_round', 'ids' );
	if ( empty( $programme_rounds ) ) {
		return;
	}
	$prepare_store = array();
	foreach ( $programme_rounds as $p ) {
		$token = get_post_meta( $p,'update_token',true );
		if ( ! empty( $token ) && is_string( $token ) ) {
			$prepare_store[$p] = $token;
		}
	}
	if ( ! empty( $prepare_store ) ) {
		set_transient( 'tandem_pr_token_store', $prepare_store, 12 * HOUR_IN_SECONDS );
		return $prepare_store;
	}
}

function exchange_token_form_callback() {
	$results = '';
	$ajax_check = check_ajax_referer( 'exchange-token-form-nonce', 'security', false );
	if ( empty( $ajax_check ) ) {
		echo '<div class="loader-pointer section__helper">' . __( 'Whoa... where did YOU come from?', EXCHANGE_PLUGIN ) . '</div>';
		wp_die();
	}
	if ( ! empty( $_POST['prid'] ) && ! empty( $_POST['update_id'] ) && ! empty( $_POST['token'] ) ) {
		$pr_obj = BaseController::exchange_factory( $_POST['prid'], 'token-form' );
		$pr_token = $_POST['token'];
		$c_obj = BaseController::exchange_factory( $_POST['update_id'], 'token-form' );
		$story_page = get_option( 'options_story_update_form_page' );
		$s_obj = get_post( $story_page );

		if ( ! $pr_obj instanceof Programme_Round ) {
			echo '<div class="loader-pointer section__helper">' . __( 'We could not find the right data. Are you sure you have the right link?' ) . '</div>';
			wp_die();
		}
		// Create array for griditems.
		$simplegrid = new SimpleGrid();

		if ( $s_obj instanceof WP_Post ) {
			$simplegrid->add_grid_item( $pr_obj->create_token_form_cta( $s_obj, $pr_token, $c_obj ) );
		}
		if ( $c_obj instanceof Collaboration ) {
			$simplegrid->add_grid_item( $pr_obj->create_token_form_cta( $c_obj, $pr_token  ) );
		}
		if ( ! empty( $c_obj->participants ) ) {
			foreach ( $c_obj->participants as $p ) {
				$simplegrid->add_grid_item( $pr_obj->create_token_form_cta( $p, $pr_token ) );
			}
		}
		// echo '<pre>' . print_r( $simplegrid, true ) . '</pre>';
		// wp_die();

		echo $simplegrid->embed();
		wp_die();
	} else {
		echo '<div class="loader-pointer section__helper">' . __( 'We did not receive the request correctly. Make sure you\'ve selected your collaboration and try again!' ) . '</div>';
		wp_die();
	}
}

add_action( 'wp_ajax_exchange_token_form', 'exchange_token_form_callback' );
add_action( 'wp_ajax_nopriv_exchange_token_form', 'exchange_token_form_callback' );

function exchange_url_rewrite_templates_for_tokens() {
	if ( get_query_var( 'pr' ) ) {
		add_filter( 'template_include', function() {
			$archive_template = locate_template( array( 'page-token.php' ) );
			if ( '' !== $archive_template ) {
				return $archive_template;
			}
			return $archive_template;
		});
	}
}

add_action( 'template_redirect', 'exchange_url_rewrite_templates_for_tokens' );

function exchange_token_query_vars( $qvars ) {
	$qvars[] = 'pr';
	$qvars[] = 'pr_ref';
	$qvars[] = 'update_id';
	return $qvars;
}
add_filter( 'query_vars', 'exchange_token_query_vars' , 10, 1 );
