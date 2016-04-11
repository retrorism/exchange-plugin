<?php
/**
 * Programme register_column_headers Class
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
 * Programme Round CPT Class
 *
 * This class serves as the foundation for Tandem collaborations and other
 * storytellers.
 *
 * @since 0.1.0
 **/
class ProgrammeRound {

	/**
	 * Contains a reference to the Collaboration controller, once instantiated.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var object $controller Collaboration controller.
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
	 * Constructor for Programme Round objects. If available, the constructor can use
	 * a controller that's already there.
	 *
	 * @since 0.1.0
	 * @access public
	 * @param object $postobj Collaboration post object.
	 * @param object $controller optional CollaborationController object.
	 **/
	public function __construct( $postobj, $controller = null ) {
		$this->set_controller( $controller );
		$this->controller->map_programme_round( $this, $postobj );
	}

	/**
	 * Set controller property to a new instance of Collaboration controller.
	 *
	 * @since 0.1.0
	 * @access private
	 * @param object $controller Controller object (or null);
	 **/
	private function set_controller( $controller ) {
		if ( null === $controller || 'programmeroundcontroller' !== get_class( $controller ) ) {
			$this->controller = new ProgrammeRoundController();
		} else {
			$this->controller = $controller;
		}
	}
}
