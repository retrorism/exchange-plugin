<?php
/**
 * Image Class
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
 * Image pattern class.
 *
 * This class serves to build image elements.
 *
 * @since 0.1.0
 **/
class Image extends BasePattern {

	/**
	 * Orientation variable only filled when image has h&w.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var string $orientation Image orientation.
	 **/
	public $orientation;

	/**
	 * Image quality according to standard set as global variable.
	 *
	 * @since 0.1.0
	 * @access private
	 * @var bool $is_hq Image quality according to standard.
	 **/
	private $is_hq;

	/**
	 *  Image title.
	 *
	 * @since 0.1.0
	 * @access private
	 * @var string $title Image title.
	 **/
	private $title;

	/**
	 * Image description.
	 *
	 * @since 0.1.0
	 * @access private
	 * @var string $description Image description.
	 **/
	private $description;

	/**
	 * Image Caption, if available.
	 *
	 * @since 0.1.0
	 * @access private
	 * @var object $caption Image orientation.
	 **/
	private $caption;

	/**
	 * Image src attribute.
	 *
	 * @since 0.1.0
	 * @access private
	 * @var string $orientation Image orientation.
	 **/
	private $src;

	/**
	 * Image src_set attribute.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var string $src_set HTML image src_set attribute.
	 **/
	private $src_set;

	/**
	 * Image RWD sizes attribute.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var string $rwd_sizes HTML image src_set attribute.
	 **/
	private $rwd_sizes;

	/**
	 * Overwrite initial output value for Subheaders.
	 *
	 * @since 0.1.0
	 * @access protected
	 **/
	 protected function create_output() {

		$this->set_image_properties();

		// Add wrapper for centering if this is a header image.
		if ( 'story__header' === $this->context ) {
			$res = $this->is_hq ? '' : 'lowres-';
			$this->output .= '<div class="story__header__' . $res . 'image-wrapper">';
		}

		$this->output_tag_open( 'figure' );

		$this->output .= $this->build_image_element();

		// Add caption if available.
		if ( ! empty( $this->input['caption'] )
			|| ! empty( $this->title ) ) {

			$this->set_image_caption();

			if ( is_object( $this->caption ) ) {
				$this->output .= $this->build_image_caption();
			}
		}
		// Close element.
		$this->output_tag_close( 'figure' );

		// Close wrapper.
		if ( 'story__header' === $this->context ) {
			$this->output .= '</div>';
		}
	}

	/**
	 * Get image sizes
	 *
	 * @link https://core.trac.wordpress.org/timeline?from=2014-06-05T06%3A26%3A26Z&precision=second
	 *
	 * @param string $size Image size
	 * @return array
	 */

	private function get_image_size_data( $size = 'thumbnail' ) {
		$default_image_sizes = array( 'thumbnail', 'medium', 'large' ); // Standard sizes
		if ( in_array( $size, $default_image_sizes ) ) {
			$result['width'] = intval( get_option( "{$size}_size_w" ) );
			$result['height'] = intval( get_option( "{$size}_size_h" ) );
			// If not set: crop false per default.
			$result[ $size ]['crop']   = false;
			if ( get_option( "{$size}_crop" ) ) {
				$result[ $size ]['crop'] = get_option( "{$size}_crop" );
			}
		} else {
			global $_wp_additional_image_sizes;
			if ( in_array( $size, array_keys( $_wp_additional_image_sizes ) ) ) {
				$result = $_wp_additional_image_sizes[ $size ];
			}
		}
		return $result;
	}

	private function get_all_image_sizes() {
		global $_wp_additional_image_sizes;
		$default_sizes = array( 'thumbnail','medium','large' );
		$image_sizes = array();
		foreach( $default_sizes as $size ) {
			$size_data = $this->get_image_size_data( $size );
			$image_sizes[$size] = $size_data;
		}
		return array_merge( $image_sizes, $_wp_additional_image_sizes );
	}

