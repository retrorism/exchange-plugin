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
	}

	public function publish_featured_image() {
		if ( $this->has_featured_image ) {
			$this->featured_image->publish();
		}
	}
}
