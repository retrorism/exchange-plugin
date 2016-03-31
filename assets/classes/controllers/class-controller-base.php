<?php
/**
 * Base Controller Class
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
 * Base Controller.
 *
 * This class contains all common controller logic.
 *
 * @since 0.1.0
 **/
class BaseController  {

	/**
	 * Constructor for Base controller
	 *
	 * @since 0.1.0
	 * @access public
	 **/
	public function __construct() {

		if ( $post_id_or_object ) {
			$post = get_post( $post_id_or_object );
			if ( $post ) {
				$this->post = $post;
				$this->post_id = $post->ID;
			}
		}

	}
}