	private function image_size_in_context() {
		$sizes = array(
			'story__header' => 'header-image',
			'griditem'      => 'story-landscape',
			'contactblock'  => 'thumbnail',
		);
		if ( ! array_key_exists( $this->context, $sizes ) ) {
			return false;
		} else {
			return $sizes[ $this->context ];
		}
	}

	private function check_for_src_set() {
		$src_sets = array();
		$input_sizes = $this->input['sizes'];
		$combined_sizes = $this->get_all_image_sizes();
		foreach( $combined_sizes as $size => $vals ) {
			if ( ! $vals['height'] === $input_sizes[ $size . '-height' ] ) {
				$src_sets[ $size ] = false;
				continue;
			}
			if ( ! $vals['width'] === $input_sizes[ $size . '-width' ] ) {
				$src_sets[ $size ] = false;
				continue;
			}
			$src_sets[ $size ] = array(
				$input_sizes[ $size ],
				$input_sizes[ $size . '-width'] . 'w',
			);
		}
		return $src_sets;
	}

	protected function get_appropriate_image_size() {
		if ( empty( $this->context ) ) {
			return;
		}
		$size = $this->image_size_in_context();
		$src_sets = $this->check_for_src_set();
		if ( $size && is_array( $src_sets[ $size ] ) ) {
			return $size;
		}
	}

	/**
	 * Get source-set part for this size
	 *	 *
	 * @param string $size Image size.
	 * @return void | string
	 */
	private function get_src_set_part( $size ) {
		$src_sets = $this->check_for_src_set();
		if ( ! is_array( $src_sets[ $size ] ) ) {
			return;
		}
		return implode( ' ', $src_sets[ $size ] );
	}

	/**
	 * Set source set
	 *
	 * @return void
	 */
	 private function set_src_set_and_sizes() {
		if ( 'contactblock' === $this->context ) {
			$thumb = $this->get_src_set_part( 'thumbnail' );
		} else {
			if ( 'portrait' !== $this->orientation ) {
				$wide = $this->get_src_set_part( 'header-image' );
				$medium = $this->get_src_set_part( 'story-landscape' );
				$small = $this->get_src_set_part( 'story-landscape-small' );
			} else {
				$medium = $this->get_src_set_part( 'story-portrait' );
				$small = $this->get_src_set_part( 'story-portrait-small' );
			}
		}
		switch ( $this->context ) {
			case 'story__header':
				$order = array( $wide, $medium, $small );
				if ( $this->is_hq ) {
					$sizes = "100vw";
				} else {
					$sizes = "(max-width: 60em) 100vw, (min-width: 90em) 50vw, 75vw";
				}
				break;
			case 'griditem' :
				$order = array( $medium, $small );
				$sizes = "(max-width: 30em) 100vw, (min-width: 60em) 33vw, 50vw";
				break;
			case 'contactblock' :
				$order = array( $thumb );
				$sizes = "(max-width: 30em) 25vw, (min-width: 30em) 20vw";
				break;
			default :
				$order = array( $medium, $small );
				if ( 'portrait' !== $this->orientation ) {
					$sizes = "(max-width: 30em) 100vw, (min-width: 90em) 50vw, 75vw";
				} else {
					$sizes = "(max-width: 30em) 75vw, (max-width: 60em) 50vw, 33vw";
				}
				break;
		}
		$new_order = array();
		foreach( $order as $url_and_width ) {
			if ( ! empty( $url_and_width ) ) {
				$new_order[] = $url_and_width;
			}
		}
		if ( count( $new_order ) > 0 ) {
			$this->src_set = implode( ', ', $new_order );
		}
		if ( isset( $sizes ) ) {
			$this->rwd_sizes = $sizes;
		}
	 }

