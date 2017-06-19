
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
	 * Attributes check
	 *
	 * @since 0.1.0
	 * @access public
	 * @var bool $has_attributes Whether there are special attributes for this pattern;
	 **/
	protected $has_attributes = false;

	/**
	 * List of data-attributes for pattern container element.
	 *
	 * @since 0.1.0
	 * @access protected
	 * @var array $data_attributes Attributes-list.
	 **/
	protected $data_attributes = array();

	/**
	 * List of link-attributes for pattern container element.
	 *
	 * @since 0.1.0
	 * @access protected
	 * @var array $link_attributes Link attributes-list.
	 **/
	protected $link_attributes = array();

	/**
	 * List of link-attributes for pattern container element.
	 *
	 * @since 0.1.0
	 * @access protected
	 * @var array $misc_attributes Link attributes-list.
	 **/
	protected $misc_attributes = array();

	/**
	 * List of aria-attributes for pattern container element.
	 *
	 * @since 0.1.0
	 * @access protected
	 * @var array $aria_attributes Aria attributes-list.
	 **/
	protected $aria_attributes = array();

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
	public $input;

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
	 **/
	public function __construct( $input, $context = '', $modifiers = array() ) {

		/* Check if the context (for example the patterns parent)
		 * provided at instantiation is not empty, then assign it.
		 */
		$this->context = ! empty( $context ) ? $context : '';

		// Check if the modifiers provided at instantiation are not empty.
		if ( ! empty( $modifiers ) ) {
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
		// Add base class;
		$this->classes['element'] = $this->element;
		if ( ! array_key_exists('base__element', $this->classes )
			|| empty( $this->classes['base__element'] ) ) {
			if ( ! empty( $this->context ) ) {
				$this->classes['base__element'] = $this->context . '__' . $this->element;
			}
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
	 * Stringify attributes for use in HTML.
	 *
	 * @since 0.1.0
	 * @access protected
	 *
	 * @TODO move HTML output to template parts instead?
	 **/
	protected function stringify_attributes() {
		$attribute_list = array();
		$allowed_types = array( 'data','aria','link','misc' );
		foreach ( $allowed_types as $type ) {
			$prop = $type . '_attributes';
			$prefix = '';
			if ( ! empty( $this->$prop ) ) {
				$list = array();
				if ( 'data' === $type ||  'aria' === $type ) {
					$prefix = $type . '-';
				}
				foreach ( $this->$prop as $key => $val ) {
					$attribute = $prefix . $key;
					if ( $val !== true ) {
						$attribute .= '="' . esc_attr( $val ) . '"';
					}
					$list[] = $attribute;
				}
				if ( count( $list ) > 0 ) {
					$string = implode( ' ', $list );
					$attribute_list[] = $string;
				}
			}
		}
		if ( count( $attribute_list ) > 0 ) {
			$attribute_string = implode( ' ', $attribute_list );
			return $attribute_string;
		}
	}

	/**
	 * Prepare attributes for use in HTML.
	 *
	 * @since 0.1.0
	 * @access protected
	 *
	 * @param array $modifiers Modifiers array passed in Constructor.
	 * @TODO Make this more DRY.
	 **/
	protected function process_modifiers() {
		foreach ( $this->modifiers as $key => $val ) {
			switch ( $key ) {
				case 'data':
				case 'aria':
				case 'link':
				case 'misc':
					$atts = $this->modifiers[ $key ];
					if ( is_array( $atts ) && ! empty( $atts ) ) {
						foreach ( $atts as $k => $v ) {
							if ( ! empty( $v ) ) {
								$this->set_attribute( $key, $k, $v );
								$this->has_attributes = true;
							}
						}
					}
					break;
				case 'classes' :
					$classes = $this->modifiers[ 'classes' ];
					if ( ! empty( $classes ) && is_array( $classes ) ) {
						$this->classes = array_merge( $this->classes, $classes );
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
	protected function set_attribute( $type, $key, $val ) {
		$allowed_types = array( 'data','aria','link','misc' );
		if ( ! in_array( $type, $allowed_types, true ) ) {
			return;
		}
		$prop = $type . '_attributes';
		if (  ! empty( $val ) && is_array( $this->$prop ) ) {
			// Apparently, it's not possible to set keys directly to an array that is an object property.
			$arr = $this->$prop;
			$arr[$key] = $val;
			$this->$prop = $arr;
			$this->has_attributes = true;
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
			$inputol = '';
		} else {
			$inputol = PHP_EOL;
		}
		$this->output .= '<' . $tag;
		if ( count( $this->classes ) > 0 ) {
			$this->output .= ' ' . $this->stringify_classes();
		}
		if ( $this->has_attributes ) {
			$this->output .= ' ' . $this->stringify_attributes();
		}

		$this->output .= '>' . $inputol;
	}

	public static function build_edge_svg( $pos, $colour ) {
		if ( empty( $colour ) || false === strpos( $colour, '#' ) ) {
			return;
		}
		switch ( $pos ) {
			case 'top' :
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" class="section__edge--top" viewBox="0 0 840 20" preserveAspectRatio="none"><path d="M444 12l7 1 5-2h3l8-1 8 1h7l5-1h9l4 3 6-1 4 1h9l4-3 3 2 1-2 4 1h7l6 1v-2h1l3 2v-2l3 2 1-2 2 1h12l3 1 4-2 2 3h9l8-2 9 1h6l5-1h3l9 1h10l3-1 4 1 8-1h1l2 2 4-3 5 3 7-3h8l4 3 4-1 3 1 4-3 1 1h4l2-1 6 3 4-3 1 1h2l3 1h1l2-2 7 3 1-3 7 2 8-2v3-3l7 3 4-3v2l1-2 9 3 1-1 7-2 9 2v-1l5 1h5l8-2 8 2v1l7-1 3-2h7v1l4 1 9-1 4-1 6 10h6H0V10l13 2 9 1 5-2v1h4l6-2 9 3v-1h8l8 1 8-1 7 1 9-3 3 1h9l8 2v-1l9 1 4-3 9 1v-1l7 1 1 1h6l7 1 7-1v1h1l2-2h7l1 2h16l8-1 9-2 7 3h5l4-2 8-1 9 3 8-3 5 3 9-2h6l7 2 6-3h5l9 3 1-3h3l4 1 5 1h8l5-1 5 1 2-2 7 1 6 2 5-1 4-1h3l1 1 8-2 7 1h2l2-1h5v1l1-1v2l2-1 6 1 1-2 3 1h7l9 2 4-2h9l4-1 9 3 4-3h7z" fill="' . $colour . '"/></svg>';
				break;
			case 'bottom' :
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" class="section__edge--bottom" viewBox="0 0 840 20" preserveAspectRatio="none"><path d="M451 7l5 2h3l8 1 8-1h7l5 1h9l4-3 6 1 4-1h9l4 3 3-2 1 2 4-1h7l6-1v2h1l3-2v2l3-2 1 2 2-1h12l3-1 4 2 2-3h9l8 2 9-1h6l5 1h3l9-1h10l3 1 4-1 8 1h1l2-2 4 3 5-3 7 3h8l4-3 4 1 3-1 4 3 1-1h4l2 1 6-3 4 3 1-1h2l3-1h1l2 2 7-3 1 3 7-2 8 2V7v3l7-3 4 3V8l1 2 9-3 1 1 7 2 9-2v1l5-1h5l8 2 8-2V7l7 1 3 2h7V9l4-1 9 1 4 1 6-10h6H0v10l13-2 9-1 5 2V8h4l6 2 9-3v1h8l8-1 8 1 7-1 9 3 3-1h9l8-2v1l9-1 4 3 9-1v1l7-1 1-1h6l7-1 7 1V7h1l2 2h7l1-2h16l8 1 9 2 7-3h5l4 2 8 1 9-3 8 3 5-3 9 2h6l7-2 6 3h5l9-3 1 3h3l4-1 5-1h8l5 1 5-1 2 2 7-1 6-2 5 1 4 1h3l1-1 8 2 7-1h2l2 1h5V9l1 1V8l2 1 6-1 1 2 3-1h7l9-2 4 2h9l4 1 9-3 4 3h7l7-2z" fill="' . $colour . '"/></svg>';
				break;
			default:
				return;
		}
		return $svg;
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
	 * Return modifier from array;
	 *
	 * @return mixed
	 */
	public function get_modifier( $key ) {
		if ( ! array_key_exists( $key, $this->modifiers ) ) {
			return false;
		} else {
			return $this->modifiers[ $key ];
		}
	}

	/**
	 * 
	 *
	 *
     **/
	public function clear_output() {
		// If this pattern is reused, make sure its output = empty;
		$this->output = '';
		$this->context = '';
		$this->classes = array();
		$this->modifiers = array();
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
		// If this pattern is reused, make sure its output = empty;
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
		// If this pattern is reused, make sure its output = empty;
		$this->prepare( $context )->create_output();
		return $this->output;
	}

	public function embed_stripped( $context = '', $limit = 0 ) {
		$this->prepare( $context )->create_output();
		$this->output = wp_trim_words( $this->output, $limit, __( '...','exchange' ) );
		return $this->output;
	}

	/**
	 * Returns a pattern string
	 *
	 * @since 0.1.0
	 * @access public
	 *
	 * @var array $input ACF field input
	 * @var string $type ACF layout type (pattern class name)
	 * @var string $context The pattern's container.
	 * @return string $output HTML output consisting of tags and content.
	 **/
	public static function pattern_factory( $input, $type, $context = '', $return_as_object = false ) {
		if ( empty( $input ) ) {
			return;
		}
		switch ( $type ) {
			case 'image':
				$image_mods = array();
				$focus_points = exchange_get_focus_points( $input['image'] );
				$image_mods['data'] = array( 'img_id' => $input['image']['id'] );
				if ( ! empty( $focus_points ) ) {
					$image_mods['data'] = array_merge( $image_mods['data'], $focus_points );
					$image_mods['classes'] = array('focus');
				}
				if ( isset( $input['image_orientation'] ) && 'portrait' === $input['image_orientation']  ) {
					$image_mods['orientation'] = 'portrait';
				}
				if ( ! empty( $input['image'] ) ) {
					$pattern = new Image( $input['image'], $context, $image_mods );
				}
				break;

			case 'two_images':
				$pattern = new ImageDuo( $input, $context );
				break;

			case 'paragraph':
				$p_mods = array();
				if ( isset( $input['add_translation'] ) && ! empty( $input['add_translation'] ) ) {
					$translations = $input['translations'];
					$p_mods['type'] = 'has_translations';
					$languages = array();
					foreach( $translations as $t ) {
						if ( ! empty ( $t['translation_text'] ) ) {
							if ( $t['translation_language'] instanceof WP_Term ) {
								$languages[] = $t['translation_language']->name;
							} elseif ( is_string( $t['translation_language'] ) ) {
								$languages[] = $t['translation_language'];
							}
						}
					}
					if ( ! empty( $languages ) ) {
						$p_mods['data']['languages'] = implode(',', $languages );
						$pattern = new TranslatedParagraph( $input, $context, $p_mods );
					}
					break;
				}
				if ( ! empty( $input['text'] ) ) {
					$pattern = new Paragraph( $input['text'], $context );
				}
				break;

			case 'block_quote':
				$pattern = new BlockQuote( $input, $context );
				break;
			case 'pull_quote':
				$pquote_mods = array();
				if ( ! empty( $input['pquote_colour'] ) ) {
					$pquote_mods['colour'] = $input['pquote_colour'];
				}
				$pattern = new PullQuote( $input, $context, $pquote_mods );
				break;

			case 'embedded_video':
				$pattern = new Video( $input, $context );
				break;

			case 'embed':
				$pattern = new Embed( $input, $context );
				break;
				
			case 'interview_conversation':
				if ( ! empty( $input['interview'] ) ) {
					$pattern = new InterviewConversation( $input['interview'], $context );
				}
				break;
			case 'interview_q_and_a':
				if ( ! empty( $input['interview'] ) ) {
					$pattern = new InterviewQA( $input['interview'], $context );
				}
				break;
			case 'subheader':
				if ( ! empty( $input['text'] ) ) {
					$pattern = new SubHeader( $input['text'], $context );
				}
				break;
			case 'section_header':
				$header_mods = array();
				$colour = $input['tape_colour'];
				$type = $input['type'];
				if ( ! empty( $colour ) ) {
					$header_mods['colour'] = $colour;
					$header_mods['data'] = array( 'tape_colour' => $colour );
				}
				// Check for decoration colour
				if ( current_theme_supports( 'decorated_section_headers' ) ) {
					$colour = $input[ 'decoration_colour' ];
					$decoration_position = $input[ 'decoration_position' ];
					if ( ! empty( $colour ) ) {
						$header_mods['colour'] = $colour;
						$header_mods['data'] = array( 'decoration_colour' => $colour );
					}
					if ( ! empty( $decoration_position ) ) {
						$header_mods['position'] = $decoration_position;
					}
				}
				if ( ! empty( $type ) ) {
					$header_mods['type'] = $input['type'];
				}
				if ( ! empty( $input['text'] ) ) {
					$pattern = new SectionHeader( $input['text'], $context, $header_mods );
				}
				break;
			case 'simple_map':
				if ( ! empty( $input ) ) {
					$pattern = new SimpleMap( $input, $context );
				}
				break;
			case 'emphasis_block':
				$block_mods = array();
				$type = $input['block_type'];
				$align = $input['block_alignment'];
				$block_elements = $input[ $type . '_block_elements' ];
				if (  empty( $type ) || ! count( $block_elements ) ) {
					break;
				}
				switch ( $align ) {
					case 'left':
					case 'right':
						$block_mods['classes'] = array( 'floated' );
					case 'full':
						$block_mods['align'] = $align;
					default:
						break;
				}
				$block_mods['type'] = $type;
				$block_mods['colour'] = $input[ $type . '_colour' ];
				$block_mods['data'] = array( 'element_count' => count( $block_elements ) );
				$pattern = new EmphasisBlock( $block_elements, $context, $block_mods );
				break;
		case 'uploaded_files':
			$document_mods = array(
				'type' => 'post-it',
				'colour' => exchange_slug_to_hex('blue-1-web'),
			);
			if ( 'collaboration' === $context ) {
				$document_mods['colour'] = exchange_slug_to_hex('blue-2-web');
			}
			$pattern = new DocumentBlock( $input, $context, $document_mods );
			break;
		default:
			// $output = '<div data-alert class="alert-box alert">';
			// $output .= '<strong>' . __( 'Error: This layout has not yet been defined', EXCHANGE_PLUGIN ) . '</strong>';
			// $output .= '</div>';
			// $pattern = new Paragraph( $output, $context );
			// break;
			return;
		}
		if ( ! isset( $pattern ) ) {
			return;
		}
		if ( $return_as_object ) {
			return $pattern;
		} else {
			return $pattern->embed();
		}
	}
}
