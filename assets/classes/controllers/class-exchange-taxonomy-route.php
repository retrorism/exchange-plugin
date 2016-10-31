<?php
/**
 * Exchange Taxonomy Route Class
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 30/08/2016
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
 * Controller for routing taxonomy queries
 *
 * @since 0.1.0
 **/
class Exchange_Taxonomy_Route extends WP_REST_Controller {

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		$version = '1';
		$namespace = 'exchange/v' . $version;
		$base = '(?P<taxonomy>tandem_tag|post_tag|language|location|project_output|discipline|methodology|topic)/(?P<slug>[a-zA-Z0-9-]+)';
		register_rest_route( $namespace, '/' . $base, array(
			array(
				'methods'         => WP_REST_Server::READABLE,
				'callback'        => array( $this, 'get_items' ),
				'args'            => $this->get_collection_params(),
			),
		) );
	}

	/**
	 * Get a collection of items
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		$args = array(
			'post_type' => array(
				'page',
				'story',
				'collaboration',
			),
			'posts_per_page' => $request['per_page'],
			'paged'          => $request['page'],
			'tax_query'      => array(
				array(
					'taxonomy' => $request['taxonomy'],
					'field'    => 'slug',
					'terms'    => $request['slug'],
				),
			),
		);
		$tax_query = new WP_Query( $args );
		if ( ! empty( $tax_query->posts ) ) {
			$results = array();
			$posts = $tax_query->posts;
			foreach ( $posts as $post ) {
				$exchange = BaseController::exchange_factory( $post->ID );
				$griditem = BaseGrid::create_grid_item_from_post( $post->ID,'archive__grid',$grid_mods );
				$griditem_html = $griditem->embed();
				$results[] = array(
					'exchange_basics' => array(
						'data' => $exchange,
						'griditem' => $griditem_html,
					),
				);
			}
		    return new WP_REST_Response( $results, 200 );
		} else {
			return new WP_Error( 'code', __( 'I could not find anything', EXCHANGE_PLUGIN ) );
		}
	}

	/**
	 * Prepare the item for the REST response
	 *
	 * @param mixed $item WordPress representation of the item.
	 * @param WP_REST_Request $request Request object.
	 * @return mixed
	 */
	public function prepare_item_for_response( $item, $request ) {
		return array();
	}

	/**
	 * Get the query params for collections
	 *
	 * @return array
	 */
	public function get_collection_params() {
		return array(
			'page'                   => array(
				'description'        => 'Current page of the collection.',
				'type'               => 'integer',
				'default'            => 1,
				'sanitize_callback'  => 'absint',
			),
			'per_page'               => array(
				'description'        => 'Maximum number of items to be returned in result set.',
				'type'               => 'integer',
				'default'            => get_option('posts_per_page'),
				'sanitize_callback'  => 'absint',
			),
			'search'                 => array(
				'description'        => 'Limit results to those matching a string.',
				'type'               => 'string',
				'sanitize_callback'  => 'sanitize_text_field',
			),
		);
	}
}
