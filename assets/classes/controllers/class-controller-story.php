<?php
/**
 * Controller Class
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
	 * @param object $story Newly instantiated Story class object.
	 * @param object $post Story post object.
	 *
	 * @throws Exception When no valid post ID is presented.
	 **/

	public function map_story( $story, $post ) {

		// Retrieve post_id variable.
		$post_id = $post->ID;

		// Throw Exception when the input is not a valid story post type object.
		if ( ! ( $post_id >= 1 ) || 'story' !== $post->post_type ) {
			//unset( $story );
			throw new Exception( 'This is not a valid post' );
		}

		// Dump ACF variables.
		$acf_editorial_intro = get_field( 'editorial_intro', $post_id );
		$acf_storyteller = get_field( 'storyteller', $post_id );
		$acf_category = get_field( 'category', $post_id );
		$acf_language = get_field( 'language', $post_id );
		$acf_terms = get_field( 'terms', $post_id );
		$acf_sections = get_field( 'sections', $post_id );
		$acf_header_image = get_field( 'header_image', $post_id );


		// Set story title.
		$story->title = get_the_title( $post_id );

		// Set story type.
		$story->type = get_post_type( $post_id );

		// Set editorial introduction.
		if ( ! empty( $acf_editorial_intro ) ) {
			$story->editorial_intro = new EditorialIntro( $acf_editorial_intro, 'story' );
		}

		// Set language.
		if ( is_object( $acf_language ) ) {
			if ( 'WP_Term' === get_class( $acf_language ) ) {
				$story->language = $acf_language->name;
			}
		}

		// Set category.
		if ( is_object( $acf_category ) ) {
			if ( 'WP_Term' === get_class( $acf_category ) ) {
				$story->category = $acf_category->name;
			}
		}

		// Set participant.
		if ( is_object( $acf_storyteller ) ) {
			if ( 'WP_Post' === get_class( $acf_storyteller ) ) {
				$story->storyteller = new Participant( $acf_storyteller );
				$story->storyteller->name = $acf_storyteller->post_title;
			}
		}

		// Set tags.
		if ( ! empty( $acf_terms ) ) {
			foreach ( $terms as $term ) {
				$story->add_tag( $term->name, get_term_link( $term ) );
			}
		}

		// Set special tags.
		// if ( $acf['add_special_tags'] ) {
		// 	if ( in_array( 'location', $acf['add_special_tags'], true ) ) {
		// 		$story->add_tag( 'TODO location', 'TODO url' );
		// 	} elseif ( in_array( 'programme_round', $acf['add_special_tags'], true ) ) {
		// 		$story->add_tag( 'TODO programme_round', 'TODO url' );
		// 	}
		// }

		// Set sections.
		if ( ! empty( $acf_sections ) ) {
			$story->sections = $acf_sections;
		}

		// Set header image.
		if ( 'none' !== $acf_header_image ) {
			$story->header_image = $this->get_header_image( $acf_header_image, $post_id );
			$story->has_header_image = true;
		}

		$this->set_byline( $story );

		return $story;
	}

	/**
	 * Retrieves and attaches header image to story
	 *
	 * @param string $acf_header_image Advanced Custom Fields Header selection option
	 * @param integer $post_id.
	 * @return Image object or null
	 */
	protected function get_header_image( $acf_header_image, $post_id ) {
		switch ( $acf_header_image ) {
			case 'use_featured_image':
				// Use ACF function to create array for Image object constructor.
				$thumb = acf_get_attachment( get_post_thumbnail_id( $post_id ), 'header_image' );
				break;
			case 'upload_new_image':
				$thumb = get_field( 'upload_header_image', $post_id );
				break;
			default: break;
		}
		if ( is_array( $thumb ) ) {
			return new HeaderImage( $thumb, 'story', array(
				'is_header_image' => true,
			) );
		}
	}

	/**
	 * Returns one story with all its properties.
	 *
	 * @param mixed $post_id_or_object Post ID or Object.
	 * @return Story object or null
	 */
	function get_story( $post_id_or_object ) {
		if ( $post_id_or_object ) {
			$post = get_post( $post_id_or_object );
			return $this->map_story( $post );
		} else {
			return null;
	   	}
	}

	/**
	 * Return posts with WP Query using supplied argument array.
	 *
	 * @param array $args Query arguments.
	 * @return array of stories
	 */
	protected function execute_query( $args ) {
		$query = new WP_Query( $args );
		return $query->posts;
	}

	/**
	 * Returna story set.
	 *
	 * @return array of stories
	 *
	 * @param array $args Query arguments.
	 */
	private function get_story_set( $args ) {
		$posts = $this->execute_query( $args );
		$stories = array();
		foreach ( $posts as $p ) {
			$stories[] = $this->map_story( $p );
		}
		return $stories;
	}

	/**
	 * Returns all stories
	 *
	 * @return array of stories
	 */
	function get_all_stories() {
		$stories = array();
		foreach ( $this->get_all_story_posts() as $p ) {
			$stories[] = $this->map_story( $p );
		}
		return $stories;
	}

	/**
	 * Returns all story posts.
	 *
	 * @return array of story post objects
	 **/
	function get_all_story_posts() {
		$args = array(
			'post_type' => 'story',
		);
		$query = new WP_Query( $args );
		return $query->posts;
	}

	/**
	 * Retrieve stories by taxonomy.
	 *
	 * @param string $taxonomy Taxonomy name.
	 * @param string $term Term name.
	 * @return array of story objects
	 **/
	public function get_stories_by_taxonomy( $taxonomy, $term ) {
		$args = array(
			'post_type' => 'story',
			'tax_query' => array(
				array(
					'taxonomy' => ''.$taxonomy,
					'field'    => 'name',
					'terms'    => ''.$term,
				),
			),
		);
		return $this->get_story_set( $arg );
	}

	/**
	 * Returns all story posts by taxonomy.
	 *
	 * @param array $tax_params Tax parameters.
	 *
	 * @TODO Write function.
	 **/
	public function get_stories_by_programme_round( $tax_params ) {
	}

	/**
	 * Returns all story posts by taxonomy.
	 *
	 * @param integer $collaboration_id Collaboration identifier.
	 *
	 * @TODO Write function.
	 **/
	public function get_stories_by_collaboration( $collaboration_id ) {
	}

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
	 * @access public
	 * @param story object
	 * @return string $byline Byline object built from byline template.
	 **/
	public function set_byline( $story ) {
		if ( is_object( $story->storyteller ) && is_object( $story->storyteller->collaboration ) ) {
			$templates = $this->get_byline_templates();

			if ( $story->storyteller->is_active ) {
				$byline_template = $templates['present'];
			} else {
				$byline_template = $templates['past'];
			}
			$byline_template = str_replace( '[[storyteller]]', $story->storyteller->name, $byline_template );
			$byline_template = str_replace( '[[programme_round]]', tandem_create_link( $story->storyteller->collaboration->programme_round ), $byline_template );
			$byline = str_replace( '[[collaboration]]', tandem_create_link( $story->storyteller->collaboration ), $byline_template );

			$story->byline = new Byline( $byline, 'footer' );
		}

		else {
			$story->byline = null;
		}
	}
}
