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
	 * Constructor for Section Pattern class objects.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed  $input Pattern content as defined in ACF input values.
	 * @param string $context Optional. String referring to pattern.
	 * @param array  $modifiers Optional. Additional modifiers that influence look and functionality.
	 **/
	public function __construct( $input, $context = '', $modifiers = array() ) {
		Parent::__construct( $input, $context, $modifiers );

		$this->set_image_properties( $input, $context, $modifiers );

		$this->output_tag_open( 'figure' );
		$this->output .= $this->build_image_element();
		// Add caption if available.
		if ( ! empty( $input['caption'] ) ) {
			$this->set_image_caption( $input, $modifiers );

			if ( is_object( $this->caption ) ) {
				$this->output .= $this->build_image_caption();
			}
		}
		// Close element.
		$this->output_tag_close( 'figure' );

	}

	/**
	 * Set image properties by using input and modifiers.
	 *
	 * @since 0.1.0
	 *
	 * @param array $input ACF input, consisting of image araray.
	 * @param array $modifiers List of modifiers received from sibling classes.
	 **/
	protected function set_image_properties( $input, $context, $modifiers ) {
		$h = $input['height'];
		$w = $input['width'];
		// Get orientation and validate with actual height and width.
		if ( is_int( $h ) && is_int( $w ) ) {
			$this->set_image_orientation( $h, $w, $modifiers );
			$this->set_image_quality( $h, $w );
		};
		// Default base size is story-portrait or story-landscape, switch to different sizes depending on context.
		$image_size = 'story-' . $this->orientation;

		if ( key_exists( 'context', $modifiers ) ) {
			if ( 'header' === $context ) {
				$image_size = 'header-image';
			} elseif ( 'grid' === $context ) {
				$image_size = 'post_thumbnail';
			}
		}

		// Set src_set from attachment_id.
		if ( ! empty( $input['ID'] ) ) {
			$this->src_set = wp_get_attachment_image_srcset( $input['ID'], $image_size );
		}

		// Set src just in case.
		if ( ! empty( $input['sizes'][ $image_size ] ) ) {
			$this->src = $input['sizes'][ $image_size ];
		}

		// Set description to be used as alt and alternative for title.
		if ( ! empty( $input['description'] ) ) {
			$this->description = $input['description'];
		}

		// Add description to be used as title.
		if ( ! empty( $input['title'] ) ) {
			$this->title = $input['title'];
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
	 *
	 * @param array $input ACF input.
	 * @param array $modifiers List of modifiers received from sibling classes.
	 **/
	protected function set_image_caption( $input, $modifiers ) {

		// Get caption position from modifiers paramater.
		$mods = array();
		if ( ! empty( $modifiers['caption_position'] ) ) {
			$mods['position'] = $modifiers['caption_position'];
		}

		$this->caption = new Caption( $input['caption'], $this->base, $mods );
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
	 * @param array   $modifiers List of modifiers may contain info on orientation.
	 *
	 * @TODO resolve difference between user input and actual size.
	 **/
	protected function set_image_orientation( $h, $w, $modifiers ) {
		// Check for orientation modifier.
		if ( ! empty( $modifiers['orientation'] ) ) {
			if ( 'portrait' === $modifiers['orientation'] ) {
				$this->orientation = 'portrait';
			} else {
				$this->orientation = 'landscape';
			}
		}
		// Calculate it from image dimensions otherwise.
		if ( empty( $this->orientation ) ) {
			if ( $h > $w ) {
				$this->orientation = 'portrait';
			} else {
				$this->orientation = 'landscape';
			}
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
		}
	}
}
