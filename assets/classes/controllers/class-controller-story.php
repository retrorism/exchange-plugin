<?php
/**
 * Story Controller Class
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 11/2/2016
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
 * Controller for stories.
 *
 * This class contains all story logic.
 *
 * @since 0.1.0
 **/
class StoryController extends BaseController {

	/**
	 * Return story object with properties taken from ACF Fields.
	 *
	 * @since 0.1.0
	 * @access public
	 *
	 * @param object $this->container Newly instantiated Story class object.
	 * @param object $post Story post object.
	 **/
	public function map_story_basics() {

		// Retrieve post_id variable.
		$post_id = $this->container->post_id;

		// Map ACF variables.
		$acf_intro = get_post_meta( $post_id, 'editorial_intro', true );

		if ( current_theme_supports( 'language_tags' ) ) {
			$acf_language = wp_get_post_terms( $post_id, 'language', true );
		}

		$acf_category = wp_get_post_terms( $post_id, 'category', true );
		$acf_has_cta = get_post_meta( $post_id, 'has_cta', true );

		// Set editorial introduction.
		if ( ! empty( $acf_intro ) ) {
			$this->set_editorial_intro( $acf_intro );
		}

		// Set language.
		if ( ! empty( $acf_language ) && 'WP_Term' === get_class( $acf_language[0] ) ) {
			$this->container->language = $acf_language[0];
		}

		// Set category.
		if ( ! empty( $acf_category ) && 'WP_Term' === get_class( $acf_category[0] ) ) {
			$this->container->category = $acf_category[0];
		}

		// Set CTA check
		if ( ! empty( $acf_has_cta ) ) {
			$this->container->has_cta = $acf_has_cta;
		}
	}

	/**
	 * Return story object with properties taken from ACF Fields.
	 *
	 * @since 0.1.0
	 * @access public
	 * @return void;
	 * @TODO make this ACF-independent (remove get_field)
	 **/
	public function map_full_story() {

		// Retrieve post_id variable from basic mapping.
		$post_id = $this->container->post_id;

		// Set language global to language category
		if ( isset( $this->container->language ) ) {
			$GLOBALS['story_language'] = $this->container->language->name;
		}

		// // Throw Exception when the input is not a valid story post type object.
		// if ( ! ( $post_id >= 1 ) ) {
		// 	unset( $this->container );
		// 	throw new Exception( 'This is not a valid post' );
		// }

		// Get related
		$curation = get_post_meta( $post_id, 'related_content_auto_select', true );
		if ( ! $curation ) {
			$related_content = $this->get_related_grid_content_by_tags();
		} else {
			$related_content = get_post_meta( $post_id, 'related_content', true );
		}

		if ( is_array( $related_content ) && count( $related_content ) > 0 ) {
			$this->set_related_grid_content( $related_content );
		}

		//Set sections.
		if ( function_exists( 'get_field' ) ) {
			$acf_sections = get_field( 'sections', $post_id );
			if ( ! empty( $acf_sections ) ) {
				$this->set_sections( $acf_sections );
			}
		}

		// $acf_section_count = get_post_meta( $post_id, 'sections', true );
		// var_dump( $acf_section_count );
		// if ( ! empty( $acf_section_count ) ) {
		// 	$post_custom = get_post_custom( $post_id );
		// 	var_dump( $post_custom );
		// }
		// for ( $i = 0; $i < $acf_section_count; $i++ ) {
		// 	$acf_section_contents = get_post_meta( $post_id, 'sections_' . $i . '_contents', true );
		// 	var_dump( $acf_section_contents );
		// 	if ( empty( $acf_section_contents ) || ! is_array( $acf_section_contents ) ) {
		// 		continue;
		// 	}
		// 	$contents_length = count( $acf_section_contents );
		// 	for( $j = 0; $j < $contents_length; $j++ ) {
		// 		switch( $acf_section_contents[ $j ] ) {
		// 			case 'has_story_elements' :
		// 				$meta_name = 'sections_' . $i . '_contents_' . $j . '_story_elements';
		// 				$acf_story_elements = get_post_meta( $post_id, $meta_name, true );
		// 				var_dump( $meta_name );
		// 				var_dump( $acf_story_elements );
		// 				break;
		// 			default :
		// 				var_dump( $acf_sections_items[ $j ] );
		// 				break;
		// 		}
		// 	}
		// }
		// throw new Exception("Testing {1:What are we testing?}");


		// Set header image.
		$this->set_header_image( 'story__header' );

		// Set one or more participants as storytellers
		$acf_storyteller = get_post_meta( $post_id, 'storyteller', true );
		if ( is_numeric( $acf_storyteller ) ) {
			$s_obj = BaseController::exchange_factory( $acf_storyteller );
			if ( $storyteller instanceof Participant ) {
				$this->container->storyteller[] = $s_obj;
			}
		}
		if ( is_array( $acf_storyteller ) ) {
			foreach ( $acf_storyteller as $s ) {
				$s_obj = BaseController::exchange_factory( $s );
				if ( $s_obj instanceof Participant ) {
					$this->container->storyteller[] = $s_obj;
				}
			}
		}
		if ( current_theme_supports( 'bylines') ) {
			if ( ! empty( $this->container->storyteller ) ) {
				$this->set_byline();
			} else {
				$this->set_custom_byline();
			}
		}

 		//$this->set_videos(); 
		
		$this->populate_gallery();
	}

