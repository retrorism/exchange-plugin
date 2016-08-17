<?php
/**
 * Story Class
 * Author: Willem Prins | SOMTIJDS
 * Author: Bart Bloemers
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
 * Story CPT Class
 *
 * This class serves as the foundation for individual story objects.
 *
 * @since 0.1.0
 **/
class Story extends Exchange {

	/**
	 * Language.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var string Language.
	 **/
	public $language;

	/**
	 * Category by ID.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var integer Category by ID.
	 **/
	public $category;

	/**
	 * Storyteller
	 *
	 * @since 0.1.0
	 * @access public
	 * @var Participant object.
	 **/
	public $storyteller;

	/**
	 * $has_custom_byline
	 *
	 * @since 0.1.0
	 * @access public
	 * @var boolean to see if a custom byline should replace the
	 **/
	public $has_custom_byline = false;

	/**
	 * Constructor for story objects. Sets controller property.
	 *
	 * @since 0.1.0
	 * @access public
	 * @param object $post Story post object.
	 * @param string $context Context, to allow for partial mapping.
	 **/
	public function __construct( $post, $context = '', $controller = null ) {
		Parent::__construct( $post, $controller );
		$this->controller->map_story_basics();

		if ( 'griditem' !== $context ) {
			$this->controller->set_ordered_tag_list();
			$this->controller->map_full_story();
		}
	}

	/**
	 * Publish byline
	 *
	 * @return void
	 */
	public function publish_byline( $context= '' ) {
		if ( isset( $this->byline ) ) {
			$this->byline->publish( $context );
		}
	}
}
