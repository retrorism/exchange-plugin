<?php
/**
 * Base Grid Class File
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 12/04/2016
 *
 * @package Exchange Plugin
 **/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
};

/**
 * Base Grid Class
 *
 * This class serves as the basis for all grid views, to be used for
 * Overview pages and Related post views.
 *
 * @since 0.1.0
 **/
abstract class BaseGrid extends BasePattern {

	/**
	 * Grid Items variable
	 *
	 * @var array $grid_items Items array containing all Grid objects
	 */
	protected $grid_items;

	/**
	 * Grid Items Check
	 *
	 * @var array $has_grid_items Items array containing all posts
	 */
	protected $has_grid_items = false;

	/**
	 * Constructor for Base Grid Class.
	 *
	 * @since 0.1.0
	 * @access public
	 * @param array  $input Collection of Grid Item objects
	 * @param string $context Optional. String referring to pattern.
	 * @param array  $modifiers Optional. Additional modifiers that influence look and functionality.
	 **/
	public function __construct( $input, $context = '', $modifiers = array() ) {
		Parent::__construct( $input, $context, $modifiers );

		if ( is_array( $input ) &&  count( $input ) > 0 ) {
			$this->set_grid_items( $input );
		} else {
			throw new Exception( __( 'This is not valid grid content', EXCHANGE_PLUGIN ) );
		}

	}

	/**
	 * Set grid items.
	 *
	 * If not empty, sets input array to grid_items property.
	 *
	 * @param array $input List of items passed from ACF or controller.
	 */
	protected function set_grid_items( $input ) {
		// Reset grid items array.
		$this->grid_items = array();
		foreach ( $input as $item ) {
			$this->add_grid_item( $this->create_grid_item( $item ) );
		}
		if ( count( $this->grid_items ) > 0 ) {
			$this->has_grid_items = true;
		}
	}

	/** Add grid items.
	 *
	 * If not empty, sets input array to grid_items property.
	 *
	 * @param object $item Item object to be added to the Grid.
	 */
	public function add_grid_item( $item ) {
		if ( is_object( $item ) && 'GridItem' === get_class( $item ) ) {
			$this->grid_items[] = $item;
		}
	}

	/**
	 * Remove grid item.
	 *
	 * If found, removes item with corresponding post_id from grid_items property.
	 *
	 * @param integer $post_id Post_id stored in grid-item.
	 * @TODO I'm guessing this should be a frontend JS function instead.
	 */
	protected function remove_grid_item( $post_id ) {
		$post_ids = array();
		$item_count = count( $this->grid_items );
		$found = false;
		if ( $this->has_grid_items && $item_count > 0 ) {
			$i = 0;
			while ( $i < $item_count && $found = false ) {
				if ( $this->grid_items[ $i ]->post_id === $post_id ) {
					$found = true;
					unset( $array[ $i ] );
				}
				$i++;
			}
		}
		return $found;
	}

	/**
	 * Create grid item.
	 *
	 * Creates grid item with post_item data, add modifiers when necessary.
	 *
	 * @param integer $item Post object to be represented by grid item.
	 */
	protected function create_grid_item( $item ) {
		$exchange = BaseController::exchange_factory( $item, 'grid' );
		$item_mods = self::add_grid_modifiers( $exchange );
		$grid_item = new GridItem( $exchange, $this->element, $item_mods );
		return $grid_item;
	}
	/**
	 * Add term modifiers to post before creating Pattern object.
	 *
	 * @access public
	 * @param WP Post object
	 * @param array modifiers Optional. For adding to existing modifier lists.
	 * @return array $modifiers Modifiers with / without updated term list.
	 */

	public static function add_grid_modifiers( $exchange, $modifiers = array() ) {
		if ( ! is_array( $modifiers ) ) {
			throw Exception( __( 'This is not a valid modifiers array' ) );
		}
		$modifiers['type'] = $exchange->type;
		$modifiers['data']['date'] = $exchange->date;
		if ( $exchange->has_tags ) {
			// Empty array to store 'all-purpose-tags'.
			$tag_ids = array();
			if ( ! array_key_exists( 'data', $modifiers ) ) {
				$modifiers['data'] = array();
			}
			foreach ( $exchange->tag_list as $term ) {
				switch ( $term->taxonomy ) {
					case 'language':
					case 'category':
					case 'location':
						$modifiers['data'][ $term->taxonomy ] = $term->term_id;
						break;
					case 'topic':
					case 'discipline':
					case 'output':
						$tag_ids[] = $term->term_id;
						break;
				}
			}
			if ( count( $tag_ids ) > 0 ) {
				$modifiers['data']['tags'] = implode(',', $tag_ids );
			}
		}
		return $modifiers;
	}
}
