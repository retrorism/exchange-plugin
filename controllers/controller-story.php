<?php

/*
 * Controller Class
 * Author: Willem Prins | SOMTIJDS
 * Project Tandem
 * Date created: 11/2/2016
 */

class StoryController extends BaseController {

	public function __construct() {
		//parent::__construct($post_id_or_post_object);
	}

	function mapStory($post) {

		// retrieve post_id variable or throw Exception
  	$post_id = $post->ID;
  	if ($post_id < 1) {
  		throw new Exception("This is no valid post");
  	}

		// dump ACF variables
		$acf = get_fields( $post_id );

		// create story class
  	$story = new Story();

		if ( $acf ) {
			$story->acf = $acf;
		}

		// set story title
  	$story->title = get_the_title( $post_id  );

		// set story type
		$story->type = get_post_type( $post_id );

		// set editorial introduction
		if ( !empty( $acf['editorial_intro'] ) ) {
  		$story->editorial_intro = $acf['editorial_intro'];
		}

  	// set language
  	$language_term = $acf['language'];
  	if ( $language_term ) {
			$story->language = $language_term->name;
		}

		// set category
		$cat = $acf['category'];
		if ( $cat ) {
			$story->category = $cat->name;
		}

		// set particpant
		$storyteller = $acf['story_teller'];
    	if ( $storyteller ) {
				$story->storyteller = new Participant();
				$story->storyteller->name = $storyteller->post_title;
    	}

		//set tags
		$terms = $acf['topics'];
		if( $terms ) {
			foreach( $terms as $term ) {
				$story->addTag($term->name, get_term_link( $term ));
			}
		}

		// set special tags
		if ( $acf['add_special_tags'] ) {
			if( in_array( 'location', $acf['add_special_tags'] ) )
			{
				$story->addTag('TODO location', 'TODO url');
			}
			elseif ( in_array( 'programme_round', $acf['add_special_tags'] ) )
			{
				$story->addTag('TODO programme_round', 'TODO url');
			}
		}

		// set sections
		if ( !empty( $acf['sections'] ) ) {
			$story->sections = $acf['sections'];
		}

    return $story;
  }

	/**
   * Returns one story
	 * @return Story or null
   */

	function getStory( $post_id_or_object ) {
		if ( $post_id_or_object ) {
    	$post = get_post( $post_id_or_object );
   		return $this->mapStory( $post ) ;
   	} else {
   		return null;
   	}
	}

	protected function executeQuery( $args ) {
		$query = new WP_Query( $args );
		return $query->posts;
	}

	private function getStorySet( $args ) {
		$posts = $this->executeQuery( $args );
		$stories = array();
		foreach( $posts as $p ) {
			$stories[] = $this->mapStory( $p );
		}
		return $stories;
	}

	/**
    * Returns all stories
  	* @return array of stories
    */
	function getAllStories() {
		$stories = array();
			foreach( $this->getAllStoryPosts() as $p ){
			$stories[] = $this->mapStory( $p );
		}
		return $stories;
	}

	/**
    * Returns all story posts
  	* @return array of story post objects
    */
	function getAllStoryPosts() {
		$args = array(
			'post_type' => 'story'
		);
		$query = new WP_Query( $args );
		return $query->posts;
	}

	function getStoriesByTaxonomy( $taxonomy, $tax_name ) {
		$args = array(
			'post_type' => 'story',
			'tax_query' => array(
				array(
					'taxonomy' => ''.$taxonomy,
					'field'    => 'name',
					'terms'    => ''.$tax_name,
				),
			)
		);
		return $this->getStorySet($arg);
	}

	function getStoriesByProgramme_round($taxParams) {
	}


	function getStoriesByCollaboration($collaborationID) {
	}

}
