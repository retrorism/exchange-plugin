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
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
};

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
	 * List of data-attributes for pattern container element.
	 *
	 * @since 0.1.0
	 * @access protected
	 * @var array $data Attributes-list.
	 **/
	protected $data = array();

	/**
	 * List of link-attributes for pattern container element.
	 *
	 * @since 0.1.0
	 * @access protected
	 * @var array $data Link attributes-list.
	 **/
	protected $link_attributes = array();

	/**
	 * Output string
	 *
	 * @since 0.1.0
	 * @access public
	 * @var string $output pattern content.
	 **/
	protected $output;

	/**
	 * List of classes for pattern container element.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var array $output Context for Twig template.
	 **/
	protected $twig_output = array();

	/**
	 * List of classes for pattern container element.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var string $view Twig template name.
	 **/
	protected $view;

	/**
	 * Context in/through which this pattern object has been instantiated.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var mixed $context Parent object or slug name.
	 **/
	public $context;

	/**
	 * Base class name slug as element.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var string $base Class-name as slug to be used as 'element' in BEM css-class.
	 **/
	public $element;

	/**
	 * Constructor for Pattern Base class.
	 *
	 * At instantiation this method fills basename variable, base and parent classes and initial output.
	 *
	 * @since 0.1.0
	 * @access protected
	 *
	 * @param mixed  $input Pattern content.
	 * @param string $context String referring to pattern.
	 * @param array  $modifiers Additional modifiers that influence look and functionality.
	 **/
	protected function __construct( $input, $context = '', $modifiers = array() ) {

		$this->set_basename();
		$this->set_context_and_base_class( $context );

		if ( ! empty( $modifiers ) ) {
			$this->process_modifiers( $modifiers );
		}
		$this->set_initial_output( $input );
	}

	/**
	 * Set object base name.
	 *
	 * @access protected
	 * @since 0.1.0
	 **/
	protected function set_basename() {
		$this->element = strtolower( get_class( $this ) );
	}

	/**
	 * Check if parent is set and add BEM classes.
	 *
	 * @since 0.1.0
	 * @access protected
	 *
	 * @param string $context Context (usually the parent element's element / basename).
	 **/
	protected function set_context_and_base_class( $context ) {
		$this->context = ! empty( $context ) ? $context : '';
		// Add generic story element class for all direct children of a section.
		if ( 'section' === $this->context ) {
			$this->classes['section__default-element'] = 'section__slice';
		}
		// Add
		if ( ! array_key_exists('base__element', $this->classes )
			|| empty( $this->classes['base__element'] ) ) {
			if ( ! empty( $this->context ) ) {
				$this->classes['base__element'] = $this->context . '__' . $this->element;
			}
		} else {
			// Fallback to setting generic class element.
			$this->classes['element'] = $this->element;
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
			$this->output .= '<h1 style="color: red">No output defined for' . $this->element . '</h1>';
			$this->output_tag_close();

			$this->twig_output_set_tag();
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
			$string = 'class="' . esc_attr( implode( ' ', $this->classes ) ) . '"';
			return $string;
		}
	}

	/**
	 * Stringify link attributes for use in HTML.
	 *
	 * @since 0.1.0
	 * @access protected
	 **/
	protected function stringify_link_attributes() {
		if ( ! empty( $this->link_attributes ) ) {
			$list = array();
			foreach ( $this->link_attributes as $key => $val ) {
				if ( ! empty( $val ) ) {
					$list[] = $key . '="' . esc_attr( $val ) . '"';
				}
			}
			if ( count( $list ) > 0 ) {
				$string = implode( ' ', $list );
			}
			return $string;
		}
	}

	/**
	 * Stringify data attributes for use in HTML.
	 *
	 * @since 0.1.0
	 * @access protected
	 *
	 * @TODO move HTML output to template parts instead?
	 **/
	protected function stringify_data() {
		if ( ! empty( $this->data ) ) {
			$list = array();
			foreach ( $this->data as $key => $val ) {
				$list[] = 'data-' . $key . '="' . esc_attr( $val ) . '"';
			}
			if ( count( $list ) > 0 ) {
				$string = implode( ' ', $list );
			}
			return $string;
		}
	}

	/**
	 * Stringify data attributes for use in HTML.
	 *
	 * @since 0.1.0
	 * @access protected
	 *
	 * @param array $modifiers Modifiers array passed in Constructor.
	 **/
	protected function process_modifiers( $modifiers ) {
		foreach ( $modifiers as $key => $val ) {
			switch ( $key ) {
				case 'data' :
					$data_atts = $modifiers[ $key ];
					if ( ! empty( $data_atts ) && is_array( $data_atts ) ) {
						foreach ( $data_atts as $k => $v ) {
							if ( is_string( $k ) ) {
								$this->set_data_attribute( $k, $v );
							}
						}
					}
					break;
				case 'link_attributes' :
					$link_atts = $modifiers[ $key ];
					if ( ! empty( $link_atts ) && is_array( $link_atts ) ) {
						foreach ( $link_atts as $k => $v ) {
							if ( is_string( $k ) ) {
								$this->set_link_attribute( $k, $v );
							}
						}
					}
					break;
				case 'classes' :
					$classes = $modifiers[ $key ];
					if ( ! empty( $classes ) && is_array( $classes ) ) {
						foreach ( $classes as $class ) {
							$this->classes[] = $class ;
						}
					}
					break;
				default :
					$this->set_modifier_class( $key, $val );
					break;
			}
		}
	}

	/**
	 * Add modifier classes.
	 *
	 * @since 0.1.0
	 * @access protected
	 *
	 * @global string $context Parent element may serve to prefix for the entire modifier class.
	 *
	 * @param string $key Identifies what (key) is modified.
	 * @param mixed  $val Contains info on how (val) it is modified.
	 *
	 * @TODO move HTML output to template parts instead?
	 * @TODO create util class for util functions like hex_to_slug
	 **/
	protected function set_modifier_class( $key, $val ) {
		if ( is_string( $val ) ) {
			$class = '';
			if ( 'colour' === $key ) {
				$val = exchange_hex_to_slug( $val );
			}
			if ( ! empty( $this->parent ) ) {
				$class .= $this->parent . '__';
			}
			$class .= $this->element.'--'.$val;
			$this->classes[] = $class;
		}
	}

	/**
	 * Add data-attribute.
	 *
	 * @since 0.1.0
	 * @access protected
	 *
	 * @param string $key Data-attribute identifier.
	 * @param string $val Contains the value for this data-attribute.
	 **/
	protected function set_data_attribute( $key, $val ) {
		if (  null !== $val || 'undefined' !== $val || 'NaN' !== $val ) {
			$this->data[ $key ] = $val;
		}
	}

	/**
	 * Add link-attribute.
	 *
	 * @since 0.1.0
	 * @access protected
	 *
	 * @param string $key Link-attribute identifier.
	 * @param string $val Contains the value for this link-attribute.
	 **/
	protected function set_link_attribute( $key, $val ) {
		if ( ! empty( $val ) && is_string( $val ) ) {
			$this->link_attributes[ $key ] = $val;
		}
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
		return '<!-- end ' . strtolower( $this->element ) . ' -->' . PHP_EOL;
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
		$this->output = '<' . $tag;
		if ( count( $this->classes ) > 0 ) {
			$this->output .= ' ' . $this->stringify_classes();
		}
		if ( count( $this->link_attributes ) > 0 ) {
			$this->output .= ' ' . $this->stringify_link_attributes();
		}
		if ( count( $this->data ) > 0 ) {
			$this->output .= ' ' . $this->stringify_data();
		}
		$this->output .= '>' . $eol;
	}

	/**
	 * Overriding output, part 1: opening tag. Adds EOL suffix for block elements.
	 *
	 * @since 0.1.0
	 * @access protected
	 *
	 * @param string $tag HTML element tag to be opened. Defaults to div.
	 **/
	protected function twig_output_set_tag( $tag = 'div' ) {
		$this->twig_output['tag'] = $tag;
		if ( count( $this->classes ) > 0 ) {
			$this->twig_output['classes'] .= $this->classes;
		}
		if ( count( $this->link_attributes ) > 0 ) {
			$this->twig_output['link_attributes'] = $this->link_attributes;
		}
		if ( count( $this->data ) > 0 ) {
			$this->twig_output['data'] = $this->data;
		}
		$this->twig_output['close_comment'] = $this->end_pattern_tag_comment();
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
	 * Prints escaped pattern output. Make sure to escape anywhere else.
	 *
	 * @since 0.1.0
	 * @access public
	 **/
	public function publish() {
		echo $this->output . PHP_EOL;
	}

	/**
	 * Prints untagged.
	 *
	 * @since 0.1.0
	 * @access public
	 **/
	public function publish_stripped() {
		echo strip_tags( $this->output, '<div><p><em>' ) . PHP_EOL;
	}


	/**
	 * Returns escaped pattern output for use in parent object.
	 *
	 * @since 0.1.0
	 * @access public
	 * @return string $output HTML output consisting of tags and content.
	 **/
	public function embed() {
		return $this->output;
	}

	/**
	 * Returns escaped pattern output for use in parent object.
	 *
	 * @since 0.1.0
	 * @access public
	 * @return array $output HTML output consisting of tags and content.
	 **/
	public function get_twig_context() {
		return $this->twig_output;
	}
}
