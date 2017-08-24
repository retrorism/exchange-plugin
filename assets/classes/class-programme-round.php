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
	 *
	 *
	 * @var string $block_paragraph_text Short intro for Programme Round Card.
	 */
	 public $block_paragraph_text;

	 /**
	  *
	  *
	  * @var string $cta_colour Colour for Programme Round Card.
	  */
	  public $cta_colour;

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
		if ( 'griditem' === $context ) {
			$this->controller->set_block_paragraph_text();
			$this->controller->set_cta_colour();
		}
	}

	public function publish_grid_programme_round( $modifier = '' ) {
		$prog_name = explode( ' ', $this->title )[1];
		$link = get_post_permalink( $this->post_id );
		// Fall back to default colour when no colour is set.
		$cta_colour = ! empty ( $this->cta_colour )
			? $this->cta_colour
			: exchange_slug_to_hex('rose-1-web');

		// Fall back to default paragraph when no intro is set.
		$paragraph = ! empty( $this->block_paragraph_text )
			? $this->block_paragraph_text->embed_stripped('emphasisblock', 30)
			: '<p>' . __('Click below for more programme information', EXCHANGE_PLUGIN ) . '</p>';
		
		// @TODO: add this to globals.
		if ( in_array( $prog_name, array( 'C&P', 'Community', 'C' ), true ) ) {
			$prog_name = 'C_P';
		} elseif ( in_array( $prog_name, array( 'Fryslân' ), true ) ) {
			$prog_name = 'Fryslan';
		}

		$properties = array(
			'block_type' => 'cta',
			'cta_colour' => $cta_colour,
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


	public function create_token_form_cta( $obj, $pr_token = '', $c_obj = null ) {
		if ( empty( $obj->type ) && empty( $obj->post_type ) ) {
			return;
		}
		switch ( $obj->type ) {
			case 'participant' :
				$form_paragraph = __( 'Update information about you and your organisation(s)', EXCHANGE_PLUGIN );
				$cta_colour = exchange_slug_to_hex('rose-1-web');
				$form_title = ! empty( $obj->name ) ? $obj->name : 'participant';
				// Set update link.
				$acf_update_link = $obj->get_update_form_link();
				$update_link = ! empty( $acf_update_link ) ? $acf_update_link : '#';
				break;
			case 'collaboration' :
				$form_paragraph = __( 'Update information on your project', EXCHANGE_PLUGIN );
				$cta_colour = exchange_slug_to_hex('blue-2-web');
				$form_title = ! empty( $obj->title ) ? wp_trim_words( $obj->title, 6, __( '...', EXCHANGE_PLUGIN ) ) : 'collaboration';
				// Set update link.
				$acf_update_link = $obj->get_update_form_link();
				$update_link = ! empty( $acf_update_link ) ? $acf_update_link : '#';
				break;
			default :
				if ( $obj->post_type == 'page' ) {
					$form_paragraph = __( 'Share your Tandem experience on your collaboration page', EXCHANGE_PLUGIN );
					$cta_colour = exchange_slug_to_hex('yellow-tandem');
					$form_title = ! empty( $obj->post_title ) ? $obj->post_title : 'story';
					$permalink = get_post_permalink( $obj->ID );
					if ( $c_obj instanceof Collaboration && is_numeric( $c_obj->post_id ) ) {
						$permalink = add_query_arg('update_id',$c_obj->post_id,$permalink);
					}
					if ( ! empty( $pr_token ) ) {
						$permalink = add_query_arg('pr_ref',$pr_token,$permalink);
					}
					$update_link = ! empty( $permalink ) ? $permalink : '#';
					break;
				} else {
					return;
				}
		}

		// Set paragraph.
		$paragraph = '<p>' . $form_paragraph . '</p>';

		// Set programme name for logo.
		$prog_name = explode( ' ', $this->title )[1];
		if ( in_array( $prog_name, array( 'C&P', 'Community', 'C' ), true ) ) {
			$prog_name = 'C_P';
		}

		$properties = array(
			'block_type' => 'cta',
			'cta_colour' => $cta_colour,
			'block_alignment' => false,
			'cta_block_elements' => array(
				0 => array(
						'acf_fc_layout' => 'block_header',
						'block_header_text' => $form_title,
					),
				1 => array(
						'acf_fc_layout' => 'block_logo',
						'block_programme' => $prog_name,
					),
				2 => array(
						'acf_fc_layout' => 'block_paragraph',
						'block_paragraph_text' => $form_paragraph,
					),
			),
		);
		if ( 'collaboration' === $obj->type ) {

			if ( isset( $obj->link ) ) {
				$properties['cta_block_elements'][3] = array(
					'acf_fc_layout' => 'block_button',
					'button_size' => 'small',
					'button_text' => __('View my collaboration', EXCHANGE_PLUGIN ),
					'button_help_text' => __('Navigate to your collaboration page', EXCHANGE_PLUGIN ),
					'button_link' => $obj->link,
					'button_target' => '_self',
				);
			}
			if (  isset( $form_title ) && isset( $update_link ) ) {
				$properties['cta_block_elements'][4] = array(
					'acf_fc_layout' => 'block_button',
					'button_size' => 'small',
					'button_text' => __('Update my collaboration page', EXCHANGE_PLUGIN ),
					'button_help_text' => sprintf( __('Add or edit information for %s', EXCHANGE_PLUGIN ), $form_title ),
					'button_link' => $update_link,
					'button_target' => '_self',
				);
			}
		} elseif ( 'participant' === $obj->type ) {

			if ( isset( $form_title ) && isset( $update_link ) ) {
				$properties['cta_block_elements'][3] = array(
					'acf_fc_layout' => 'block_button',
					'button_size' => 'small',
					'button_text' => __( 'Update my info', EXCHANGE_PLUGIN ),
					'button_help_text' => sprintf( __('Edit the information about %s', EXCHANGE_PLUGIN ), $form_title ),
					'button_link' => $update_link,
					'button_target' => '_self',
				);
			}
		} elseif ( 'page' === $obj->post_type ) {

			$properties['cta_block_elements'][3] = array(
				'acf_fc_layout' => 'block_button',
				'button_size' => 'small',
				'button_text' => __( 'Submit a story', EXCHANGE_PLUGIN ),
				'button_help_text' => __('Navigate to the page for submitting stories', EXCHANGE_PLUGIN ),
				'button_link' => $update_link,
				'button_target' => '_self',
			);
		}
		// $results = '<pre>' . print_r( $properties, true ) . '</pre>';
		// return $results;
		$block = BasePattern::pattern_factory( $properties, 'emphasis_block', 'griditem', true );
		$griditem_mods = array(
			'grid_width'     => 'grid_fourth',
			'type'           => 'cta',
			'grid_width_num' => 3 );
		$griditem = new Griditem( $block, 'simplegrid', $griditem_mods  );
		return $griditem;
	}

	public function build_pr_dropdown() {
		$collab_set = $this->controller->get_collaborations();
		if ( empty( $collab_set ) ) {
			return;
		}
		$output = '<form class="token-form"><fieldset>';
		$output .= '<input type="hidden" class="token-form__nonce" name="exchange-token-form-nonce" value="' . wp_create_nonce( 'exchange-token-form-nonce' ) . '" />';
		$output .= '<select name="token-form-collab-select" class="token-form__collab-select">';
		$output .= '<option value="null">Select your collaboration</option>';
		foreach ( $collab_set as $collab ) {
			$output .= sprintf( '<option data-programme-round="%s" data-collaboration-token="%s" value="%s">%s</option>', esc_attr( $this->post_id ), esc_attr( $this->post_id ), esc_attr( $collab->ID ), esc_html( $collab->post_title ) );
		}
		$output .= '</select>';
		$output .= '<a class="button--large button--token-form-submit token-form__submit" id="token-form__submit">' . __( 'Go!', EXCHANGE_PLUGIN ) . '</a>';
		$output .= '</fieldset></form>';
		return $output;
	}
}
