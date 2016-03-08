<?php
/*
 * Section SubHeader Class
 * Author: Willem Prins | SOMTIJDS
 * Project Tandem
 * Date created: 07/03/2016
 */

class SubHeader extends BasePattern {

  // initiate pattern
  function __construct( $input, $parent = '', $modifiers = array() ) {
    Parent::__construct( $input, $parent, $modifiers );

    // open element with stringified classes
    $this->output = '<header class="' . $this->stringify_classes(). '">' . PHP_EOL;
    $this->output .= '<h3>' . $input . '</h3>' . PHP_EOL;
    $this->output .= '</header>' . $this->end_pattern_tag_comment();

  }

}
