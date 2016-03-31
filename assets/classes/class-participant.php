<?php
/**
 * Participant Class
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
 * Participant CPT Class
 *
 * This class serves as the foundation for Tandem participants and other
 * storytellers.
 *
 * @since 0.1.0
 **/
class Participant {

	/**
	 * Contains a reference to the Participant controller, once instantiated.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var object $controller Story controller.
	 **/
	public $controller;

	/**
	 * Name.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var string Name.
	 **/
	public $name;

	/**
	 * Is this indidual currently active in a programme? Defaults to true
	 *
	 * @since 0.1.0
	 * @access public
	 * @var bool $is_active If no longer part of a running programme, this is set to false.
	 */
	public $is_active = true;

	/**
	 * The collaboration this person is involved in. Can only be changed from the
	 * Collaboration admin page.
	 *
	 * @since 0.1.0
	 * @access protected
	 * @var integer $collaboration.
	 */
	protected $collaboration;

	/**
	 * Constructor for participant objects.
	 *
	 * @since 0.1.0
	 * @access public
	 * @param object $post Participant post object.
	 **/
	public function __construct( $post ) {
		$this->set_controller();
	}

	/**
	 * Set controller property to instance of Participant controller.
	 *
	 * @since 0.1.0
	 * @access private
	 **/
	private function set_controller() {
		$this->controller = ParticipantController::get_instance();
	}
}
