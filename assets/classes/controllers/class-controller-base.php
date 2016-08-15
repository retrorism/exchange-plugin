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
	 * Container - reference to the Exchange object that has instantiated this controller.
	 *
	 * @access protected
	 * @var object $container This controller's container.
	 */
	protected $container;

	/**
	 * Attaches a reference to the instantiating object.
	 *
	 * @access public
	 * @param object (reference);
	 * @return void
	 **/
	public function set_container( $object ) {
		if ( is_subclass_of( $object, 'Exchange', false ) ) {
			$this->container = &$object;
		}
	}

	/**
	 * Checks the post type against a list of appropriate post types.
	 *
	 * Prevents the creation of grid items from non-content post types.
	 * @access public
	 * @param WP_Post $post_object WP_Post types passed in function.
	 * @param string $type Optional. Class name to be checked against.
	 * @return content type, if the post is right for content creation.
	 **/
	public static function is_correct_content_type( $post_id_or_object, $type = null ) {
		if ( is_numeric( $post_id_or_object ) && $post_id_or_object > 0 ) {
			$post_id_or_object = get_post( $post_id_or_object );
		}
		if ( ! is_object( $post_id_or_object ) ) {
			return;
		}
		if ( 'WP_Post' != get_class( $post_id_or_object ) ) {
			return;
		}
		$allowed_types = array(
			'story'           => 'story',
			'page'            => 'story',
			'programme_round' => 'programme_round',
			'collaboration'   => 'collaboration',
			'participant'     => 'participant',
		);
		if ( ! array_key_exists( $post_id_or_object->post_type, $allowed_types ) ) {
			return;
		}
		$content_type = $post_id_or_object->post_type;
		if ( $allowed_types[ $content_type ] === $type || null === $type ) {
			return $content_type;
		}
	}

	/**
	 * Returns an Exchange class object based upon post type.
	 *
	 * @access public
	 * @param WP_Post $post_id_or_object WP_Post types / IDs passed in function.
	 * @param string $context Optional. Context in which the object will be instantiated.
	 *
	 * @throws Exception when wrong post type is supplied.
	 **/
	public static function exchange_factory( $post_id_or_object, $context = '', $check_for_type = null ) {
		if ( is_numeric( $post_id_or_object ) && $post_id_or_object > 0 ) {
			$post_id_or_object = get_post( $post_id_or_object );
		}
		$type = self::is_correct_content_type( $post_id_or_object, $check_for_type );
		if ( empty( $type ) ) {
			throw new Exception( __( 'The factory disagrees: type = ' ) . $type );
		}
		$args = array( $post_id_or_object, $context );
		switch ( $type ) {
			case 'collaboration':
				return new Collaboration( ...$args );
			case 'programme_round':
				return new Programme_Round( ...$args );
			case 'participant':
				return new Participant( ...$args );
			case 'story':
			case 'page':
			default:
				return new Story( ...$args );
		}
	}

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
	 **/
	public function map_basics( $exchange, $post ) {
		// Check if the post and the newly created CPT object are of the same type
		$class_lower = strtolower( get_class( $exchange ) );
		if ( empty( self::is_correct_content_type( $post, $class_lower ) ) ) {
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

		if ( 'collaboration' === $exchange->type && $post->post_parent >= 1 ) {
			$exchange->controller->set_programme_round( $post->post_parent );
		}

		if ( 'programme_round' === $exchange->type ) {
			 $slug = sanitize_title( $exchange->title );
			 //$exchange->term = get_term_by( 'slug', $slug, 'post_tag' ) ? $slug : null;
			 $exchange->term = $slug;
		}

		// Set permalink.
		$exchange->link = get_permalink( $post );

	}

	// Store sections in Exchange object
	protected function set_sections( $acf_sections ) {
		// Loop through sections.
		foreach( $acf_sections as $s ) {
			$section_mods = array();
			if ( ! empty( $s['contents'] ) && isset( $s['contents']['acf_fc_layout'] ) ) {
				$section_mods['type'] = $s['contents']['acf_fc_layout'];
			}
			$section = new Section( $s, strtolower( get_class( $this->container ) ), $section_mods );
			if ( is_object( $section ) && is_a( $section, 'Section' ) ) {
				$this->container->sections[] = $section;
			}
		}
	}

	// // Store sections in Exchange object
	// protected function set_sections( $acf_sections ) {
	// 	// Loop through sections.
	// 	if ( empty( $acf_sections ) ) {
	// 		return;
	// 	}
	// 	for ( $i = 0; $i < $acf_sections; $i++ ) {
	// 		$section_mods = array();
	// 		$contents = get_post_meta( $this->container->post_id, 'sections_' . $i . '_contents', true );
	// 		var_dump( $contents );
	// 		throw new Exception("Testing {1:What are we testing?}");
	// 		if ( ! empty( $contents ) && isset( $contents['acf_fc_layout'] ) ) {
	// 			$section_mods['type'] = $contents['acf_fc_layout'];
	// 		}
	// 		$section = new Section( $contents, strtolower( get_class( $this->container ) ), $section_mods );
	// 		if ( is_object( $section ) && is_a( $section, 'Section' ) ) {
	// 			$this->container->sections[] = $section;
	// 		}
	// 	}
	// }


	protected function get_header_image_source() {
		return get_post_meta( 'header_image', $this->container->post_id, true );
	}

	protected function get_header_image( $context ) {
		switch ( $this->get_header_image_source( $context ) ) {
			case 'upload_new_image':
				$thumb = get_post_meta( 'upload_header_image', $this->container->post_id, true );
				break;
			case 'none':
				break;
			case 'use_featured_image':
			default:
				$thumb_id = get_post_thumbnail_id( $this->container->post_id );
				// Use ACF function to create array for Image object constructor.
				if ( ! empty( $thumb_id ) && function_exists( 'acf_get_attachment' ) ) {
					$thumb = acf_get_attachment( $thumb_id );
				}
				break;
		}
		if ( isset( $thumb ) ) {
			$focus_points = exchange_get_focus_points( $thumb );
			$image_mods = array();
			if ( ! empty( $focus_points ) ) {
				$image_mods['data'] = $focus_points;
				$image_mods['classes'] = array('focus');
			}
			if ( $this->container->type === 'collaboration' ) {
				if ( $this->container->has_participants && count( $this->container->participants ) > 2 || $this->container->description_length > 100 ) {
					$image_mods['style'] = 'tridem_or_more';
				}
			}
			return new Image( $thumb, $context, $image_mods );
		}
	}

	/**
	 * Attaches header image to story or collab
	 *
	 * @param string $acf_header_image Advanced Custom Fields Header selection option
	 * @param integer $post_id.
	 * @return HeaderImage object or null
	 */
	protected function set_header_image( $context = '' ) {
		$image = $this->get_header_image( $context );
		if ( is_object( $image ) && is_a($image, 'Image') ) {
			$this->container->header_image = $image;
			$this->container->has_header_image = true;
		}
	}


	/**
	 * Retrieves featured image to story (for example for use in grid views).
	 *
	 * @param integer $post_id.
	 * @return null or Image object;
	 **/
	protected function get_featured_image( $context ) {
		$thumb_props = $this->get_featured_image_props();
		$focus_points = exchange_get_focus_points( $thumb_props );
		$image_mods = array();
		if ( ! empty( $focus_points ) ) {
			$image_mods['data'] = $focus_points;
			$image_mods['classes'] = array('focus');
		}
		if ( ! empty( $thumb_props ) ) {
			return new Image( $thumb_props, $context, $image_mods );
		}
	}

	protected function get_featured_image_props() {
		$post_id = $this->container->post_id;
		$thumb_id = get_post_thumbnail_id( $post_id );
		if ( empty( $thumb_id ) && function_exists( 'acf_get_attachment' ) ) {
			$thumb_props = acf_get_attachment( $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['IMAGES']['fallback_image_att_id'] );
		} else {
			if ( 'attachment' === get_post( $thumb_id )->post_type && function_exists( 'acf_get_attachment' ) ) {
				$thumb_props = acf_get_attachment( $thumb_id );
			}
		}
		return $thumb_props;
	}

	/**
	 * Attaches featured image to content for use in grids.
	 *
	 * @param string $acf_header_image Advanced Custom Fields Header selection option
	 * @param object $exchange Content type to attach featured image to.
	 *
	 * @return void.
	 **/
	public function set_featured_image( $context = '' ) {
		$image = $this->get_featured_image( $context );
		if ( is_a( $image, 'Image' ) ) {
			$this->container->has_featured_image = true;
			$this->container->featured_image = $image;
		}
	}

	protected function get_gallery_from_attachments() {
		$attachments = get_attached_media( 'image', $this->container->post_id );
		if ( ! count( $attachments ) ) {
			return;
		}
		// Empty array to store Image objects;
		$gallery = array();
		foreach( $attachments as $attachment ) {
			$img_array = acf_get_attachment( $attachment );
			$image_mods = array();
			$focus_points = exchange_get_focus_points( $img_array );
			if ( ! empty( $focus_points ) ) {
				$image_mods['data'] = $focus_points;
				$image_mods['classes'] = array('focus');
			}
			$img_obj = new Image( $img_array, 'gallery', $image_mods );
			if ( is_object( $img_obj ) && is_a( $img_obj, 'Image') ) {
				$gallery[] = $img_obj;
			}
		}
		return $gallery;
	}

	protected function get_gallery_from_acf() {
		$gallery = get_post_meta( $this->container->type . '_gallery', $this->container->post_id, true );
		return $gallery;
	}

	protected function get_video_from_acf() {
		// Set empty array for video properties
		$input = array();
		$video = get_post_meta( $this->container->type . '_video_embed_code', $this->container->post_id, true );
		$video_caption = get_post_meta( $this->container->type . '_video_caption', $this->container->post_id, true );
		if ( empty( $video ) ) {
			return;
		}
		$input['video_embed_code'] = $video;
		if ( ! empty( $video_caption ) ) {
			$input['video_caption'] = $video_caption;
		}
		return $input;
	}

	protected function get_gallery_from_query() {
		global $wpdb;
		$rows = $wpdb->get_results( $wpdb->prepare(
			"
			SELECT *
			FROM {$wpdb->prefix}postmeta
			WHERE post_id = %s
				AND ( meta_key LIKE %s OR meta_key LIKE %s OR meta_key LIKE %s OR meta_key LIKE %s )
			",
			$this->container->post_id,
			'sections_%_story_elements_%_two_images',
			'sections_%_story_elements_%_image',
			'upload_header_image',
			'_thumbnail_id'
		));
		if ( ! $rows ) {
			return;
		}
		// Empty array for storing image ids.
		$ids = array();

		// Iterate over rows
		foreach( $rows as $row ) {
			$image_id = $row->meta_value;
			$key = preg_replace( '/_(\d)_image/i', '_0${1}_image', $row->meta_key );
			if ( empty( $image_id ) ) {
				continue;
			}
			// Single IDs
			if ( is_numeric( $image_id ) ) {
				$ids[$key] = intval( $image_id );
			} elseif ( is_string( $image_id ) ) {
				$ids_array = unserialize( $image_id );
				// Move to next row if this is not a serialized array;
				if ( !is_array( $ids_array ) || ! count( $ids_array) ) {
					continue;
				}
				foreach( $ids_array as $id ) {
					if ( is_numeric( $id ) ) {
						$ids[$key] = intval( $id );
					}
				}
			}
		}
		if ( ! count( $ids ) ) {
			return;
		}

		// Filter IDs and sort by key
		$unique_ids = array_unique( $ids );
		ksort( $unique_ids );
		return $unique_ids;
	}

	protected function set_gallery() {
		if ( $this->container->has_gallery ) {
			return;
		}
		$unique_arrs = $this->get_gallery_from_acf();
		if ( empty( $unique_arrs ) && 'story' === $this->container->type ) {
		// Start over with a new gallery array to be filled with a query.
			$unique_arrs = array();
			$unique_ids = $this->get_gallery_from_query();
			if ( empty( $unique_ids ) ) {
				return;
			}
			foreach ( $unique_ids as $img_id ) {
				$img_arr = acf_get_attachment( $img_id );
				if ( ! empty( $img_arr ) ) {
					$unique_arrs[] = $img_arr;
				}
			}
		}
		$gallery = $this->prepare_gallery_images( $unique_arrs );
		if ( empty( $gallery ) ) {
			if ( $this->container->has_video ) {
				$this->container->has_gallery = true;
			}
		} else {
			$this->container->gallery = $gallery;
			$this->container->has_gallery = true;
		}
	}

	protected function set_video() {
		$input = $this->get_video_from_acf();
		if ( ! empty( $input ) ) {
			$video_obj = new Video( $input, 'section' );
		}
		if ( ! empty( $video_obj ) ) {
			$this->container->video = $video_obj;
			$this->container->has_video = true;
		}
	}

	protected function prepare_gallery_images( $unique_arrs ) {
		if ( empty( $unique_arrs ) ) {
			return;
		}
		$index = 1;
		$gallery = array();
		foreach ( $unique_arrs as $img_arr ) {
			$image_mods = array();
			// Add Image post ID and index to gallery item
			$image_mods['data'] = array(
				'img_id' => $img_arr['ID'],
			 	'index'  => $index,
			);
			$focus_points = exchange_get_focus_points( $img_arr );
			if ( ! empty( $focus_points ) ) {
				$image_mods['data'] = array_merge( $image_mods['data'], $focus_points );
				$image_mods['classes'] = array('focus');
			}
			// Add gallery context
			$img_obj = new Image( $img_arr, 'gallery', $image_mods );
			if ( is_object( $img_obj ) && is_a( $img_obj, 'Image' ) ) {
				$gallery[] = $img_obj;
			}
			$index++;
		}
		return $gallery;
	}

	 /**
	 * Sets ordered tag_list
	 *
	 * Retrieves WP Term objects and adds them to the Exchange object as a property
	 *
	 * @param object $exchange Exchange Content Type
	 *
	 * @return void.
	 **/
	protected function get_ordered_tag_list() {
		// Returns empty array if nothing found (and the post tag taxonomy exists ).
		$post_tags = get_object_term_cache( $this->container->post_id, 'post_tag' );
		if ( ! $post_tags || empty( $post_tags ) ) {
			$ordered_tag_list = wp_get_object_terms( $this->container->post_id, 'post_tag' );
		} else {
			$ordered_tag_list = $post_tags;
		}
		if ( empty( $ordered_tag_list ) && 'collaboration' === $this->container->type ) {
			$term_id = $this->set_tag_from_programme_round();
			if ( is_numeric( $term_id ) ) {
				$ordered_tag_list[] = get_term( intval( $term_id[0] ), 'post_tag');
			}
		}
		// Empty tax array.
		switch ( $this->container->type ) {
			case 'story':
				$tax_list = $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['TAXONOMIES']['display_priority_story'];
				break;
			case 'collaboration':
				$tax_list = $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['TAXONOMIES']['display_priority_collaboration'];
				break;
			default:
				return;
		}
		foreach ( $tax_list as $tax ) {
			// Try and retrieve terms from object from cache in the order of the tax list.
			$terms = get_object_term_cache( $this->container->post_id, $tax );
			if ( ! $post_tags || empty( $post_tags ) ) {
				if ( function_exists( 'get_field' ) ) {
					$tax_results = get_field( $tax, $this->container->post_id );
				}
			} else {
				$tax_results = $terms;
			}
			// Attempt to retrieve the pr_tag from the collaborations parent.
			if ( ! empty( $tax_results ) ) {
				if ( is_numeric( $tax_results ) ) {
					$term_obj = get_term( $tax_results );
					if ( $term_obj instanceof WP_Term ) {
						$tax_results = array( $term_obj );
					}
				}
				if ( $tax_results instanceof WP_Term ) {
					$tax_results = array( $tax_results );
				}
				if ( is_array( $tax_results ) ) {
					$ordered_tag_list = array_merge( $ordered_tag_list, $tax_results );
				}
			}
		}
		if ( isset( $this->container->language ) ) {
			$ordered_tag_list[] = $this->container->language;
		}
		if ( ! empty( $ordered_tag_list ) ) {
			return $ordered_tag_list;
		}
	}



	/**
	 * Sets ordered tag list
	 *
	 * @return void.
	 **/
	public function set_ordered_tag_list() {
		$ordered_tag_list = $this->get_ordered_tag_list();
		if ( ! empty( $ordered_tag_list ) ) {
			$this->container->ordered_tag_list = $ordered_tag_list;
			$this->container->has_tags = true;
		}
	}

	/**
	 * Returns short list of tags (no more than 2) for this story.
	 *
	 * @since 0.1.0
	 * @access public
	 *
	 * @return array $shortlist List of tags.
	 *
	 * @TODO Expand selection options.
	 **/
	public function get_tag_short_list() {
		$tag_list = $this->container->ordered_tag_list;
		if ( empty( $tag_list ) ) {
			return;
		}
		$shortlist = array();
		$tag_number = count( $tag_list );
		$i = 0;
		while ( $i < $tag_number && count( $shortlist ) < $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['TAXONOMIES']['grid_tax_max'] ) {
			$term = $tag_list[$i];
			if ( $term instanceof WP_Term ) {
				$shortlist[] = $term;
			}
			$i++;
		}
		return $shortlist;
	}

	/**
	 * Gets grid content
	 *
	 * Taking an array of objects this function gets the related grid content
	 *
	 * @param object $exchange Exchange Content Type
	 * @param array $related_content
	 *
	 * @throws Exception when there are no items to put in the grid.
	 **/
	protected function get_grid_content( $grid_items ) {
		$content = array();
		// Store post ID in the unique array so that it won't get added.
		$unique_ids = array( $this->container->post_id );
		foreach ( $grid_items as $item ) {
			// Tests for WP_Post content types.
			if ( BaseController::is_correct_content_type( $item ) ) {
				// Tests if the items are unique and don't refer to the post itself.
				if ( ! in_array( $item, $unique_ids, true ) ) {
					$grid_content[] = $item;
				}
			}
		}
		if ( count( $grid_content ) > 0 ) {
			return $grid_content;
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
	protected function set_related_grid_content( $related_content ) {
		$grid_content = $this->get_grid_content( $related_content );
		if ( isset( $grid_content ) ) {
			$this->container->has_related_content = true;
			$grid = new RelatedGrid( $grid_content, $this->container->type );
			$this->container->related_content = $grid;
		}
	}

	/**
	 * undocumented function summary
	 *
	 * Undocumented function long description
	 *
	 * @param type var Description
	 * @return {11:return type}
	 * @TODO POST_TAGS FOR COLLABORATIONS!!!
	 */
	protected function get_related_grid_content_by_tags() {
		if( ! $this->container->has_tags && 'story' === $this->container->type ) {
			$related_posts = $this->get_related_grid_content_by_cat();
			return $related_posts;
		} else {
			$tag_arr = array();
			$tags = $this->container->ordered_tag_list;
			foreach( $tags as $tag ) {
				$tag_arr[] = $tag->term_id;
			}

			$args = array(
				'post_type' => array('story','collaboration','programme_round','page'),
				'tag__in' => $tag_arr,
				'numberposts' => 3, /* you can change this to show more */
				'post__not_in' => array( $this->container->post_id ),
			);
			$related_posts = get_posts( $args );
		}
		return $related_posts;
	}

	protected function get_related_grid_content_by_cat() {
		$cat = $this->container->category;
		if( empty( $cat ) ) {
			return;
		} else {
			$args = array(
				'post_type' => array('story'),
				'cat' => $cat->slug,
				'numberposts' => 3, /* you can change this to show more */
				'post__not_in' => array( $this->container->post_id ),
			);
			$related_posts = get_posts( $args );
			return $related_posts;
		}
	}

	public function prepare_tag_modifiers( $term, $context = '' ) {
		if ( 'WP_Term' !== get_class( $term ) ) {
			throw new Exception( __('This is not a valid tag') );
		}
		$desc = ! empty( $term->description ) ? $tag->description : $term->name;
		$term_link = get_term_link( $term );
		$link = ! empty( $term_link ) ? $term_link : '#';
		$term_mods = array(
				'data' => array(
				'term_id'     => $term->term_id,
			),
			'classes' => array(
				'taxonomy' => $term->taxonomy,
			)
		);
		$term_mods['link'] = array(
			'title'       => $desc,
			'href'        => $link,
		);
		return $term_mods;
	}

	protected function set_programme_round( $parent_id ) {
		$this->container->programme_round = self::exchange_factory( $parent_id, '', 'programme_round' );
	}
}
