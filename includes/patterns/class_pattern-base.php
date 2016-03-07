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
  public $base = '';
  public $modifiers = array(
    'colour'     => '',
  );


  // initiate pattern
  // takes $modifiers array (parent, css modifiers) ;
  function __construct($input,$modifiers = array(),$parent = '') {

    // set initial output value
    if ( empty($input) ) {
      $this->output = "No input given";
    }

    // check if there are any modifiers
    if ( is_array( $modifiers ) ) {
      if ( count( $modifiers ) > 0 ) {
      // if section type is set...
        foreach( $modifiers as $key => $val) {
          // add type-slug to class list
          $this->set_modifier( $key, $val );
        }
      }
    }

    // check if parent is set
    if ( !empty( $parent ) ) {
      $this->classes[] = $parent.'__'.$this->base;
      $this->parent = $parent;
    }
    else {
      $this->classes[] = $this->base;
    }

  }

  public function set_modifier( $key,$val ) {
    if ( $key == 'colour' ) {
      $val = tandem_hex_to_slug( $val );
    }
    $this->classes[] = $this->base.'--'.$val;
  }

  public function stringify_classes() {
    $string = implode(' ',$this->classes);
    return $string;
  }

  public function publish() {
    echo $this->output;
  }
}