	/**
	 * Set image properties by using input and modifiers.
	 *
	 * @since 0.1.0
	 **/
	protected function set_image_properties() {
		if ( isset( $this->input['height'] ) ) {
			$h = $this->input['height'];
			$w = $this->input['width'];
			// Get orientation and validate with actual height and width.
			if ( is_int( $h ) && is_int( $w ) ) {
				$this->set_image_orientation( $h, $w );
				$this->set_image_quality( $h, $w );
			}
		}

		// Set src_set and RWD sizes based on context.
		$this->set_src_set_and_sizes();

		// Default base size is story-portrait or story-landscape, switch to different sizes depending on context.
		$image_size = $this->get_appropriate_image_size();

		// Set src just in case.
		if ( ! empty( $this->input['sizes'][ $image_size ] ) ) {
			$this->src = $this->input['sizes'][ $image_size ];
		} else {
			$this->src = $this->input['sizes'][ 'medium' ];
		}

		// Set description to be used as alt and alternative for title.
		if ( ! empty( $this->input['description'] ) ) {
			$this->description = $this->input['description'];
		}

		// Add description to be used as title.
		if ( ! empty( $this->input['title'] ) ) {
			$this->title = $this->input['title'];
		}

	}

	/**
	 * Set image properties by using input and modifiers.
	 *
	 * @since 0.1.0
	 *
	 * @return string $img HTML image element.
	 **/
	protected function build_image_element() {

		// Add src to output.
		$img = '<img src="' . $this->src . '"';

		// Add srcset to image element.
		if ( $this->src_set ) {
			$img .= ' srcset="' . esc_attr( $this->src_set ) . '"';
		}

		// Add RWD_sizes to image element.
		if ( $this->rwd_sizes ) {
			$img .= ' sizes="' . esc_attr( $this->rwd_sizes ) . '"';
		}

		// Add title to image element.
		if ( $this->title ) {
			$img .= ' title="' . esc_attr( $this->title ) . '"';
		}

		// Add alt to image element.
		if ( $this->description ) {
			$img .= ' alt="' . esc_attr( $this->description ) . '"';
		} elseif ( $this->title ) {
			$img .= ' alt="' . esc_attr( $this->title ) . '"';
		}

		// Close image element.
		$img .= '>' . PHP_EOL;

		return $img;
	}

	/**
	 * Set image caption object from input and modifiers.
	 *
	 * @since 0.1.0
	 **/
	protected function set_image_caption() {

		// Get caption position from modifiers paramater.
		$mods = array();
		if ( $this->modifiers['style'] === 'rounded' ) {
			return;
		}
		if ( ! empty( $this->modifiers['caption_position'] ) ) {
			$mods['position'] = $this->modifiers['caption_position'];
		}
		$caption = ! empty( $this->input['caption'] ) ?
			$this->input['caption'] :
			$this->title;

		$this->caption = new Caption( $caption, $this->element, $mods );
	}

	/**
	 * Set image properties by using input and modifiers.
	 *
	 * @since 0.1.0
	 *
	 * @return string $embed HTML string of caption to be added.
	 **/
	protected function build_image_caption() {
		$embed = $this->caption->embed();
		if ( ! empty( $embed ) ) {
			return $embed;
		}
	}

	/**
	 * Set image orientation from height and width, overriding ACF setting when necessary.
	 *
	 * @since 0.1.0
	 *
	 * @param integer $h Image height.
	 * @param integer $w Image width.
	 *
	 * @TODO resolve difference between user input and actual size.
	 **/
	protected function set_image_orientation( $h, $w ) {
		// Check for orientation modifier.
		if ( empty( $this->modifiers['orientation'] ) ) {
			$this->orientation = $h > $w ?
				'portrait' :
				'landscape';
		} else {
			$this->orientation = 'portrait' === $this->modifiers['orientation'] ?
				'portrait' :
				'landscape';
		}
	}

	/**
	 * Determine image quality by comparing it to standard.
	 *
	 * @since 0.1.0
	 *
	 * @global integer EXCHANGE_PLUGIN_CONFIG->IMAGES->hq-norm.
	 *
	 * @param array $h Image height.
	 * @param array $w Image width.
	 **/
	protected function set_image_quality( $h, $w ) {
		$sum = $h * $w;
		if ( is_int( $sum ) && $sum >= $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['IMAGES']['hq-norm'] ) {
			$this->is_hq = true;
			$this->classes[] = 'highres';
		} else {
			$this->classes[] = 'lowres';
		}
	}
}
