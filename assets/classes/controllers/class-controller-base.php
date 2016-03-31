<?php
/**
 * Base Controller Class
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 11/2/2016
 *
 * @package Exchange Plugin
 *
 * @link Via http://stackoverflow.com/questions/8091143/how-to-check-for-a-specific-type-of-object-in-php
 **/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
};

/**
 * Base Controller.
 *
 * This class contains all common controller logic. It's a singleton, so that
 * the individual controllers only need to be instantiated once.
 *
 * @since 0.1.0
 **/
class BaseController {

	/**
	 * Constructor for Base controller
	 *
	 * @since 0.1.0
	 * @access protected
	 **/
	public function __construct() {
	}

	/**
	 * Instantiated (singleton) object.
	 *
	 * @since 0.1.0
	 * @var object $instance Controller object, once instantiated.
	 */
	protected static $instance;

	/**
	 * Common config for all controllers.
	 *
	 * @since 0.1.0
	 * @var array $config Configuration settings.
	 */
	protected static $config = array();

	/**
	 * Intercepts creation of clones.
	 *
	 * @since 0.1.0
	 * @access public
	 *
	 * @throws RuntimeException When system attempts to clone this singleton.
	 **/
	private function __clone() {
		throw new RuntimeException;
	}

	/**
	 * Sets config variable.
	 *
	 * @since 0.1.0
	 * @access public
	 * @param array $config Configurations to be assigned to controller.
	 **/
	public static function set_config( $config ) {
		self::$config = $config;
	}

	/**
	 * Checks if instance is already present, otherwise instantiates.
	 *
	 * @since 0.1.0
	 * @access public
	 **/
	public static function get_instance() {
		if ( null === self::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}
}
