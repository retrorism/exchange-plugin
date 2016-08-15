<?php
/**
 * Grid Item Class
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 12/04/2016
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
 * GridItem pattern class.
 *
 * This class serves to build Item elements.
 *
 * @since 0.1.0
 **/
class GridItem extends BasePattern {

	/**
	 * Overwrite initial output value for Grid items.
	 *
	 * @since 0.1.0
	 * @access protected
	 **/
	 protected function create_output() {

		if ( ! is_object( $this->input ) ) {
			return;
		}
		$this->output_tag_open();
		if ( $this->input instanceof Exchange ) {
 			$this->output .= $this->build_grid_item_from_post();
		} elseif ( $this->input instanceof BasePattern ) {
			$this->output .= $this->build_grid_item_from_pattern();
		}
		$this->output_tag_close();
	}

	/**
	 * Build Item output
	 *
	 * @since 0.1.0
	 * @TODO switch for context, switch for grid width
	 **/
	protected function build_grid_item_from_post( $cta = false ) {
		if (
			( 'archive__grid' === $this->context && 'everywhere' === $this->input->has_cta ) ||
			( ( 'simplegrid' === $this->context || 'relatedgrid' === $this->context ) && 'no' !== $this->input->has_cta )
		) {
			$cta = true;
		}
		if ( 'featuredgrid' === $this->context && locate_template( 'parts/grid-featured.php') !== '' ) {
			if ( isset( $this->modifiers['grid_width_num'] ) && 12 === $this->modifiers['grid_width_num'] ) {
				$template = 'featured';
			} else {
				$template = 'default';
			}
		} elseif ( $cta && locate_template( 'parts/grid-cta.php') !== '' ) {
			$template = 'cta';
		} elseif ( locate_template( 'parts/grid-' . $this->input->type . '.php' ) !== '') {
			$template = $this->input->type;
		} elseif ( locate_template( 'parts/grid-default.php' ) !== '') {
			$template = 'default';
		} else {
			$template = false;
		}
		if ( $template ) {
			$exchange = $this->input;
			$modifier = false;
			if ( isset( $this->modifiers['grid_width'] ) ) {
				$modifier = $this->modifiers['grid_width'];
			}
			ob_start();
			include( locate_template( 'parts/grid-' . $template .'.php' ) );
			$grid_item = ob_get_contents();
			ob_end_clean();
		} else {
			$grid_item = "I couldn't find the right template";
		}
		return $grid_item;
	}

	/**
	 * Build Item output
	 *
	 * @since 0.1.0
	 * @TODO switch for context, switch for grid width
	 **/
	protected function build_grid_item_from_pattern() {
		if ( locate_template( 'parts/grid-pattern.php' ) !== '') {
			$template = 'pattern';
		} else {
			$template = 'default';
		}
		if ( $template ) {
			$pattern = $this->input;
			$modifier = false;
			if ( isset( $this->modifiers['grid_width'] ) ) {
				$modifier = $this->modifiers['grid_width'];
			}
			ob_start();
			include( locate_template( 'parts/grid-' . $template .'.php' ) );
			$grid_item = ob_get_contents();
			ob_end_clean();
		} else {
			$grid_item = "I couldn't find the right template";
		}
		return $grid_item;
	}
}
