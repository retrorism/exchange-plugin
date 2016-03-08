<?php
/*
 * PullQuote Class
 * Author: Willem Prins | SOMTIJDS
 * Project Tandem
 * Date created: 08/03/2016
 */

class PullQuote extends BasePattern {
  public $source = array();

  // initiate pattern
  function __construct( $input, $parent = '', $modifiers = array() ) {
    Parent::__construct( $input, $parent, $modifiers );

    if ( !empty( $input['pquote_source_text'] ) ) {
      $this->text = $input['pquote_text'];
    }

    if ( !empty( $input['pquote_source_individual'] ) ) {
      $this->source['source_name'] = $input['pquote_source_individual'];
    }

    if ( !empty( $input['pquote_source_info'] ) ) {
      $this->source['source_info'] = $input['pquote_source_info'];
    }

    if ( !empty( $input['pquote_text'] ) ) {
      $this->output = '<aside class="' . $this->stringify_classes(). '">' . PHP_EOL;
      $this->output .= $input['pquote_text'];

    }

    // add quote source name as caption
    if ( !empty( $this->source ) ) {
      $caption = new Caption( $this->source, $this->base );
      $this->output .= $caption->embed();
    }

    // close element
    $this->output .= '</aside>' . $this->end_pattern_tag_comment();

  }

}
