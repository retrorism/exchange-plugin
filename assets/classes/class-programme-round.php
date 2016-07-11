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
class Programme_Round extends Exchange {

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
		$this->controller->map_basics( $this, $post );
		if ( 'grid' === $context ) {
			$this->controller->set_featured_image( $this, $post->ID );
		} else {
			$this->controller->map_full_programme_round( $this, $post );
		}
	}

	public function publish_grid_programme_round( $modifier = '' ) {
		$prog_name = explode( ' ', $this->title )[1];
		$acf_editorial_intro = get_field( 'editorial_intro', $this->post_id );
		$paragraph = ! empty( $acf_editorial_intro ) ? $acf_editorial_intro : __('Click below for more programme information', EXCHANGE_PLUGIN );
		if ( in_array( $prog_name, array( 'C&P', 'Community', 'C' ), true ) ) {
			$prog_name = 'C_P';
		}
		$properties = array(
			'block_type' => 'cta',
			'cta_colour' => '#' . $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['COLOURS']['rose-1-web'],
			'block_alignment' => false,
			'cta_block_elements' => array(
				0 => array(
						'acf_fc_layout' => 'block_header',
						'block_header_text' => $this->title,
					),
				1 => array(
						'acf_fc_layout' => 'block_logo',
						'block_programme' => $prog_name,
					),
				2 => array(
						'acf_fc_layout' => 'block_paragraph',
						'block_paragraph_text' => $paragraph,
					),
				3 => array(
						'acf_fc_layout' => 'block_button',
						'button_size' => 'small',
						'button_text' => __('Read more', EXCHANGE_PLUGIN ),
						'button_help_text' => __('See all collaborations', EXCHANGE_PLUGIN ),
						'button_link' => '#',
						'button_target' => '_self',
					),
				4 => array(
						'acf_fc_layout' => 'block_button',
						'button_size' => 'small',
						'button_text' => __('All collaborations', EXCHANGE_PLUGIN ),
						'button_help_text' => __('See all collaborations', EXCHANGE_PLUGIN ),
						'button_target' => '_self',
						'button_link' => '#',
					),
			),
		);
		if ( $modifier === 'grid_full' ) {
			$properties['block_alignment'] = 'full';
		}
		$block = BasePattern::pattern_factory( $properties, 'emphasis_block', 'griditem' );

		echo $block;
	}
}
