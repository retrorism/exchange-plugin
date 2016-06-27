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
	 * @access private
	 * @var string $rwd_sizes HTML image src_set attribute.
	 **/
	private $rwd_sizes;

	/**
	 * Load image lazily?
	 *
	 * @since 0.1.0
	 * @access public
	 * @var bool $lazy Whether to load this image lazily. Default is true.
	 **/
	public $lazy = true;

	/**
	 * Float
	 *
	 * @since 0.1.0
	 * @access public
	 * @var bool $lazy Whether to load this image lazily. Default is true.
	 **/
	public $ratio;

	/**
	 * Overwrite initial output value for images.
	 *
	 * @since 0.1.0
	 * @access protected
	 **/
	 protected function create_output() {

		$this->set_image_properties();

		// Open wrapper.
		$this->wrapper( 'open' );

		// Open element.
		$this->output_tag_open( 'figure' );

		// Close anchor.
		$this->anchor( 'open' );

		// Add placeholder for images that need lazy-loading.
		if ( in_array( $this->context, array( 'griditem','contactblock' ), true ) ) {
			$this->lazy = false;
		}
		$this->output .= $this->build_image_placeholder();

		// Add caption if available.
		if ( ! empty( $this->input['caption'] )
			|| ! empty( $this->title ) ) {

			$this->set_image_caption();

			if ( is_object( $this->caption && $this->context !== 'collaboration_header' ) ) {
				$this->output .= $this->build_image_caption();
			}
		}

		// Close anchor.
		$this->anchor( 'close' );

		// Close element.
		$this->output_tag_close( 'figure' );

		// Close wrapper.
		$this->wrapper( 'close' );
	}

	/**
	 * Add wrapper elements for specific contexts
	 *
	 * @param string $location ( open or close )
	 * @return void
	 * @TODO add filter instead of hardcoding the Orbit references
	 */
	protected function wrapper( $location ) {
		// Empty array that contains two element values if context is met.
		$el = array();
		switch ( $this->context ) {
			case 'story__header':
				// Add wrapper for centering if this is a header image.
				$res = $this->is_hq ? '' : 'lowres-';
				$el['open'] = '<div class="story__header__' . $res . 'image-wrapper">';
				$el['close'] = '</div>';
				break;
			case 'collaboration__header':
				$el['open'] = '<div class="collaboration__header__image-wrapper" data-equalizer-watch>';
				$el['close'] = '</div>';
				break;
			case 'lightbox':
				$orbit = '';
				$orbit_is_active = '';
				// Add wrapper for gallery list, optionally add orbit-slide class
				if ( ! empty( $this->modifiers['data']['index'] ) && ( 1 == $this->modifiers['data']['index'] ) ) {
					$orbit_is_active = ' is-active';
				}
				$orbit_class = defined( 'EXCHANGE_THEME' ) ? 'orbit-slide' . $orbit_is_active : '';
				$el['open'] = '<li class="gallery__item '. $orbit_class . '" id="' . $this->modifiers['data']['img_id'] . '">';
				$el['close'] = '</li>';
				break;
			default:
				break;
		}
		if ( count( $el ) ) {
			$this->output .= $el[ $location ];
		}
	}

	protected function anchor( $location ) {
		// Empty array that contains two element values if context is met.
		$el = array();
		switch ( $this->context ) {
			case 'imageduo':
			case 'section':
				$el['open'] = '<a data-open="story__modal--gallery" data-img_id="' . $this->modifiers['data']['img_id'] . '">';
				$el['close'] = '</a>';
				break;
			default:
				break;
		}
		if ( count( $el ) ) {
			$this->output .= $el[ $location ];
		}
	}

	/**
	 * Get image size data for the given size.
	 *
	 * @link https://core.trac.wordpress.org/timeline?from=2014-06-05T06%3A26%3A26Z&precision=second
	 *
	 * @param string $size Image size
	 * @return array $result Width, height, and crop settings
	 */
	private function get_image_size_data( $size = 'thumbnail' ) {
		$default_image_sizes = array( 'thumbnail', 'medium', 'large' ); // Standard sizes
		if ( in_array( $size, $default_image_sizes ) ) {
			$result['width'] = intval( get_option( "{$size}_size_w" ) );
			$result['height'] = intval( get_option( "{$size}_size_h" ) );
			// If not set: crop false per default.
			$result[$size]['crop'] = empty( get_option( "{$size}_crop" ) ) ? false : get_option( "{$size}_crop" );
		} else {
			global $_wp_additional_image_sizes;
			if ( in_array( $size, array_keys( $_wp_additional_image_sizes ) ) ) {
				$result = $_wp_additional_image_sizes[ $size ];
			}
		}
		return $result;
	}

	/**
	 * Get all image size data.
	 *
	 * Merge array with default image size data with additional image sizes global for complete list.
	 *
	 * @return array $result Width, height, and crop settings for each available size.
	 */
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

	/**
	 * Prepare sourceset sizes
	 *
	 * See which crops are available for each size, return available crops with widths.
	 *
	 * @return array $src_sets List of available sizes with widths.
	 */
	private function check_for_src_set() {
		$src_sets = array();
		$src_sets['full'] = array( $this->input['url'], $this->input['width'] . 'w' );
		$input_sizes = $this->input['sizes'];
		$combined_sizes = $this->get_all_image_sizes();
		foreach( $combined_sizes as $size => $vals ) {
		// Check if the available crops are more or less the same height as the ideal height
			$diff_h = $input_sizes[ $size . '-height' ] / $vals['height'];
			if ( ( $diff_h * 100 ) > 110 || ( $diff_h * 100 ) < 90  ) {
				$src_sets[ $size ] = false;
				continue;
			}
			// Check if the available crops are more or less the same width as the ideal width.
			$diff_w = $input_sizes[ $size . '-width' ] / $vals['width'];
			if ( ( $diff_w  * 100 ) > 110 || ( $diff_w * 100 ) < 90  ) {
				$src_sets[ $size ] = false;
				continue;
			}
			// Check if the image ratio is too small to be rendered in landscape
			$src_sets[ $size ] = array(
				$input_sizes[ $size ],
				$input_sizes[ $size . '-width'] . 'w',
			);
		}
		return $src_sets;
	}

	/**
	 * Get source-set part for this size
	 *	 *
	 * @param string $size Image size.
	 * @return void | string
	 */
	private function get_src_set_part( $size, $src_sets ) {
		if ( empty( $src_sets[ $size ] ) ) {
			return;
		}
		return implode( ' ', $src_sets[ $size ] );
	}

	/**
	 * Set source set and sizes for the available crops
	 *
	 * @return void
	 */
	 private function set_src_set_and_sizes() {
		$src_sets = $this->check_for_src_set();
		$full = $this->get_src_set_part( 'full', $src_sets );
		if ( 'contactblock' === $this->context ) {
			$thumb = $this->get_src_set_part( 'thumbnail', $src_sets );
		} elseif ( 'portrait' !== $this->orientation ) {
			$wide = $this->get_src_set_part( 'header-image', $src_sets );
			$large = $this->get_src_set_part( 'large', $src_sets );
			$mlarge = $this->get_src_set_part( 'medium-large', $src_sets );
			$medium = $this->get_src_set_part( 'medium', $src_sets );
		} else {
			$large = $this->get_src_set_part( 'large-portrait', $src_sets );
			$mlarge = $this->get_src_set_part( 'medium-large-portrait', $src_sets );
			$medium = $this->get_src_set_part( 'medium-portrait', $src_sets );
		}
		if ( 'collaboration__header' === $this->context ) {
			$mlarge = $this->get_src_set_part( 'medium-large-square', $src_sets );
		}
		switch ( $this->context ) {
			case 'story__header':
				$order = array( $medium, $mlarge, $large, $wide );
				break;
			case 'contactblock' :
				$order = array( $thumb );
				break;
			case 'collaboration_header' :
				$order = array( $mlarge, $large );
				break;
			case 'griditem' :
			case 'lightbox' :
			case 'section' :
			default :
				// Add full size if the ratio is severe pano or severe portrait.
				if ( $this->ratio < 0.5 || $this->ratio > 2 ) {
					$order = array( $medium, $mlarge, $large, $full );
				} else {
					$order = array( $medium, $mlarge, $large );
				}
				$sizes = '(min-width: 1080px) 60vw, 100vw';
				break;
		}
		// Remove empty values in the src_set list with array_filter
		$order_cleaned = array_filter( $order );
		if ( ! empty( $order_cleaned ) ) {
			$this->src_set = implode( ', ', $order_cleaned );
		}
		// Add sizes property, if set.
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
				$this->set_image_orientation( $w, $h );
				$this->set_image_quality( $w, $h );
				$this->set_image_ratio( $w, $h );
			}
		}

		// Set src_set and RWD sizes based on context.
		$this->set_src_set_and_sizes();

		// Set src just in case, defaulting to medium when not availabe
		$this->src = $this->input['sizes']['medium'];

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
	protected function build_image_placeholder() {
		$lazy_style = '';
		$ratio_perc = round( ( $this->ratio * 100 ), 3 );

		if ( ! empty( $ratio_perc ) ) {
			$lazy_style = ' style="position: relative;height:0;padding-bottom:' . $ratio_perc . '%;"';
		}
		$placeholder = '<div class="image__placeholder"' . $lazy_style . '>';
		$placeholder .= $this->build_image_element();
		$placeholder .= '<img class="image__placeholder__thumb" src=' . $this->input['sizes']['placeholder'] . ' />';
		$placeholder .= '</div>';

		return $placeholder;
	}


	/**
	 * Set image properties by using input and modifiers.
	 *
	 * @since 0.1.0
	 *
	 * @return string $img HTML image element.
	 **/
	protected function build_image_element() {
		$lazy_class = ' lazyload lazypreload';
		if ( $this->lazy ) {
			$lazy_class = ' lazyload';
		}
		$img = '<img class="image--main' . $lazy_class . '"';

		// Add src to output.
		$img .= ' data-src="' . $this->src . '"';

		// Add ratio.
		if ( $this->ratio ) {
			$img .= ' data-ratio="' . esc_attr( $this->ratio ) . '"';
		}

		// Add srcset to image element.
		if ( $this->src_set ) {
			$img .= ' data-sizes="auto"';
			$img .= ' data-srcset="' . esc_attr( $this->src_set ) . '"';
		}

		// if ( $this->lazy ) {
		// 	$img .= ' srcset="data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="';
		// }
		// // Add RWD_sizes to image element.
		// if ( $this->rwd_sizes ) {
		// 	$img .= ' sizes="' . esc_attr( $this->rwd_sizes ) . '"';
		// }

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
		if ( ! empty( $this->modifiers['style'] ) && $this->modifiers['style'] === 'rounded' ) {
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
	 * @param int $w Image width.
	 * @param int $h Image height.
	 * @TODO resolve difference between user input and actual size.
	 **/
	protected function set_image_orientation( $w, $h ) {
		// Check for orientation modifier.
		if ( empty( $this->modifiers['orientation'] ) ) {
			$this->orientation = $h > $w ?
				'portrait' :
				'landscape';
		} else {
			// Default to landscape if 'portrait' is not explicitly set.
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
	 * @param int $w Image width.
	 * @param int $h Image height.
	 **/
	protected function set_image_quality( $w, $h ) {
		$ratio = $w / $h;
		$sum = $h * $w;
		if ( is_int( $sum ) && $sum >= $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['IMAGES']['hq-norm'] ) {
			$this->is_hq = true;
			$this->classes[] = 'highres';
		} else {
			$this->classes[] = 'lowres';
		}
	}

	/**
	 * Calculate image ratio
	 *
	 * @since 0.1.0
	 *
	 *
	 * @param int $w Image width.
	 * @param int $h Image height.
	 *
	 * @TODO allow for custom ratios inside stories
	 **/
	protected function set_image_ratio( $w, $h ) {
		$ratio = intval( $h ) / intval( $w );
		$rounded = round( $ratio, 3 );
		if ( ! empty( $rounded ) )  {
			$this->ratio = $rounded;
		}
		if ( ! empty( $this->context ) ) {
			switch ( $this->context ) {
				case 'contactblock':
				case 'collaboration__header':
					$this->ratio = 1;
					break;
				case 'griditem':
					$this->ratio = 0.6;
					break;
				case 'section':
				case 'imageduo':
					if ( empty( $rounded ) ) {
						break;
					}
					if ( $this->is_hq && 'landscape' === $this->orientation ) {
						$this->ratio = 0.6667;
					} else {
						$this->ratio = $rounded;
					}
					break;
				case 'story__header':
					if ( empty( $rounded ) ) {
						break;
					}
					if ( 'landscape' === $this->orientation ) {
						$this->ratio = $rounded;
					} else {
						$this->ratio = 0.75;
					}
				case 'lightbox':
				default:
					if ( ! empty( $rounded ) )  {
						$this->ratio = $rounded;
					}
					break;
			}
		}
	}
}
