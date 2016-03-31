<?php
/**
 * Pattern Base Class
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 07/03/2016
 *
 * @package Exchange Plugin
 **/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Base pattern class for all frontend patterns.
 *
 * This class provides the basis for frontend patterns to be used in pages and stories (posts) that
 * have been created with ACF.
 *
 * @since 0.1.0
 **/
abstract class BasePattern {

	/**
	 * List of classes for pattern container element.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var array $classes Class-list.
	 **/
	public $classes = array();

	/**
	 * List of classes for pattern container element.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var string $output HTML containing pattern content.
	 **/
	public $output = '';

	/**
	 * Parent object in/through which this pattern object has been instantiated.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var mixed $parent Parent object or slug name.
	 **/
	public $parent;

	/**
	 * Base class name slug.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var string $base Class-name as slug to be used in css-class.
	 **/
	public $base;


	/**
	 * Constructor for Pattern Base class.
	 *
	 * At instantiation this method fills basename variable, base and parent classes and initial output.
	 *
	 * @since 0.1.0
	 * @access protected
	 *
	 * @param mixed  $input Pattern content.
	 * @param string $parent String referring to pattern.
	 * @param array  $modifiers Additional modifiers that influence look and functionality.
	 **/
	protected function __construct( $input, $parent, $modifiers ) {

		$this->set_basename();
		$this->set_parent_and_base_class( $parent );
		$this->set_initial_output( $input );

	}

	/**
	 * Set object base name.
	 *
	 * @access protected
	 * @since 0.1.0
	 **/
	protected function set_basename() {
		$this->base = strtolower( get_class( $this ) );
	}

	/**
	 * Check if parent is set and add BEM classes.
	 *
	 * @since 0.1.0
	 * @access protected
	 *
	 * @param string $parent Parent basename.
	 **/
	protected function set_parent_and_base_class( $parent ) {
		if ( isset( $parent ) ) {
			$this->parent = $parent;
			// Add generic story element class for all direct children of a section.
			if ( 'section' === $this->parent ) {
				$this->classes[] = 'section__story-element';
			}
			// Add parent classes.
			$this->classes[] = $this->parent . '__' . $this->base;
		} else {
			// Fallback to setting generic class element.
			$this->classes[] = $this->base;
		}
	}

	/**
	 * Set initial output value. Will be overwritten by individual Patterns.
	 *
	 * @since 0.1.0
	 * @access protected
	 *
	 * @param string $input Uescaped input.
	 *
	 * @TODO move HTML output to template parts instead?
	 * @TODO escape input here?
	 **/
	protected function set_initial_output( $input ) {
		if ( ! empty( $input ) ) {
			$this->output_tag_open();
			$this->output .= $input;
			$this->output_tag_close();
		}
	}

	/**
	 * Stringify classes for use in HTML.
	 *
	 * @since 0.1.0
	 * @access protected
	 *
	 * @TODO move HTML output to template parts instead?
	 **/
	protected function stringify_classes() {
		if ( ! empty( $this->classes ) ) {
			$string = implode( ' ', $this->classes );
			return esc_attr( $string );
		}
	}

	/**
	 * Add modifier classes.
	 *
	 * @since 0.1.0
	 * @access protected
	 *
	 * @global string $parent Parent element may serve to prefix for the entire modifier class.
	 *
	 * @param string $key Identifies what (key) is modified.
	 * @param mixed  $val Contains info on how (val) it is modified.
	 *
	 * @TODO move HTML output to template parts instead?
	 * @TODO create util class for util functions like hex_to_slug
	 **/
	protected function set_modifier_classes( $key, $val ) {
		$class = '';
		if ( 'colour' === $key ) {
			$val = tandem_hex_to_slug( $val );
		}
		if ( ! empty( $this->parent ) ) {
			$class .= $this->parent . '__';
		}
		$class .= $this->base.'--'.$val;
		$this->classes[] = $class;
	}

	/**
	 * Adds closing HTML comment for pattern container.
	 *
	 * @since 0.1.0
	 * @access protected
	 *
	 * @return string HTML closing comment.
	 **/
	protected function end_pattern_tag_comment() {
		return '<!-- END ' . strtoupper( $this->base ) . ' ELEMENT-->' . PHP_EOL;
	}

	/**
	 * Overriding output, part 1: opening tag. Adds EOL suffix for block elements.
	 *
	 * @since 0.1.0
	 * @access protected
	 *
	 * @param string $tag HTML element tag to be opened. Defaults to div.
	 **/
	protected function output_tag_open( $tag = 'div' ) {
		if ( in_array( $tag, array( 'p', 'span', 'a', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ), true ) ) {
			$eol = '';
		} else {
			$eol = PHP_EOL;
		}
		$this->output = '<' . $tag . ' class="' . $this->stringify_classes() . '">' . $eol;
	}

	/**
	 * Overriding output, part 2: closing tag
	 *
	 * @since 0.1.0
	 *
	 * @param string $tag HTML element tag to be closed. Defaults to div.
	 **/
	protected function output_tag_close( $tag = 'div' ) {
		$this->output .= '</' . $tag . '>' . $this->end_pattern_tag_comment();
	}

	/**
	 * Prints escaped pattern output. Make sure not to escape anywhere else.
	 *
	 * @since 0.1.0
	 * @access protected
	 **/
	public function publish() {
		echo $this->output;
	}

	/**
	 * Returns escaped pattern output for use in parent object.
	 *
	 * @since 0.1.0
	 *
	 * @return string $output HTML output consisting of tags and content.
	 **/
	public function embed() {
		return $this->output;
	}
}
