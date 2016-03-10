<?php
/*
 * Section Class
 * Author: Willem Prins | SOMTIJDS
 * Project Tandem
 * Date created: 07/03/2016
 */

class Section extends BasePattern {
  public $section_header;
  public $story_elements;
  public $base = 'section';

  function __construct($input,$modifiers = array(),$parent = '') {
    Parent::__construct($input,$modifiers,$parent);

    // check for background colour modifier and add to classes
    if ( isset( $input['background_colour'] ) ) {
      $this->set_modifier_classes( 'colour',$input['background_colour'] );
    }

    // open element with stringified classes
    //print_r($this->classes);
    $this->output = '<section class="'.$this->stringify_classes().'">';

    // check for section header
    if ( isset( $input['section_header'] ) ) {
      $this->section_header = new SectionHeader($input['section_header'],$this->base);
      $this->output .= $this->section_header->embed();
    }

    // add story elements to output
    $this->get_story_elements($input);

    // close element
    $this->output .= '</section>' . $this->end_pattern_tag_comment();
  // end construction
  }

  public function get_story_elements($input) {
    // check for story elements
    if ( isset( $input['story_elements'] ) ) {
      $this->story_elements = $input['story_elements'];
      if ( count( $this->story_elements) > 0 ) {
        foreach( $this->story_elements as $e ) {

          // loop through elements
          switch ( $e['acf_fc_layout'] ) {

            case 'image':
              $iamge_mods = array();
              if ( $e['image_orientation'] === 'portrait' ) {
                $image_mods['orientation'] = 'portrait';
              }
              $image = new Image( $e['image'],$this->base, $image_mods );
              $this->output .= $image->embed();
              break;

            case 'two_images':
              $duo = new ImageDuo( $e['two_images'], $this->base );
              $this->output .= $duo->embed();
              break;

            case 'paragraph':
              $paragraph = new Paragraph( $e['text'], $this->base );
              $this->output .= $paragraph->embed();
              break;

            case 'block_quote':
              $blockquote = new BlockQuote( $e, $this->base );
              $this->output .= $blockquote->embed();
              break;

            case 'pull_quote':
              $pquote_mods = array();
              if ( isset ( $e['pquote_colour'] ) ) {
                  $pquote_mods['colour'] = $e['pquote_colour'];
              }
              $pullquote = new PullQuote( $e, $this->base, $pquote_mods );
              $this->output .= $pullquote->embed();
              break;

            case 'subheader':
              $subheader = new SubHeader( $e['text'], $this->base );
              $this->output .= $subheader->embed();
              break;


            default:
              $this->output .= ( 'something else' );
              break;
           }

         }
       }
     }
     else {
       echo "error: no input given";
     }
  }

}
