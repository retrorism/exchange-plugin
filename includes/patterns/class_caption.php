<?php
/*
 * Caption Class
 * Author: Willem Prins | SOMTIJDS
 * Project Tandem
 * Date created: 07/03/2016
 */

class Caption extends BasePattern {

  // initiate pattern
  function __construct($input, $modifiers = array(), $parent = '') {
    Parent::__construct($input, $modifiers, $parent);

    // open element with stringified classes for image caption
    if ( $this->parent == 'image' ) {
      $this->output = '<figcaption class="' . $this->stringify_classes() . '">'. PHP_EOL;
      $this->output .= $input;
      $this->output .= '</figcaption>' . $this->end_pattern_tag_comment();
    }

    // open element with stringified classes for quote caption
    elseif ( ( $this->parent == 'blockquote' || 'pullquote' ) && !empty( $input ) ) {
      $this->output = '<footer class="' . $this->stringify_classes() . '">'. PHP_EOL;
      if ( isset( $input['source_name'] ) || isset( $input['source_info'] ) )  {
        // add citation wrapper
        $this->output .= '<cite>';
        // add name if available
        if ( !empty( $input['source_name'] ) ) {
          $this->output .= '<div class="' . $this->parent . '__source-name' .'">' . $input['source_name'] . '</div>' . PHP_EOL;
        }
        // add info if available
        if ( !empty( $input['source_info'] ) ) {
          $info_cleaned = strip_tags(apply_filters( 'the_content',$input['source_info'] ),'<a>' );
          $this->output .= '<p class="' . $this->parent . '__source-info">' . $info_cleaned . '</p>' . PHP_EOL;
        }
        $this->output .= '</cite>';
      }
      $this->output .= '</footer>' . $this->end_pattern_tag_comment();
    }

  // end construct
  }

}
