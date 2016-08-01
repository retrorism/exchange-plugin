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
	 *  Programme Round slug
	 *
	 * @var $term ID for programme_round tag
	 */
	public $term;

	/**
	 *  Is this progamme round currently running?
	 *
	 * @var bool $is_active for programme_round tag. Defaults to false.
	 */
	public $is_active = false;

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
		Parent::__construct( $post );
		$this->controller->map_programme_round_basics();
	}

	public function publish_grid_programme_round( $modifier = '' ) {
		$prog_name = explode( ' ', $this->title )[1];
		$link = get_post_permalink( $this->post_id );
		$paragraph = ! empty( $this->editorial_intro )
			? $this->editorial_intro->embed_stripped('emphasisblock', 30)
			: '<p>' . __('Click below for more programme information', EXCHANGE_PLUGIN ) . '</p>';
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
						'button_text' => __('Read the stories', EXCHANGE_PLUGIN ),
						'button_help_text' => __('Read the stories', EXCHANGE_PLUGIN ),
						'button_link' => get_post_type_archive_link('story') . '?programme-round=' . $this->term,
						'button_target' => '_self',
					),
				4 => array(
						'acf_fc_layout' => 'block_button',
						'button_size' => 'small',
						'button_text' => __('Learn about the collaborations', EXCHANGE_PLUGIN ),
						'button_help_text' => __('Learn about the collaborations', EXCHANGE_PLUGIN ),
						'button_target' => '_self',
						//'button_link' => get_post_type_archive_link('collaboration') . '?programme-round=' . $this->term,
						'button_link' => get_bloginfo('url') . '?programme-round=' . $this->term,
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
