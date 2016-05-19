<?php
/**
 * Exchange Class
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 12/4/2016
 *
 * @package Exchange Plugin
 * TODO Proper namespacing (http://stackoverflow.com/a/30647705);
 **/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
};

/**
 * Exchange CPT Class
 *
 * This class serves as the foundation for the 4 Tandem Exchange Content Types
 * storytellers.
 *
 * @since 0.1.0
 **/
class Exchange {

	/**
	 * Contains a reference to the Exchange Base controller, once instantiated.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var object $controller Exchange controller.
	 **/
	public $controller;

	/**
	 * Title.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var string Title.
	 **/
	public $title;

	/**
	 * The permalink.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var string Link.
	 **/
	public $link;

	/**
	 * Featured image.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var Image $header_image Header image object.
	 **/
	public $featured_image;

	/**
	 * Has featured image check
	 *
	 * @since 0.1.0
	 * @access public
	 * @var Bool $has_featured_image Set when featured image is set.
	 **/
	public $has_featured_image = false;

	/**
	 * Header image.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var Image $header_image Header image object.
	 **/
	public $header_image;

	/**
	 * Has header image.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var Bool $has_header_image Set to true when header image is set.
	 **/
	public $has_header_image = false;

	/**
	 * Ordered array for use in grid / single display.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var array $ordered_tag_list Ordered tag-list.
	 **/
	public $ordered_tag_list = array();

	/**
	 * Array to be filled with tags.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var array Tag-list.
	 **/
	public $tag_list = array();

	/**
	 * Has tags
	 *
	 * @since 0.1.0
	 * @access public
	 * @var Bool $has_tags Set to true when tags are set.
	 **/
	public $has_tags;

	/**
	 * List of sections.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var array $sections Sections list, built up out of pattern classes.
	 **/
	public $sections = array();

	/**
	 * Has related content check
	 *
	 * @since 0.1.0
	 * @access public
	 * @var Bool $has_related_content Set when related content is set.
	 **/
	public $has_related_content = false;

	/**
	 * When set, this variable contains a Related Content Grid object.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var object $related_content Related Content Grid object.
	 **/
	public $related_content;

	/**
	 * Constructor for all CPT objects.
	 *
	 * @since 0.1.0
	 * @access public
	 * @param object $post CPT WP_Post object.
	 * @param string $context Optional. Where this object is created.
	 * @param object $controller Optional. Add a (modified) controller to be used.
	 **/
	public function __construct( $post, $context = '', $controller = null ) {
		$this->set_controller( $controller );
		$this->controller->map_basics( $this, $post );
	}

	/**
	 * Set controller property to a new instance of Collaboration controller.
	 *
	 * @since 0.1.0
	 * @access private
	 * @param object $controller Controller object (or null).
	 **/
	protected function set_controller( $controller ) {
		$controller_name = get_class( $this ) . 'Controller';
		if ( null === $controller || get_class( $controller ) !== $controller_name ) {
			$this->controller = new $controller_name;
		} else {
			$this->controller = $controller;
		}
		$this->controller->set_container( $this );
	}

	public function publish_featured_image( $context = '' ) {
		if ( $this->has_featured_image ) {
			$this->featured_image->publish( $context );
		}
	}


	// /**
	//  * Add tag to tag list, accompanied by its archive link.
	//  *
	//  * @since 0.1.0
	//  * @access public
	//  *
	//  * @param string $name Term name.
	//  * @param string $link Archive link.
	//  **/
	// public function add_tag( $name, $link ) {
	// 	$this->tag_list[] = array(
	// 		'name' => $name,
	// 		'link' => $link,
	// 	);
	// }

	public function publish_header_image() {
		if ( $this->has_header_image ) {
			$this->header_image->publish();
		}
	}

	public function publish_sections() {
		$section_mods = array();
		if ( count( $this->sections ) > 0 ) {
			// Loop through sections.
			foreach( $this->sections as $s ) {
				if ( ! empty( $s['section_contents'] ) ) {
					$section_mods['type'] = $s['section_contents'];
				}
				$section = new Section( $s, strtolower( get_class( $this ) ), $section_mods );
				$section->publish();
			}
		}
	}

	public function publish_related_content( $context = '' ) {
		if ( $this->has_related_content ) {
			$this->related_content->publish( $context );
		}
	}

	public function publish_tags( $context = '' ) {
		if ( $this->has_tags ) {
			$output = "<ol>" . PHP_EOL;
			if ( 'griditem' === $context ) {
			 	$list = $this->controller->get_tag_short_list();
			} else {
				$list = $this->ordered_tag_list;
			}
			foreach ( $list as $term ) {
				$tag_mods = $this->controller->prepare_tag_modifiers( $term );
				$tag = new Tag( $term, $context, $tag_mods );
				$output .= "<li>" . $tag->embed() . "</li>" . PHP_EOL;
			}
			$output .= "</ol>" . PHP_EOL;
			echo $output;
		}
	}
}
