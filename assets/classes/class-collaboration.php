<?php
/**
 * Collaboration Class
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
 * Collaboration CPT Class
 *
 * This class serves as the foundation for Tandem collaborations and other
 * storytellers.
 *
 * @since 0.1.0
 **/
class Collaboration extends Exchange {

	/**
	 * The programme round this collaboration was a part of.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var integer $programme_round Programme round post ID, defined as parent_id.
	 */
	public $programme_round;

	/**
	 * Constructor for collaboration objects.
	 *
	 * @since 0.1.0
	 * @access public
	 * @param object $post Collaboration post object.
	 * @param string $context Optional. Added context for modifications.
	 * @param object $controller Optional. Add existing controller if you want.
	 **/
	public function __construct( $post, $context = '', $controller = null ) {
		Parent::__construct( $post, $context, $controller );
		$this->controller->map_collaboration_basics( $this, $post );
	}
}
