<?php
/**
 * Exchange REST API functionality
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 28/08/2016
 *
 * @package Exchange Plugin
 **/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
};

add_action( 'rest_api_init', 'exchange_register_api_hooks' );

function exchange_register_api_hooks() {
    register_api_field(
        array('story','collaboration'),
        'exchange_basics',
        array(
            'get_callback'    => 'exchange_return_basics',
        )
    );
}

// Return plaintext content for posts
function exchange_return_basics( $object, $field_name, $request ) {
	$results = array();
	$grid_mods = array(
		'grid_width' => 3,
	);
	$results['data'] = BaseController::exchange_factory( $object['id'] );
	$griditem = BaseGrid::create_grid_item_from_post( $object['id'],'archive__grid',$grid_mods );
	$griditem_html = $griditem->embed();
	$results['griditem'] = $griditem_html;
    return $results;
}


add_action( 'rest_api_init', 'exchange_register_taxonomy_route' );

function exchange_register_taxonomy_route() {
	$route = new Exchange_Taxonomy_Route();
	$route->register_routes();
}

function exchange_create_archive_button() {
	global $wp_query;
	if ( 1 == exchange_get_max_num_pages( $wp_query ) ) {
		return;
	}
	$button_input = array(
		'button_text' => __( 'Load more...',EXCHANGE_PLUGIN ),
	);
	$button_mods = array(
		'link' => '#',
	);
	$button = new Button( $button_input,'archive',$button_mods );
	$button->classes[] = 'button--large';
	$params = exchange_get_query_params( $wp_query );
	foreach ( $params as $k => $v ) {
		$button->set_attribute( 'data', $k, $v );
	}
	$button->publish();
}

function exchange_get_query_params( $wp_query ) {
	$query_params = array(
		'paged' => exchange_get_paged_number( $wp_query ),
		'max_num_pages' => exchange_get_max_num_pages( $wp_query ),
		'object' => exchange_get_queried_object_slug( $wp_query ),
		'posts_per_page' => get_option('posts_per_page'),
	);
	$tax_query_param = exchange_tax_query( $wp_query );
	if ( ! empty( $tax_query_param ) ) {
		foreach( $tax_query_param as $tax ) {
			if ( ! empty( $tax['terms'] ) ) {
				$query_params['tax_query'] = implode( ',', $tax['terms'] );
			}
		}
	}
	return $query_params;
}

function exchange_get_paged_number( $wp_query ) {
	if ( isset( $wp_query->query_vars['paged'] ) ) {
		return $wp_query->query_vars['paged'];
	}
}

function exchange_get_max_num_pages( $wp_query ) {
	if ( isset( $wp_query->max_num_pages ) ) {
		return $wp_query->max_num_pages;
	}
}

function exchange_get_queried_object_slug( $wp_query ) {
	if ( ! isset ( $wp_query->queried_object ) ) {
		return;
	}
	if ( $wp_query->queried_object instanceof WP_Post_Type ) {
		return $wp_query->queried_object->rest_base;
	} elseif ( $wp_query->queried_object instanceof WP_Term ) {
		return $wp_query->queried_object->taxonomy;
	}
}

function exchange_tax_query( $wp_query ) {
	if ( ! empty( $wp_query->tax_query->queried_terms ) ) {
		return $wp_query->tax_query->queried_terms;
	}
}
