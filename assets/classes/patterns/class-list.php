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
	 * Constructor for Base class objects.
	 *
	 * @since 0.1.0
	 * @access public
	 *
	 * @param mixed  $input Pattern content as defined in ACF input values.
	 * @param string $context String referring to pattern.
	 * @param array  $modifiers Optional. Additional modifiers that influence look and functionality.
	 **/
	public function __construct( $input, $context = '', $modifiers = array() ) {
		Parent::__construct( $input, $context, $modifiers );

		$this->output_tag_open( 'ul' );
		$this->output .= $this->build_list( $input['list_items'] ) . PHP_EOL;
		$this->output_tag_close( 'ul' );
	}

	/**
	 * Build list.
	 *
	 * This function adds non-empty list items to the list_items property and then adds to HTML output.
	 *
	 * @param array $list_items Array of list items.
	 */
	protected function build_list( $list_items ) {
		if ( is_array( $list_items ) && count( $list_items ) ) {
			foreach ( $list_item as $li ) {
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
