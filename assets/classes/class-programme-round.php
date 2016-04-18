<?php
/**
 * Programme Round Class
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
class ProgrammeRound extends Exchange {

	/**
	 * Constructor for Programme Round objects. If available, the constructor can use
	 * a controller that's already there.
	 *
	 * @since 0.1.0
	 * @access public
	 * @param object $post Collaboration post object.
	 * @param object $controller optional CollaborationController object.
	 **/
	public function __construct( $post, $context = '', $controller = null ) {
		Parent::__construct( $post, $context, $controller );
		$this->controller->map_collaboration_round_basics( $this, $post );
		if ( 'grid' === $context ) {
			$this->controller->set_featured_image( $this, $post->ID );
		} else {
			$this->controller->map_programme_round( $this, $post );
		}
	}
}
