<?php
/*
 * Section Class
 * Author: Willem Prins | SOMTIJDS
 * Project Tandem
 * Date created: 07/03/2016
 */

class Section extends BasePattern {
  public $section_header;
  public $background_colour;
  public $story_elements;
  public $base = 'section';

  function __construct($input,$modifiers = array(),$parent = '') {
    Parent::__construct($input,$modifiers,$parent);

    // check for background colour modifier and add to classes
    if ( isset( $input['background_colour'] ) ) {
      $this->set_modifier( 'colour',$input['background_colour'] );
    }

    // open element with stringified classes
    print_r($this->classes);
    $this->output = '<section class="'.$this->stringify_classes().'">';

    // check for section header
    if ( isset( $input['section_header'] ) ) {
      $this->section_header = $input['section_header'];
      $this->output .= '<header class="'.$this->base.'__header">'.$this->section_header.'</header>';
    }

    // check for story elements
    if ( isset( $input['story_elements'] ) ) {
      $this->story_elements = $input['story_elements'];
      if ( count( $this->story_elements) > 0 ) {
        foreach( $this->story_elements as $element ) {
          $this->output .= "ELEMENT";
        }
      }
    }

  // end construction
  }

}
