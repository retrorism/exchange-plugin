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
	 * Array to be filled with tags.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var array Tag-list.
	 **/
	private $tag_list = array();

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
	 * Header image.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var Image $header_image Header image object.
	 **/
	public $header_image;

	/**
	 * Storyteller
	 *
	 * @since 0.1.0
	 * @access public
	 * @var Participant object.
	 **/
	public $storyteller;

	/**
	 * Has header image.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var Bool $has_header_image Set to true when header image is set.
	 **/
	public $has_header_image = false;

	/**
	 * Has editorial intro check
	 *
	 * @since 0.1.0
	 * @access public
	 * @var Bool $has_editorial_intro Set to true when editorial intro is set.
	 **/
	public $has_editorial_intro = false;

	/**
	 * Editorial Intro text taken from excerpt (needs to allow for links).
	 *
	 * @since 0.1.0
	 * @access public
	 * @var string $editorial_intro Editorial Intro.
	 *
	 * @TODO Allow for links.
	 **/
	public $editorial_intro;

	/**
	 * Constructor for story objects. Sets controller property.
	 *
	 * @since 0.1.0
	 * @access public
	 * @param object $post Story post object.
	 * @param string $context Context, to allow for partial mapping.
	 **/
	public function __construct( $post, $context = '', $controller = null ) {
		Parent::__construct( $post );
		$this->controller->map_story_basics( $this, $post );
		if ( 'grid' === $context ) {
			$this->controller->set_featured_image( $this, $post->ID );
		} else {
			$this->controller->map_full_story( $this, $post );
		}
	}

	/**
	 * Set story properties.
	 *
	 * @since 0.1.0
	 * @access public
	 **/

	/**
	 * Add tag to tag list, accompanied by its archive link.
	 *
	 * @since 0.1.0
	 * @access public
	 *
	 * @param string $name Term name.
	 * @param string $link Archive link.
	 **/
	public function add_tag( $name, $link ) {
		$this->taglist[] = array(
			'name' => $name,
			'link' => $link,
		);
	}

	/**
	 * Returns all tags for this story.
	 *
	 * @since 0.1.0
	 * @access public
	 *
	 * @return array $taglist List of tags.
	 **/
	public function get_tag_list() {
		return $this->tag_list;
	}

	/**
	 * Returns short list of tags (no more than 2) for this story.
	 *
	 * @since 0.1.0
	 * @access public
	 *
	 * @return array $shortlist List of tags.
	 *
	 * @TODO Turn limit into theme option.
	 **/
	public function get_tag_short_list() {
		$shortlist = array();

		foreach ( $this->tag_list as $tag ) {
			if ( count( $shortlist ) > 2 ) {
				continue;
			}

			$shortlist[] = $tag;
		}
		return $shortlist;
	}

	public function publish_header_image() {
		if ( null !== $this->header_image ) {
			$this->header_image->publish();
		}
	}

	public function publish_intro() {
		if ( $this->has_editorial_intro ) {
			$this->editorial_intro->publish();
		}
	}

	public function publish_byline() {
		if ( null != $this->byline ) {
			$this->byline->publish();
		}
	}

	public function publish_sections() {
		// Loop through sections.
		foreach( $this->sections as $s ) {
			$section = new Section( $s, strtolower( get_class( $this ) ) );
			$section->publish();
		}
	}

	public function publish_related_content() {
		if ( $this->has_related_content ) {
			$this->related_content->publish();
		}
	}
}
