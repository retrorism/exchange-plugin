<?php
/*
 * Pattern Base Class
 * Author: Willem Prins | SOMTIJDS
 * Project Tandem
 * Date created: 07/03/2016
 */

class BasePattern {

  public $classes = array();
  public $output;
  public $parent;
  public $base;

  // initiate pattern
  // takes $modifiers array (parent, css modifiers) ;
  function __construct( $input, $parent = '', $modifiers = array() ) {

    // set basename to class
    $this->base = strtolower( get_class( $this ) );

    // check if parent is set and add classes
    if ( !empty( $parent ) ) {
      $this->parent = $parent;
      // add generic story element class for all direct children of a section
      if ( $this->parent == 'section' ) {
        $this->classes[] = 'section__story-element';
      }
      // add parent classes
      $this->classes[] = $this->parent.'__'.$this->base;
    }
    else {
      // set generic class element
      $this->classes[] = $this->base;
    }

    // check if there are any modifiers and add modifier classes
    if ( is_array( $modifiers ) && count( $modifiers ) > 0 ) {
      // if modifiers are set...
      foreach( $modifiers as $key => $val) {
        // add type-slug to class list
        $this->set_modifier_classes( $key, $val );
      }
    }


    // set initial output value if case no other output variable is given by individual Patterns
    if ( !empty( $input ) ) {
      $this->output = '<div class="' . $this->stringify_classes().'">';
      $this->output .= $input;
      // close element
      $this->output .= '</div>' . $this->end_pattern_tag_comment();
    }

  }

  public function set_modifier_classes( $key,$val ) {
    $class = '';
    if ( $key == 'colour' ) {
      $val = tandem_hex_to_slug( $val );
    }
    if ( !empty($this->parent ) ) {
      $class .= $this->parent . '__';
    }
    $class .= $this->base.'--'.$val;
    $this->classes[] = $class;
  }

  public function stringify_classes() {
    $string = implode(' ',$this->classes);
    return esc_attr($string);
  }

  public function end_pattern_tag_comment() {
    return '<!-- END ' . strtoupper( $this->base ) . ' ELEMENT-->' . PHP_EOL;
  }

  public function publish() {
    echo $this->output;
  }

  public function embed() {
    return $this->output;
  }
}
