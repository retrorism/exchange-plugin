<?php
/**
 * Base Controller Class
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 11/2/2016
 *
 * @package Exchange Plugin
 *
 * @link Via http://stackoverflow.com/questions/8091143/how-to-check-for-a-specific-type-of-object-in-php
 **/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
};

/**
 * Base Controller.
 *
 * This class contains all common controller logic. Individual controllers will be called through Dependency Injection
 *
 * @since 0.1.0
 **/
abstract class BaseController {

	/**
	 * Sets related content grid
	 *
	 * Taking an array of post objects in the related_content property, this function
	 * returns a grid object with grid items.
	 *
	 * @param type var Description
	 * @return {11:return type}
	 *
	 * @throws Exception when there are no items to put in the grid.
	 */
	protected function set_related_content_grid( $related_content ) {
		if ( is_array( $related_content ) && count( $related_content ) > 0 ) {
			return new RelatedGrid( $related_content );
		} else {
			throw new Exception(__( 'This is not valid grid content', EXCHANGE_PLUGIN ) );
		}
	}

	/**
	 * Checks the post type against a list of appropriate post types.
	 *
	 * Prevents the creation of grid items from non-content post types.
	 *
	 * @param WP_Post $post WP_Post types passed in function.
	 * @return bool True if the post is right for content creation.
	 */
	protected function is_correct_content_type( $post ) {
		if ( 'WP_Post' === get_class( $post ) ) {
			$allowed_types = array('story','collaboration','programme_round','page','grid_breaker');
			$type = get_post_type( $post );
			if ( in_array( $type, $allowed_types, true ) ) {
				return true;
			}
			else {
				return false;
			}
		} else {
			return false;
		}
	}

}
