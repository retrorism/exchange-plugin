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
	public static function is_correct_content_type( $post_object, $type = null ) {
		if ( is_object( $post_object ) ) {
			if ( 'WP_Post' === get_class( $post_object ) ) {
				$allowed_types = array(
					'story'           => 'story',
					'page'            => 'story',
					'programme_round' => 'programme_round',
					'grid_breaker'    => 'grid_breaker',
					'collaboration'   => 'collaboration',
					'participant'     => 'participant',
				);
				if ( array_key_exists( $post_object->post_type, $allowed_types ) ) {
					$exchange = $allowed_types[ $post_object->post_type ];
					if ( $exchange === $type || null === $type ) {
						return $exchange;
					}
				}
			}
		}
	}

	/**
	 * Returns an Exchange class object based upon post type.
	 *
	 * @access public
	 * @param WP_Post $post WP_Post types passed in function.
	 * @param string $context Optional. Context in which the object will be instantiated.
	 *
	 * @throws Exception when wrong post type is supplied.
	 **/
	public static function exchange_factory( $post_object, $context = '' ) {
		$type = self::is_correct_content_type( $post_object );
		if ( ! empty( $type ) ) {
			$args = array( $post_object, $context );
			switch ( $type ) {
				case 'collaboration':
					return new Collaboration( ...$args );
				case 'programme_round':
					return new Programme_Round( ...$args );
				case 'grid_breaker':
					// Context grid is required for now.
					if ( 'griditem' === $context ) {
						return new Grid_Breaker( ...$args );
					}
					break;
				case 'story':
				case 'page':
				default:
					return new Story( ...$args );
			}
		} else {
			throw new Exception( __( 'The factory disagrees' ) );
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

		// Set permalink.
		$exchange->link = get_permalink( $post );

		// Set tags.
		$this->set_tag_list();
	}

	// Store sections in Exchange object
	protected function set_sections( $acf_sections ) {
		// Loop through sections.
		foreach( $acf_sections as $s ) {
			if ( ! empty( $s['section_contents'] ) ) {
				$section_mods['type'] = $s['section_contents'];
			}
			$section = new Section( $s, strtolower( get_class( $this->container ) ), $section_mods );
			if ( is_object( $section ) && is_a( $section, 'Section' ) ) {
				$this->container->sections[] = $section;
			}
		}
	}


	protected function get_header_image_source( $post_id ) {
		return get_field( 'header_image' );
	}

	protected function get_header_image( $post_id, $context ) {
		switch ( $this->get_header_image_source( $post_id ) ) {
			case 'upload_new_image':
				$thumb = get_field( 'upload_header_image', $post_id );
				break;
			case 'none':
				break;
			case 'use_featured_image':
			default:
				$thumb_id = get_post_thumbnail_id( $post_id );
				// Use ACF function to create array for Image object constructor.
				$thumb = acf_get_attachment( $thumb_id );
				break;
		}
		if ( isset( $thumb ) && count( $thumb ) ) {
			$focus_points = exchange_get_focus_points( $thumb );
			$image_mods = array();
			if ( ! empty( $focus_points ) ) {
				$image_mods['data'] = $focus_points;
				$image_mods['classes'] = array('focus');
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
	protected function set_header_image( $post_id, $context = '' ) {
		$image = $this->get_header_image( $post_id, $context );
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
	protected function get_featured_image( $post_id, $context ) {
		$thumb_props = $this->get_featured_image_props( $post_id );
		if ( ! empty( $thumb_props ) ) {
			return new Image( $thumb_props, $context );
		}
	}

	protected function get_featured_image_props( $post_id ) {
		if ( ! has_post_thumbnail( $post_id ) ) {
			$thumb_props = acf_get_attachment( $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['IMAGES']['fallback_image_att_id'] );
		} else {
			$thumb_id = get_post_thumbnail_id( $post_id );
			if ( 'attachment' === get_post( $thumb_id )->post_type ) {
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
	public function set_featured_image( $context ) {
		$image = $this->get_featured_image( $this->container->post_id, $context );
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
		$gallery = array();
		$index = 1;
		foreach ( $unique_ids as $image_id ) {
			$img_arr = acf_get_attachment( $image_id );
			if ( empty( $img_arr ) ) {
				continue;
			}
			$image_mods = array();
			// Add Image post ID and index to gallery item
			$image_mods['data'] = array(
				'img_id' => $image_id,
			 	'index'  => $index,
			);
			$focus_points = exchange_get_focus_points( $img_arr );
			if ( ! empty( $focus_points ) ) {
				$image_mods['data'] = array_merge( $image_mods['data'], $focus_points );
				$image_mods['classes'] = array('focus');
			}
			// Add lightbox context
			$img_obj = new Image( $img_arr, 'lightbox', $image_mods );
			if ( is_object( $img_obj ) && is_a( $img_obj, 'Image' ) ) {
				$gallery[] = $img_obj;
			}
			$index++;
		}
		return $gallery;
	}

	public function set_gallery() {
		$gallery = $this->get_gallery_from_query();
		if ( ! count( $gallery ) ) {
			return;
		} else {
			$this->container->gallery = $gallery;
			$this->container->has_gallery = true;
		}
	}

	/**
	 * Retrieves tag list.
	 *
	 * @return array of tags or void.
	 **/
	protected function get_tag_list() {
		$taxonomies = get_object_taxonomies( $this->container->type );
		$term_results = array();
		foreach ( $taxonomies as $taxonomy ) {
			$tax_terms = get_the_terms( $this->container->post_id, $taxonomy );
			if ( ! empty( $tax_terms ) ) {
				$term_results = array_merge( $term_results, $tax_terms );
			}
		}
		if ( count( $term_results ) > 0 ) {
			return $term_results;
		}
	}

	/**
	 * Attaches tags.
	 *
	 * @param string $acf_header_image Advanced Custom Fields Header selection option
	 * @param object $exchange Content type to attach featured image to.
	 *
	 * @return void.
	 **/
	protected function set_tag_list() {
		$tag_list = $this->get_tag_list();
		if ( $tag_list ) {
			$this->container->tag_list = $tag_list;
			$this->container->has_tags = true;
		}
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
		$tax_list = $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['TAXONOMIES']['display_priority'];
		$results = array();
		switch ( $this->container->type ) {
			case 'story':
				foreach( $tax_list as $taxonomy ) {
					if ( ! in_array( $taxonomy, array( 'topics', 'locations', 'post_tag', 'tandem' ), true ) ) {
						continue;
					}
					$tax_results = get_field( $taxonomy, $this->container->post_id );
					if ( empty( $tax_results ) ) {
						continue;
					}
					if ( is_object( $tax_results ) ) {
						$tax_results = array( $tax_results );
					}
					$results = array_merge( $results, $tax_results );
				}
				// End with the language tag.
				if ( isset( $this->container->language ) ) {
					$results[] = $this->container->language;
				}
				break;
			case 'collaboration':
				//$results[] = get_term_by( 'name', $this->container->programme_round->title, 'topic' );
				foreach( $tax_list as $taxonomy ) {
					if ( ! in_array( $taxonomy, array( 'topics', 'locations', 'post_tag' ), true ) ) {
						continue;
					}
					$tax_results = get_field( $taxonomy, $this->container->post_id );
					if ( empty( $tax_results ) ) {
						continue;
					}
					if ( is_object( $tax_results ) ) {
						$tax_results = array( $tax_results );
					}
					$results = array_merge( $results, $tax_results );
				}
				break;
			default:
				$results = false;
				break;
		}
		if ( ! empty( $results ) ) {
			return $results;
		}
	}

	/**
	 * Sets ordered tag list
	 *
	 * @return void.
	 **/
	public function set_ordered_tag_list() {
		$ordered_tag_list = $this->get_ordered_tag_list();
		if ( is_array( $ordered_tag_list ) && count( $ordered_tag_list ) > 0 ) {
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
		$i = 0;
		$shortlist = array();
		$tax_order = $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['TAXONOMIES']['display_priority'];
		foreach ( $tax_order as $taxonomy ) {
			if ( array_key_exists( $taxonomy, $this->container->ordered_tag_list ) ) {
				foreach ( $this->container->ordered_tag_list[$taxonomy] as $term ) {
					if ( $i < $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['TAXONOMIES']['grid_tax_max'] ) {
						$shortlist[] = $term;
						$i++;
					} else {
						continue 2;
					}
				}
			}
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
				if ( ! in_array( $item->ID, $unique_ids, true ) ) {
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
			$grid = new RelatedGrid( $grid_content );
			$this->container->related_content = $grid;
		}
	}

	public function prepare_tag_modifiers( $term ) {
		if ( 'WP_Term' !== get_class( $term ) ) {
			throw new Exception( __('This is not a valid tag') );
		}
		$desc = ! empty( $term->description ) ? $tag->description : $term->name;
		$term_mods = array(
				'data' => array(
				'term_id'     => $term->term_id,
			),
			'link_attributes' => array(
				'title'       => $desc,
				'href'        => '#',
			),
			'classes' => array(
				'taxonomy' => $term->taxonomy,
			)
		);
		return $term_mods;
	}


}
