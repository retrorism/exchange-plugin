<?php
/*
 * Paragraph Class
 * Author: Willem Prins | SOMTIJDS
 * Project Tandem
 * Date created: 07/03/2016
 */

class Image extends BasePattern {
  /* public $classes = array();
  public $output;
  public $parent;
  public $base = ''; */
  public $orientation;
  public $is_hq;
  public $title;
  public $description;
  public $caption;
  public $src;
  public $src_set;

  // initiate pattern
  function __construct( $input, $parent = '', $modifiers = array() ) {
    Parent::__construct( $input, $parent, $modifiers );

    // check for portrait modifier
    if ( $modifiers['orientation'] === 'portrait' ) {
      $this->orientation = 'portrait';
    }
    else {
      $this->orientation = 'landscape';
    }

    $image_size = 'story-'.$this->orientation;

    // get src_set from attachment_id
    if ( isset ( $input['ID'] ) ) {
      $this->src_set = wp_get_attachment_image_srcset( $input['ID'], $image_size ) ;
    }

    // get src just in case
    if ( !empty( $input['sizes'][$image_size] ) ) {
      $this->src = $input['sizes'][$image_size];
    }

    // get orientation
    if ( isset( $input['height'] ) && isset( $input['width'] ) ) {
      $this->get_proportions($input['height'],$input['width']);
    }

    // add description to be used as alt and alternative for title
    if ( !empty( $input['description'] ) ) {
      $this->description = $input['description'];
    }

    // add description to be used as title
    if ( !empty( $input['title'] ) ) {
      $this->title = $input['title'];
    }


    // open element with stringified classes
    $this->output = '<figure class="' . $this->stringify_classes() . '">'. PHP_EOL;

    // add src to output
    $this->output .= '<img src="' . $this->src . '"';

    //add srcset to output
    if ( $this->src_set ) {
      $this->output .= ' srcset="' . esc_attr( $this->src_set ) . '"';
    }

    // add title to output
    if ( $this->title ) {
      $this->output .= ' title="' . esc_attr( $this->title ) . '"';
    }

    // add alt to output
    if ( $this->description ) {
      $this->output .= ' alt="' . esc_attr( $this->description ) . '"';
    }
    elseif ( $this->title ) {
      $this->output .= ' alt="' . esc_attr( $this->title ) . '"';
    }

    // close image tag
    $this->output .= '>' . PHP_EOL;

    // add caption
    if ( !empty( $input['caption'] ) ) {

      // get caption position
      $mods = array();
      if ( !empty( $modifiers['caption_position'] ) ) {
        $mods['position'] = $modifiers['caption_position'];
      }

      $this->caption = new Caption( $input['caption'], $this->base, $mods );
      $this->output .= $this->caption->embed();
    }

     // close element
     $this->output .= '</figure>' . $this->end_pattern_tag_comment();

  // end construct
  }

  public function get_proportions($h,$w) {
    $sum = $h * $w;
    if ( $sum >= $GLOBALS['TANDEM_CONFIG']['IMAGES']['hq-norm'] ) {
      $this->is_hq = true;
    }
    if ( $h > $w ) {
      $this->orientation = 'portrait';
    }
    else {
      $this->orientation = 'landscape';
    }
  }

}