	/**
	 * Set editorial intro.
	 *
	 * @param string $acf_intro Editorial Intro.
	 */
	 protected function set_editorial_intro( $acf_intro ) {
		 $intro_input = array(
			 'text' => '<p>' . $acf_intro . '</p>',
		 );
		 $this->container->has_editorial_intro = true;
		 // Allow for translations and buttons
		 $acf_intro_add_translation = get_post_meta( $this->container->post_id, 'add_intro_translation', true );
		 if ( $acf_intro_add_translation && function_exists( 'get_field' ) ) {
			 $acf_intro_translations = get_field( 'intro_translations', $this->container->post_id );
			 $intro_input['add_translation'] = $acf_intro_add_translation;
			 $intro_input['translations'] = $acf_intro_translations;
		 }
		 $this->container->editorial_intro = new EditorialIntro( $intro_input, 'story' );
	 }

	/**
	 * Store sections in Exchange object
	 *
	 * @since 0.1.0
	 * @author Willem Prins | Somtijds
	 *
	 * @return void
	 **/
 	protected function set_sections( $acf_sections ) {
 		// Loop through sections.
 		foreach( $acf_sections as $s ) {
 			$section_mods = array();
 			if ( ! empty( $s['contents'] ) && isset( $s['contents']['acf_fc_layout'] ) ) {
 				$section_mods['type'] = $s['contents']['acf_fc_layout'];
 			}
 			$section = new Section( $s, strtolower( get_class( $this->container ) ), $section_mods );
 			if ( $section instanceof Section ) {
 				$this->container->sections[] = $section;
 			}
 		}
		//throw new Exception("Testing {1:What are we testing?}");
 	}

	// protected function get_sections() {
	// 	$
	// }

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


	/**
	 * Retrieve story byline template from options page.
	 *
	 * @since 0.1.0
	 * @access public
	 * @return array $templates Byline templates for present and past projects to be replaced with story-specific fields.
	 */
	protected function get_byline_templates() {
		$templates = array();
		$byline_template_present = get_option( EXCHANGE_PLUGIN . '_byline_template_present' );
		$byline_template_past = get_option( EXCHANGE_PLUGIN . '_byline_template_past' );
		if ( empty( $byline_template_present ) ) {
			$templates['present'] = 'This story was shared by [[storyteller]], who currently participates in [[programme_round]] with [[collaboration]]';
		}
		else {
			$templates['present'] = $byline_template_present;
		}
		if ( empty( $byline_template_past ) ) {
			$templates['past'] = 'This story was shared by [[storyteller]], who participated in [[programme_round]] with [[collaboration]]';
		}
		else {
			$templates['past'] = $byline_template_past;
		}
		return $templates;
	}

