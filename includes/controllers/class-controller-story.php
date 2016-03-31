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
	exit;
}

/**
 * Controller for stories.
 *
 * This class contains all story logic.
 *
 * @since 0.1.0
 **/
class StoryController extends BaseController {

	/**
	 * Constructor for Story controller.
	 *
	 * @since 0.1.0
	 * @access public
	 **/
	public function __construct() {

		// parent::__construct($post_id_or_post_object);
	}

	/**
	 * Return story object with properties taken from ACF Fields.
	 *
	 * @since 0.1.0
	 * @access public
	 *
	 * @param integer $post Post ID.
	 * @return object $story Story object with set properties.
	 *
	 * @throws Exception When no valid post ID is presented.
	 **/
	public function map_story( $post ) {

		// Retrieve post_id variable or throw Exception.
		$post_id = $post->ID;
		if ( ! ( $post_id >= 1 ) ) {
			throw new Exception( 'This is no valid post' );
		}

		// Dump ACF variables.
		$acf = get_fields( $post_id );

		// Create story class.
		$story = new Story();

		if ( ! empty( $acf ) ) {
			$story->acf = $acf;
		}

		// Set story title.
		$story->title = get_the_title( $post_id );

		// Set story type.
		$story->type = get_post_type( $post_id );

		// Set editorial introduction.
		if ( ! empty( $acf['editorial_intro'] ) ) {
			$story->editorial_intro = $acf['editorial_intro'];
		}

		// Set language.
		$language_term = $acf['language'];
		if ( $language_term ) {
			$story->language = $language_term->name;
		}

		// Set category.
		$cat = $acf['category'];
		if ( $cat ) {
			$story->category = $cat->name;
		}

		// Set participant.
		$storyteller = $acf['story_teller'];
		if ( is_object( $storyteller ) ) {
				$story->storyteller = new Participant( $storyteller );
				$story->storyteller->name = $storyteller->post_title;
		}

		// Set tags.
		$terms = $acf['topics'];
		if ( $terms ) {
			foreach ( $terms as $term ) {
				$story->add_tag( $term->name, get_term_link( $term ) );
			}
		}

		// Set special tags.
		if ( $acf['add_special_tags'] ) {
			if ( in_array( 'location', $acf['add_special_tags'], true ) ) {
				$story->add_tag( 'TODO location', 'TODO url' );
			} elseif ( in_array( 'programme_round', $acf['add_special_tags'], true ) ) {
				$story->add_tag( 'TODO programme_round', 'TODO url' );
			}
		}

		// Set sections.
		if ( ! empty( $acf['sections'] ) ) {
			$story->sections = $acf['sections'];
		}

		return $story;
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
}
