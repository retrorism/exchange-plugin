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
class BaseController {

	/**
	 * Set properties that need to be available for all content types and
	 * can be mapped directly depend on the WP_Post.
	 *
	 * @since 0.1.0
	 * @access protected
	 * @param object $exchange Exchange content object;
	 * @param object $post WP_post object to be mapped;
	 *
	 * @throws Exception when this is not the right content type.
	 * @TODO Fix name for programme rounds
	 **/
	public function map_basics( $exchange, $post ) {

		// Throw Exception when the input is not a valid exchange object.
		if ( ! BaseController::is_correct_content_type( $post, strtolower( get_class( $exchange ) ) ) ) {
			unset( $exchange );
			throw new Exception( 'This is not a valid post' );
		}

		$post_id = $post->ID;

		// Set Post ID.
		$exchange->post_id = $post_id;

		// Set Published date.
		$exchange->date = $post->post_date;

		// Set title.
		$exchange->title = $post->post_title;

		// Set post_type.
		$exchange->type = $post->post_type;

		// Set permalink.
		$exchange->link = get_permalink( $post );
	}

	/**
	 * Checks the post type against a list of appropriate post types.
	 *
	 * Prevents the creation of grid items from non-content post types.
	 * @access public
	 * @param WP_Post $post WP_Post types passed in function.
	 * @param string $type Optional. Class name to be checked against.
	 * @return content type, if the post is right for content creation.
	 **/
	public static function is_correct_content_type( $post, $type = null ) {
		if ( 'WP_Post' === get_class( $post ) ) {
			$allowed_types = array( 'story', 'collaboration', 'programme_round', 'page', 'grid_breaker' );
			if ( in_array( $post->post_type, $allowed_types, true ) ) {
				if ( $post->post_type === $type || null === $type ) {
					return $post->post_type;
				}
			}
		}
	}

	/**
	 * Retrieves featured image to story for use in grids.
	 *
	 * @param string $acf_header_image Advanced Custom Fields Header selection option
	 * @param integer $post_id.
	 * @return Image object or null
	 **/
	protected function get_featured_image( $post_id ) {
		$thumb = acf_get_attachment( get_post_thumbnail_id( $post_id ) );
		if ( is_array( $thumb ) ) {
			return new Image( $thumb, '', array(
				'context' => 'grid',
			) );
		} else {
			return null;
		}
	}

	/**
	 * Attaches featured image to content for use in grids.
	 *
	 * @param string $acf_header_image Advanced Custom Fields Header selection option
	 * @param object $exchange Content type to attach featured image to.
	 * @param integer $post_id.
	 **/
	 public function set_featured_image( $exchange, $post_id ) {
		 $image = $this->get_featured_image( $post_id );
		 if ( is_object( $image ) ) {
			 $exchange->has_featured_image = true;
			 $exchange->featured_image = $image;
		 }
	 }



	/**
	 * Sets related content grid.
	 *
	 * Taking an array of objects from ACF field input, this function sets the related content grid object.
	 *
	 * @param object $exchange Exchange Content Type
	 * @param array $related_content
	 *
	 * @throws Exception when there are no items to put in the grid.
	 **/
	protected function set_related_content_grid( $exchange, $related_content ) {
		$grid_content = array();
		// Store post ID in the unique array so that it won't get added.
		$unique_ids = array( $exchange->post_id );
		foreach ( $related_content as $item ) {
			// Tests for WP_Post content types.
			if ( BaseController::is_correct_content_type( $item ) ) {
				// Tests if the items are unique and don't refer to the post itself.
				if ( ! in_array( $item->ID, $unique_ids, true ) ) {
					$grid_content[] = $item;
				}
			}
		}
		if ( count( $related_content ) > 0 ) {
			$exchange->has_related_content = true;
			$grid = new RelatedGrid( $grid_content );
			$exchange->related_content = $grid;
		}
	}
}
