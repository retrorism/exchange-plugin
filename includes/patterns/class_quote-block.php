<?php
/*
 * BlockQuote Class
 * Author: Willem Prins | SOMTIJDS
 * Project Tandem
 * Date created: 07/03/2016
 */

class BlockQuote extends BasePattern {
  public $text;
  public $source = array();

  // initiate pattern
  function __construct( $input, $parent = '', $modifiers = array() ) {
    Parent::__construct( $input, $parent, $modifiers );

    if ( !empty( $input['bquote_source_text'] ) ) {
      $this->text = $input['bquote_text'];
    }

    if ( !empty( $input['bquote_source_individual'] ) ) {
      $this->source['source_name'] = $input['bquote_source_individual'];
    }

    if ( !empty( $input['bquote_source_info'] ) ) {
      $this->source['source_info'] = $input['bquote_source_info'];
    }

    if ( !empty( $input['bquote_text'] ) ) {
      $this->output = '<blockquote class="' . $this->stringify_classes(). '">' . PHP_EOL;
      $this->output .= '<p>' . $input['bquote_text'] . '</p>';
    }

    // add quote source name as caption
    if ( !empty( $this->source ) ) {
      $caption = new Caption( $this->source, $this->base );
      $this->output .= $caption->embed();
    }

    // close element
    $this->output .= '</blockquote>' . $this->end_pattern_tag_comment();

  }

}
