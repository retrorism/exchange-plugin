<?php
/**
 * Base List class
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 09/04/2016
 *
 * @package Exchange Plugin
 **/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
};

/**
 * List Class
 *
 *  Used to create lists for content / Tags.
 *
 * @since 0.1.0
 **/
class BaseList extends BasePattern {

	/**
	 * List Items array to be filled with non-empty List items from input (most likely: tags).
	 *
	 * @var array $list_items
	 */
	protected $list_items = array();

	/**
	 * Overwrite initial output value for List patterns.
	 *
	 * @since 0.1.0
	 * @access protected
	 **/
	 protected function create_output() {
		$this->output_tag_open();
		$this->output .='<ul>';
		$this->output .= $this->build_list() . PHP_EOL;
		$this->output .='<ul>';
		$this->output_tag_close();
	}

	/**
	 * Build list.
	 *
	 * This function adds non-empty list items to the list_items property and then adds to HTML output.
	 *
	 * @param array $list_items Array of list items.
	 */
	protected function build_list() {
		if ( is_array( $this->input['list_items'] ) && count( $this->input['list_items'] > 0 ) ) {
			foreach ( $this->input['list_items'] as $li ) {
				if ( ! empty( $li ) ) {
					$this->list_items[] = '<li>' . $li  . '</li>';
				}
			}
		}
		if ( count( $this->list_items ) > 0 ) {
			foreach ( $this->list_item as $li ) {
				$output .= $li . PHP_EOL;
			}
		}
	}
}
