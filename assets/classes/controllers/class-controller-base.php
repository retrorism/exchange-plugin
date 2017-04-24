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
	 * @since 0.1.0
	 *
	 * @access protected
	 * @var object $container This controller's container.
	 */
	protected $container;

	/**
	 * Attaches a reference to the instantiating object.
	 *
	 * @since 0.1.0
	 * @access public
	 * @param Object $object Exchange object to refer to.
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
	 * @since 0.1.0
	 *
	 * Prevents the creation of grid items from non-content post types.
	 * @access public
	 * @param integer | object $post_id_or_object WP_Post ID or Object.
	 * @param string           $type Optional. Class name to be checked against.
	 * @return content type, if the post is right for content creation.
	 **/
	public static function is_correct_content_type( $post_id_or_object, $type = null ) {
		if ( is_numeric( $post_id_or_object ) && intval( $post_id_or_object ) > 0 ) {
			$post_id_or_object = get_post( intval( $post_id_or_object ) );
		}
		if ( ! is_object( $post_id_or_object ) ) {
			return;
		}
		if ( 'WP_Post' !== get_class( $post_id_or_object ) ) {
			return;
		}
		$allowed_types = self::exchange_types();
		if ( ! array_key_exists( $post_id_or_object->post_type, $allowed_types ) ) {
			return;
		}
		$content_type = $post_id_or_object->post_type;
		if ( null === $type || $allowed_types[ $content_type ] === $type ) {
			return $content_type;
		}
	}

	/**
	 * Return an associated array connecting an Exchange class to each CPT
	 *
	 * @since 0.1.0
	 *
	 * @access private
	 **/
	private static function exchange_types() {
		return array(
			'story'           => 'story',
			'page'            => 'story',
			'programme_round' => 'programme_round',
			'collaboration'   => 'collaboration',
			'participant'     => 'participant',
		);
	}

	/**
	 * Returns an Exchange class object based upon post type.
	 *
	 * @since 0.1.0
	 *
	 * @access public
	 * @param WP_Post $post_id_or_object WP_Post types / IDs passed in function.
	 * @param string  $context Optional. Context in which the object will be instantiated.
	 * @param string  $check_for_type Exchange type to match before creating an object.
	 **/
	public static function exchange_factory( $post_id_or_object, $context = '', $check_for_type = null ) {
		if ( empty( $post_id_or_object ) ) {
			return;
		}
		if ( is_numeric( $post_id_or_object ) && intval( $post_id_or_object ) > 0 ) {
			$post_id_or_object = get_post( intval( $post_id_or_object ) );
		}
		$type = self::is_correct_content_type( $post_id_or_object, $check_for_type );
		if ( empty( $type ) ) {
			return;
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
	 * @author Willem Prins | Somtijds
	 *
	 * @access public
	 * @param object  $exchange Exchange content object.
	 * @param WP_Post $post WP_post object to be mapped.
	 **/
	public function map_basics( $exchange, $post ) {
		// Check if the post and the newly created CPT object are of the same type.
		$class_lower = strtolower( get_class( $exchange ) );
		if ( empty( self::is_correct_content_type( $post, $class_lower ) ) ) {
			unset( $exchange );
			return;
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

		// Set programme round for collaborations.
		if ( 'collaboration' === $exchange->type && $post->post_parent >= 1 ) {
			$exchange->controller->set_programme_round( $post->post_parent );
		}

		// Set slug for programme rounds.
		if ( 'programme_round' === $exchange->type ) {
			 $slug = sanitize_title( $exchange->title );
			 $exchange->term = $slug;
		}

		// Set permalink.
		$exchange->link = get_permalink( $post );

	}

	/**
	 * Retrieve value for header image source ( use_featured_image / upload_new_image / none )
	 *
	 * @since 0.1.0
	 * @author Willem Prins | Somtijds
	 *
	 * @access protected
	 * @return string containing header image source
	 * @todo Use taxonomy instead of post_met
	 **/
	protected function get_header_image_source() {
		return get_post_meta( 'header_image', $this->container->post_id, true );
	}

	/**
	 * Retrieve header image object from DB
	 *
	 * @since 0.1.0
	 * @author Willem Prins | Somtijds
	 *
	 * @access protected
	 * @param string $context Context for the header image.
	 * @return Image object or void
	 *
	 * @todo remove ACF dependency
	 */
	protected function get_header_image( $context ) {
		switch ( $this->get_header_image_source( $context ) ) {
			case 'upload_new_image':
				$thumb = get_post_meta( $this->container->post_id, 'upload_header_image', true );
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
			$image_mods = array(
				'data' => array( 
					'img_id' => $thumb['id'],
				)
			);
			$focus_points = exchange_get_focus_points( $thumb );
			if ( ! empty( $focus_points ) ) {
				$image_mods['data'] = array_merge( $image_mods['data'], $focus_points );
				$image_mods['classes'] = array( 'focus' );
			}
			if ( 'collaboration' === $this->container->type ) {
				if ( $this->container->has_participants && count( $this->container->participants ) > 2 ) {
					$image_mods['style'] = 'tridem_or_more';
				}
			}
			return new Image( $thumb, $context, $image_mods );
		}
	}

	/**
	 * Attaches header image to story or collaboration
	 *
	 * @since 0.1.0
	 * @author Willem Prins | Somtijds
	 *
	 * @access protected
	 * @param string $context Context for the header image.
	 */
	protected function set_header_image( $context = '' ) {
		$image = $this->get_header_image( $context );
		if ( $image instanceof Image ) {
			$this->container->header_image = $image;
			$this->container->has_header_image = true;
		}
	}

	/**
	 * Retrieves featured image to story (for example for use in grid views).
	 *
	 * @since 0.1.0
	 * @author Willem Prins | Somtijds
	 *
	 * @access protected
	 * @param string $context Context for the featured image.
	 * @return null or Image object.
	 **/
	protected function get_featured_image( $context ) {
		$thumb_props = $this->get_featured_image_props();
		$focus_points = exchange_get_focus_points( $thumb_props );
		$image_mods = array();
		if ( ! empty( $focus_points ) ) {
			$image_mods['data'] = $focus_points;
			$image_mods['classes'] = array( 'focus' );
		}
		if ( ! empty( $thumb_props ) ) {
			return new Image( $thumb_props, $context, $image_mods );
		}
	}

	/**
	 * Retrieves featured image properties.
	 *
	 * @since 0.1.0
	 * @author Willem Prins | Somtijds
	 *
	 * @access protected
	 * @return array of properties for featured image or for the fallback image.
	 * @todo remove ACF dependency
	 **/
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
	 * Attaches featured image to content for use in grids
	 *
	 * @since 0.1.0
	 * @author Willem Prins | Somtijds
	 *
	 * @access public
	 * @param string $context for the featured image.
	 **/
	public function set_featured_image( $context = '' ) {
		$image = $this->get_featured_image( $context );
		if ( is_a( $image, 'Image' ) ) {
			$this->container->has_featured_image = true;
			$this->container->featured_image = $image;
		}
	}

	/**
	 * Retrieves images and videos to populate the gallery modal
	 **/
	protected function populate_gallery() {
		if ( 'collaboration' === $this->container->type ) {
			$this->populate_gallery_from_uploads();
		} elseif ( 'story' === $this->container->type ) {
			$this->populate_gallery_from_sections();
		}
		if ( count( $this->container->gallery ) ) {
			$this->container->has_gallery = true;
		}
	}

	/**
	 * Add element to gallery
	 *
	 * @author Willem Prins | Somtijds
	 * @since 0.1.0
	 *
	 * @return void
	 **/
	protected function add_element_to_gallery( $element, $type = 'image' ) {
		if ( empty( $element ) ) {
			return;
		}
		// Return Image object
		$obj = BasePattern::pattern_factory( $element,$type,'gallery',true );

		if ( $obj instanceof Image || $obj instanceof Video ) {
			$this->container->gallery[] = $obj;
		}
	}

	/**
	 * Sets ordered tag_list
	 *
	 * Retrieves WP Term objects and adds them to the Exchange object as a property
	 *
	 * @since 0.1.0
	 * @author Willem Prins | Somtijds
	 *
	 * @access public
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
				$ordered_tag_list[] = get_term( intval( $term_id[0] ), 'post_tag' );
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
	 * @since 0.1.0
	 * @author Willem Prins | Somtijds
	 *
	 * @access public
	 **/
	public function set_ordered_tag_list() {
		$ordered_tag_list = $this->get_ordered_tag_list();
		if ( ! empty( $ordered_tag_list ) ) {
			$this->container->ordered_tag_list = $ordered_tag_list;
			$this->container->has_tags = true;
		}
	}

	/**
	 * Returns short list of tags (no more than 2) for this story
	 *
	 * @since 0.1.0
	 * @author Willem Prins | Somtijds
	 *
	 * @access public
	 * @param int $max Maximum number of tags.
	 * @return array List of tags.
	 *
	 * @todo Expand selection options.
	 **/
	public function get_tag_short_list( $max ) {
		$tag_list = $this->container->ordered_tag_list;
		if ( empty( $tag_list ) ) {
			return;
		}
		$shortlist = array();
		$tag_number = count( $tag_list );
		$i = 0;
		while ( $i < $tag_number && count( $shortlist ) < $max ) {
			$term = $tag_list[ $i ];
			if ( $term instanceof WP_Term ) {
				$shortlist[] = $term;
			}
			$i++;
		}
		return $shortlist;
	}

	/**
	 * Get grid content
	 *
	 * Taking in an array of objects, this function checks and returns the related grid content.
	 *
	 * @since 0.1.0
	 * @author Willem Prins | Somtijds
	 *
	 * @access protected
	 * @param array $grid_items List of WP_Posts to be validated.
	 * @return array|void Array of objects to add to the related grid or void.
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
	 * Set related content grid
	 *
	 * Taking an array of objects from ACF field input, this function sets the related content grid object.
	 *
	 * @since 0.1.0
	 * @author Willem Prins | Somtijds
	 *
	 * @access protected
	 * @param array $related_content Objects that form the related content grid.
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
	 * Get related grid content by tags
	 *
	 * Take a term list from an Exchange object and find 3 related posts.
	 *
	 * @since 0.1.0
	 * @author Willem Prins | Somtijds
	 *
	 * @access protected
	 * @return array Array of max. three related WP_Post objects.
	 */
	protected function get_related_grid_content_by_tags() {
		if ( ! $this->container->has_tags && 'story' === $this->container->type ) {
			$related_posts = $this->get_related_grid_content_by_cat();
			return $related_posts;
		} else {
			$tag_arr = array();
			$tags = $this->container->ordered_tag_list;
			foreach ( $tags as $tag ) {
				$tag_arr[] = $tag->term_id;
			}

			$args = array(
				'post_type' => array( 'story', 'collaboration', 'programme_round', 'page' ),
				'tag__in' => $tag_arr,
				'numberposts' => 3, /* you can change this to show more */
				'post__not_in' => array( $this->container->post_id ),
			);
			$related_posts = get_posts( $args );
		}
		return $related_posts;
	}

	/**
	 * Prepare tag modifiers for template output
	 *
	 * @since 0.1.0
	 * @author Willem Prins | Somtijds
	 *
	 * @access protected
	 * @param WP_Term $term Term object to add modifiers to.
	 * @param string  $context Context for tag list.
	 * @return array|void Term modifiers or void.
	 */
	public function prepare_tag_modifiers( $term, $context = '' ) {
		if ( ! $term instanceof WP_Term ) {
			return;
		}
		$desc = ! empty( $term->description ) ? $tag->description : $term->name;
		if ( class_exists( 'FacetWP' ) ) {
			$term_link = get_home_url() . '/archive/?fwp_' . $term->taxonomy . '=' . $term->slug;
		} else {
			$term_link = get_term_link( $term );
		}
		$link = ! empty( $term_link ) ? $term_link : '#';
		$term_mods = array(
				'data' => array(
				'term_id'     => $term->term_id,
			),
			'classes' => array(
				'taxonomy' => $term->taxonomy,
			),
		);
		$term_mods['link'] = array(
			'title'       => $desc,
			'href'        => $link,
		);
		return $term_mods;
	}

	/**
	 * Add Programme Round object to the Exchange container
	 *
	 * @since 0.1.0
	 * @author Willem Prins | Somtijds
	 *
	 * @param int $parent_id  $type Exchange type to be translated into a CPT.
	 * @return void
	 **/
	protected function set_programme_round( $parent_id ) {
		$this->container->programme_round = self::exchange_factory( $parent_id, '', 'programme_round' );
	}

	/**
	 * Get all posts from a certain Exchange object type.
	 *
	 * @since 0.1.0
	 * @author Willem Prins | Somtijds
	 *
	 * @param string $type Exchange type to be translated into a CPT.
	 * @param string $fields Which field to return ('ids', 'id=>parent' or '').
	 * @return array $results Array of posts that were retrieved.
	 **/
	public static function get_all_from_type( $type, $fields = '' ) {
		$types = self::exchange_types();
		if ( ! array_key_exists( $type, $types ) ) {
			return;
		}
		$args = array(
			'post_type'   => $type,
			'orderby'     => 'title',
			'order'       => 'ASC',
			'status'	  => 'publish',
			'posts_per_page' => -1,
		);

		if ( '' !== $fields && is_string( $fields ) ) {
			$args['fields'] = $fields;
		}

		$type_query = new WP_Query( $args );
		$results = $type_query->posts;
		wp_reset_postdata();
		return $results;
	}

	/**
	 * Get an attachment ID given a URL.
	 *
	 * @link https://wpscholar.com/blog/get-attachment-id-from-wp-image-url/
	 *
	 * @param string $url Image url.
	 * @return int Attachment ID on success, 0 on failure
	 */
	public function get_attachment_id_from_url( $url ) {

		$attachment_id = 0;
		$dir = wp_upload_dir();

		if ( false !== strpos( $url, $dir['baseurl'] . '/' ) ) { // Is URL in uploads directory?
			$file = basename( $url );
			$query_args = array(
				'post_type'   => 'attachment',
				'post_status' => 'inherit',
				'fields'      => 'ids',
				'meta_query'  => array(
					array(
						'value'   => $file,
						'compare' => 'LIKE',
						'key'     => '_wp_attachment_metadata',
					),
				),
			);

			$query = new WP_Query( $query_args );

			if ( $query->have_posts() ) {
				foreach ( $query->posts as $post_id ) {
					$meta = wp_get_attachment_metadata( $post_id );
					$original_file       = basename( $meta['file'] );
					$cropped_image_files = wp_list_pluck( $meta['sizes'], 'file' );
					if ( $original_file === $file || in_array( $file, $cropped_image_files = true ) ) {
						$attachment_id = $post_id;
						break;
					}
				}
			}
		}

		return $attachment_id;
	}
}