	/**
	 * If storyteller is set, Replace placeholders in template with personal details connected to the storyteller.
	 *
	 * @since 0.1.0
	 * @access protected
	 **/
	protected function set_byline() {
		if ( ! is_object( $this->container->storyteller ) ) {
			return;
		}
		$this->container->storyteller->controller->set_collaboration();
		if ( ! is_object( $this->container->storyteller->collaboration ) ) {
			return;
		}
		$templates = $this->get_byline_templates();
		if ( $this->container->storyteller->collaboration->programme_round->is_active ) {
			$byline_template = $templates['present'];
		} else {
			$byline_template = $templates['past'];
		}
		$collab_term = $this->container->storyteller->collaboration->programme_round->term;
		if ( ! empty( $collab_term ) ) {
			$term = get_term_by( 'slug', $collab_term, 'post_tag' );
		}
		if ( ! empty( $term ) && $term instanceof WP_Term ) {
			$term_link = exchange_create_link( $term );
		} else {
			$term_link = $this->container->storyteller->collaboration->programme_round->title;
		}
		$byline_template = str_replace( '[[storyteller]]', $this->container->storyteller->name, $byline_template );
		$byline_template = str_replace( '[[programme_round]]', $term_link, $byline_template );
		$byline = '<p>' . str_replace( '[[collaboration]]', exchange_create_link( $this->container->storyteller->collaboration ), $byline_template ) . '</p>';
		$this->container->byline = new Byline( $byline, 'footer' );
	}

	/**
	 * If storyteller is set, Replace placeholders in template with personal details connected to the storyteller.
	 *
	 * @since 0.1.0
	 * @access protected
	 **/
	protected function set_custom_byline() {
		$acf_custom_byline = get_post_meta( $this->container->post_id, 'custom_byline', true );
		if ( ! empty( $acf_custom_byline ) ) {
			$this->container->has_custom_byline = true;
			$this->container->byline = new Byline( $acf_custom_byline, 'footer' );
		}
		else {
			$this->set_byline();
		}
	}

	// /**
	//  * undocumented function
	//  *
	//  * @return void
	//  * @author 
	//  **/
	// protected function set_storyteller_card() {
	// 	if ( empty( $this->container->storyteller ) || ! count( $this->container->storyteller ) ) {
	// 		return;
	// 	}
	// 	foreach( $this->container->storyteller as $s_object ) {

	// 	}
	// }

	/**
	 * Iterate over sections to find images to put in the gallery
	 *
	 * @since 0.1.0
	 * @access protected
	 **/
	protected function populate_gallery_from_sections() {
		if ( count( $this->container->gallery ) > 0 ) {
			return;
		}
		if ( $this->container->has_header_image &&
			$this->container->header_image instanceof Image ) {
			$header_image = clone $this->container->header_image;
			$header_image->context = 'gallery';
			$this->container->gallery[] = $header_image;
		}
		foreach( $this->container->sections as $section ) {
			if ( empty( $section->input )
				|| empty( $section->input['contents'] ) ) {
				continue;
			}
			foreach ( $section->input['contents'] as $section_item ) {
				if ( ! empty( $section_item['acf_fc_layout'] )
					&& 'has_story_elements' === $section_item['acf_fc_layout']
					&& ! empty( $section_item['story_elements'] ) ) {

					foreach( $section_item['story_elements'] as $story_element ) {
						if ( ! in_array( $story_element['acf_fc_layout'], array( 'image', 'two_images', 'embedded_video' ), true ) ) {
							continue;
						}
						if ( ! empty( $story_element['two_images'] ) 
							&& ! empty( $story_element['image_orientation'] ) ) {
							$orientation_arr = explode( '_', $story_element['image_orientation'] );
							$i = 0;
							foreach ( $story_element['two_images'] as $image ) {
								$image_element = array(
									'acf_layout' => 'image',
									'image' => $image,
									'image_orientation' => $orientation_arr[$i],
								);
								$this->add_element_to_gallery( $image_element );
								$i++;
							}
						} elseif ( ! empty( $story_element['image'] ) ) {
							$this->add_element_to_gallery( $story_element );
						} elseif ( ! empty( $story_element['video_embed_code'] ) ) {
							$this->add_element_to_gallery( $story_element, 'embedded_video' );
						}
					}
				}
			}
		}
	}

	/**
	 * Get related grid content by tags (stories only)
	 *
	 * Take a cat list from an Exchange object and find 3 related posts.
	 *
	 * @since 0.1.0
	 * @author Willem Prins | Somtijds
	 *
	 * @access protected
	 * @return array Array of max. three related WP_Post objects or void.
	 */
	protected function get_related_grid_content_by_cat() {
		$cat = $this->container->category;
		if ( empty( $cat ) ) {
			return;
		} else {
			$args = array(
				'post_type' => array( 'story' ),
				'cat' => $cat->slug,
				'numberposts' => 3, /* you can change this to show more */
				'post__not_in' => array( $this->container->post_id ),
			);
			$related_posts = get_posts( $args );
			return $related_posts;
		}
	}


}
