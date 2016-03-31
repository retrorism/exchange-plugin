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
	exit;
}

/**
 * Story CPT Class
 *
 * This class serves as the foundation for individual story objects.
 *
 * @since 0.1.0
 **/
class Story {

	/**
	 * Array to be filled with tags.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var array Tag-list.
	 **/
	private $taglist = array();

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
	 * Story title.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var string Story title.
	 **/
	public $title;

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
		return $this->taglist;
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

		foreach ( $this->taglist as $tag ) {
			if ( count( $shortlist ) > 2 ) {
				continue;
			}

			$shortlist[] = $tag;
		}
		return $shortlist;
	}
}
