<?php
/*
 * Image Duo Class
 * Author: Willem Prins | SOMTIJDS
 * Project Tandem
 * Date created: 08/03/2016
 */

class ImageDuo extends BasePattern {
  public $gallery = array();

  // initiate pattern
  function __construct( $input, $parent = '', $modifiers = array() ) {
    Parent::__construct( $input, $parent, $modifiers );

    // check if there are two images and add them to gallery
    if ( count($input) == 2 ) {
      foreach( $input as $image ) {
        if ( !empty ( $image['filename'] ) ) {
          array_push($this->gallery,$image);
        }
      }

      // open element with stringified classes
      $this->output = '<section class="' . $this->stringify_classes() . '">'. PHP_EOL;

      $i = 0;
      while ( $i < 2 ) {
        $pos = 'left';
        if ( $i == 1 ) {
          $pos = 'right';
        }
        $mods['position'] = $pos;
        $gallery_item = new Image($this->gallery[$i], $this->base, $mods);
        $this->output .= $gallery_item->embed();
        $i++;
      }

      // close element
      $this->output .= '</section>' . $this->end_pattern_tag_comment();
    }
  }


}
