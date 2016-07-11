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
	 * @param object $this->container Newly instantiated Story class object.
	 * @param object $post Story post object.
	 *
	 * @throws Exception When no valid post ID is presented.
	 **/
	public function map_story_basics() {

		// Retrieve post_id variable.
		$post_id = $this->container->post_id;

		// Map ACF variables.
		$acf_editorial_intro = get_field( 'editorial_intro', $post_id );
		$acf_language = get_field( 'language', $post_id );
		$acf_storyteller = get_field( 'storyteller', $post_id );
		$acf_category = get_field( 'category', $post_id );
		$acf_has_cta = get_field( 'has_cta', $post_id );

		// Set editorial introduction.
		if ( ! empty( $acf_editorial_intro ) ) {
			$this->container->has_editorial_intro = true;
			$this->container->editorial_intro = new EditorialIntro( $acf_editorial_intro, 'story' );
		}

		// Set language.
		if ( is_object( $acf_language ) ) {
			if ( 'WP_Term' === get_class( $acf_language ) ) {
				$this->container->language = $acf_language;
			}
		}

		// Set category.
		if ( is_object( $acf_category ) ) {
			if ( 'WP_Term' === get_class( $acf_category ) ) {
				$this->container->category = $acf_category->name;
			}
		}

		// Set participant as storyteller
		if ( is_numeric( $acf_storyteller ) ) {
			$storyteller = self::exchange_factory( $acf_storyteller );
			if ( is_a( $storyteller, 'Participant' ) ) {
				$this->container->storyteller = $storyteller;
			}
		}

		// Set CTA check
		if ( ! empty( $acf_has_cta ) ) {
			$this->container->has_cta = $acf_has_cta;
		}

		// Add featured image
		$this->set_featured_image();

		// Add tags
		$this->set_ordered_tag_list();

	}
	/**
	 * Return story object with properties taken from ACF Fields.
	 *
	 * @since 0.1.0
	 * @access public
	 *
	 * @throws Exception When no valid post ID is presented.
	 * @return void;
	 **/
	public function map_full_story() {

		// Retrieve post_id variable from basic mapping.
		$post_id = $this->container->post_id;


		// Set language global to language category
		if ( isset( $this->container->language ) ) {
			$GLOBALS['story_language'] = $this->container->language->name;
		}

		// Throw Exception when the input is not a valid story post type object.
		if ( ! ( $post_id >= 1 ) ) {
			unset( $this->container );
			throw new Exception( 'This is not a valid post' );
		}

		$acf_sections = get_field( 'sections', $post_id );

		// Get related
		if ( get_field( 'related_content_auto_select', $post_id ) ) {
			$related_content = $this->get_related_grid_content_by_tags();
		} else {
			$related_content = get_field( 'related_content', $post_id );
		}

		if ( is_array( $related_content ) && count( $related_content ) > 0 ) {
			$this->container->has_related_content = true;
			$this->set_related_grid_content( $related_content );
		}
		$acf_has_custom_byline = get_field( 'has_custom_byline', $post_id );

		// Set sections.
		if ( ! empty( $acf_sections ) ) {
			$this->set_sections( $acf_sections );
		}

		// Set header image.
		$this->set_header_image( $post_id, 'story__header' );

		if ( is_object( $this->container->storyteller ) ) {
			$this->set_byline();
		} else {
			$this->set_custom_byline();
		}

		$this->set_gallery();
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
	 * Return a story set.
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
	 * @access protected
	 **/
	protected function set_byline() {
		if ( ! is_object( $this->container->storyteller ) ) {
			return;
		}
		if ( ! is_object( $this->container->storyteller->collaboration ) ) {
			return;
		}
		$templates = $this->get_byline_templates();

		if ( $this->container->storyteller->is_active ) {
			$byline_template = $templates['present'];
		} else {
			$byline_template = $templates['past'];
		}

		$byline_template = str_replace( '[[storyteller]]', $this->container->storyteller->name, $byline_template );
		$byline_template = str_replace( '[[programme_round]]', exchange_create_link( $this->container->storyteller->collaboration->programme_round ), $byline_template );
		$byline = str_replace( '[[collaboration]]', exchange_create_link( $this->container->storyteller->collaboration ), $byline_template );
		$this->container->byline = new Byline( $byline, 'footer' );
	}

	/**
	 * If storyteller is set, Replace placeholders in template with personal details connected to the storyteller.
	 *
	 * @since 0.1.0
	 * @access protected
	 **/
	protected function set_custom_byline() {
		$acf_custom_byline = get_field( 'custom_byline', $this->container->post_id );
		if ( ! empty( $acf_custom_byline ) ) {
			$this->container->has_custom_byline = true;
			$this->container->byline = new Byline( $acf_custom_byline, 'footer' );
		}
		else {
			$this->set_byline();
		}
	}
}
