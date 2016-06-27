
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
	 * List of classes for pattern container element.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var string $output HTML containing pattern content.
	 **/
	public $output = '';

	/**
	 * Input content, stored for use when rendering
	 *
	 * @since 0.1.0
	 * @access public
	 * @var mixed $input
	 **/
	protected $input;

	/**
	 * Context in/through which this pattern object has been instantiated.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var mixed $context Parent object or slug name.
	 **/
	public $context;

	/**
	 * Modifiers provided at instantiation, stored for later use
	 *
	 * @since 0.1.0
	 * @access public
	 * @var array $modifiers Associatiative array containing modifier-types (keys) and values.
	 **/
	protected $modifiers;


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
	 *
	 * @throws exception when input is empty.
	 **/
	public function __construct( $input, $context = '', $modifiers = array() ) {

		/* Check if the context (for example the patterns parent)
		 * provided at instantiation is not empty, then assign it.
		 */
		$this->context = ! empty( $context ) ? $context : '';

		// Check if the modifiers provided at instantiation are not empty.
		if ( !empty( $modifiers ) ) {
			$this->modifiers = $modifiers;
		}

		// Check if the input isn't empty, else throw error.
		if ( empty( $input ) ) {
			unset( $this );
			//throw new Exception('No input provided for this pattern');
		} else {
			$this->input = $input;
		}

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
		// Override base context from 'publish / embed' calls.
		if ( ! empty( $context ) ) {
			$this->context = $context;
		}
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
	protected function process_modifiers() {
		foreach ( $this->modifiers as $key => $val ) {
			switch ( $key ) {
				case 'data' :
					$data_atts = $this->modifiers[ $key ];
					if ( ! empty( $data_atts ) && is_array( $data_atts ) ) {
						foreach ( $data_atts as $k => $v ) {
							if ( is_string( $k ) ) {
								$this->set_data_attribute( $k, $v );
							}
						}
					}
					break;
				case 'link_attributes' :
					$link_atts = $this->modifiers[ $key ];
					if ( ! empty( $link_atts ) && is_array( $link_atts ) ) {
						foreach ( $link_atts as $k => $v ) {
							if ( is_string( $k ) ) {
								$this->set_link_attribute( $k, $v );
							}
						}
					}
					break;
				case 'classes' :
					$classes = $this->modifiers[ $key ];
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
		$this->output .= '<' . $tag;
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
	 * Set optional section header, if given and embed it into this section's output.
	 *
	 * @since 0.1.0
	 * @global $base Object base name is passed to embeddable child.
	 **/
	protected function build_edge_svg( $pos, $colour ) {
		return '<svg class="section__edge--' . $pos . '" viewBox="0 0 840 20" preserveAspectRatio="none"><desc>Created with Snap</desc><polygon points=" 0,20,0 10,13 12,22 13,27 11,27 12,31 12,37 10,46 13,46 12,54 12,62 13,70 12,77 13,86 10,89 11,91 11,98 11,106 13,106 12,115 13,119 10,128 11,128 10,135 11,136 12,142 12,149 13,156 12,156 13,157 13,159 11,166 11,167 13,174 13,183 13,191 12,191 12,200 10,207 13,212 13,216 11,224 10,233 13,241 10,246 13,255 11,261 11,268 13,274 10,279 10,288 13,289 10,292 10,296 11,301 12,309 12,314 11,319 12,321 10,328 11,334 13,339 12,343 11,346 11,347 12,355 10,362 11,364 11,366 10,371 10,371 11,372 10,372 12,374 11,380 12,381 10,384 11,391 11,400 13,404 11,410 11,413 11,417 10,426 13,430 10,437 10,444 12,444 12,451 13,456 11,459 11,467 10,475 11,482 11,487 10,496 10,500 13,506 12,510 13,519 13,523 10,526 12,527 10,531 11,538 11,544 12,544 10,544 10,545 10,548 12,548 10,551 12,552 10,554 11,562 11,566 11,569 12,573 10,575 13,584 13,592 11,601 12,607 12,612 11,615 11,624 12,631 12,634 12,637 11,641 12,649 11,650 11,652 13,652 13,656 10,661 13,668 10,670 10,676 10,680 13,684 12,687 13,691 10,692 11,696 11,698 10,704 13,708 10,709 11,711 11,714 12,715 12,717 10,724 13,725 10,732 12,740 10,740 13,740 10,747 13,751 10,751 12,752 10,752 10,761 13,762 12,769 10,778 12,778 11,783 12,788 12,796 10,804 12,804 13,811 12,814 10,820 10,821 10,821 11,825 12,834 11,838 10,844 20,850 20" fill="' . $colour . '"></polygon></svg>';
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
	 * Set initial output value. Will be overwritten by individual Patterns.
	 *
	 * @since 0.1.0
	 * @access protected
	 *
	 * @TODO move HTML output to template parts instead?
	 * @TODO escape input here?
	 **/
	protected function create_output() {
		if ( ! empty( $this->input ) ) {
			$this->output_tag_open();
			$this->output .= $this->input;
			$this->output_tag_close();
		} else {
			die( $debug );
		}
	}

	/**
	 * Prepare pattern for rendering or embedding
	 *
	 * @param $context
	 * @return void
	 */
	protected function prepare( $context ) {
		$this->set_basename();
		$this->set_context_and_base_class( $context );
		if ( ! empty( $this->modifiers ) ) {
			$this->process_modifiers();
		}
		return $this;
	}

	/**
	 * Prints escaped pattern output. Make sure to escape anywhere else.
	 *
	 * @since 0.1.0
	 * @access public
	 **/
	public function publish( $context = '' ) {
		$this->prepare( $context )->create_output();
		echo $this->output . PHP_EOL;
	}

	/**
	 * Prints untagged contents.
	 *
	 * @since 0.1.0
	 * @access public
	 * @param string $context
	 * @param int $limit
	 **/
	public function publish_stripped( $context = '', $limit = 0 ) {
		$this->prepare( $context )->create_output();
		$out = strip_tags( strip_shortcodes( $this->output ), '<div><p><em>' ) . PHP_EOL;
		if ( is_int( $limit ) && $limit > 0 ) {
			echo wp_trim_words( $out, $limit, __( '...','exchange' ) );
		} else {
			echo $out;
		}
	}

	/**
	 * Returns escaped pattern output for use in parent object.
	 *
	 * @since 0.1.0
	 * @access public
	 * @return string $output HTML output consisting of tags and content.
	 **/
	public function embed( $context = '' ) {
		$this->prepare( $context )->create_output();
		return $this->output;
	}
}
