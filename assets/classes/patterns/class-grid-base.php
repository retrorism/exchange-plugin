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
	 * Overwrite initial output value for Grid blocks
	 *
	 * @since 0.1.0
	 * @access protected
	 *
	 * @throws Exception when there's no valid input array.
	 **/
	 protected function create_output() {

		// If a grid is created inside a story, make this into an aside class.
		if ( is_single() ) {
			$el = 'aside';
		}
		$this->set_grid_items();

		 // Create grid with posts embedded.
		if ( $this->has_grid_items ) {
			$this->set_attribute('data','children', count( $this->grid_items ) );
			$this->output_tag_open( $el );
			foreach ( $this->grid_items as $item ) {
				$this->output .= $item->embed();
			}
			$this->output_tag_close( $el );
		}
	}

	/**
	 * Set grid items.
	 *
	 * Sets input array to grid_items property.
	 */
	protected function set_grid_items() {
		// Test input for array with posts.
		if ( is_array( $this->input ) &&  count( $this->input ) > 0 ) {
		   // Retrieve all items for this block.
			// Reset grid items array.
			$this->grid_items = array();
			foreach ( $this->input as $item ) {
				if ( is_array( $item ) ) {
					$this->add_grid_item( $this->create_grid_item_from_layout( $item ) );
				} elseif ( is_object( $item ) ) {
					$this->add_grid_item( $this::create_grid_item_from_post( $item, $this->element ) );
				}
			}
			if ( count( $this->grid_items ) > 0 ) {
				$this->has_grid_items = true;
			}
		} else {
			throw new Exception( __( 'This is not valid grid content', EXCHANGE_PLUGIN ) );
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
	 * Create grid item from post
	 *
	 * Creates grid item with post_item data, add modifiers when necessary.
	 *
	 * @param integer $item Post object to be represented by grid item.
	 */
	public static function create_grid_item_from_post( $item, $context ) {
		$exchange = BaseController::exchange_factory( $item, 'griditem' );
		$item_mods = self::add_grid_modifiers( $exchange );
		$grid_item = new GridItem( $exchange, $context, $item_mods );
		return $grid_item;
	}

	private function get_grid_width_num( $grid_width ) {
		$num = array(
			'grid_full' => 12,
			'grid_half' => 6,
			'grid_third' => 4,
			'grid_two_third' => 8,
		);
		if ( empty( $grid_width ) || ! array_key_exists( $grid_width, $num ) ) {
			return false;
		} else {
			return $num[$grid_width];
		}
	}

	/**
	 * Create grid item from ACF Layout
	 *
	 * Creates grid item with post_item data, add modifiers when necessary.
	 *
	 * @param integer $item Post object to be represented by grid item.
	 */
	protected function create_grid_item_from_layout( $item ) {
		if ( ! isset( $item[ 'acf_fc_layout' ] ) ) {
			return;
		}
		$item_mods = array();
		switch ( $item[ 'acf_fc_layout' ] ) {
			case 'grid_exchange_story':
			case 'grid_exchange_collaboration':
			case 'grid_exchange_page':
				if ( ! is_int( $item['grid_exchange_object'] ) ) {
					return;
				}
				$object = BaseController::exchange_factory( $item['grid_exchange_object'], 'griditem' );
				$item_mods = self::add_grid_modifiers( $object );
				break;
			case 'grid_paragraph' :
			case 'grid_pull_quote' :
			case 'grid_image' :
				$pattern_type = str_replace( 'grid_','', $item['acf_fc_layout'] );
				$object = BasePattern::pattern_factory( $item, $pattern_type, 'griditem__pattern', true );
				if ( $object->output === '' ) {
					return;
				}
				$item_mods['type'] = 'pattern';
				$item_mods['pattern_type'] = $pattern_type;
				break;
			default:
				return;
				break;
		}
		if ( ! empty( $item['grid_width'] ) ) {
			$item_mods['grid_width'] = $item['grid_width'];
			$num = $this->get_grid_width_num( $item['grid_width'] );
			if ( isset( $num ) & is_int( $num ) ) {
				$item_mods['grid_width_num'] = $num;
			}
		}

		$grid_item = new GridItem( $object, $this->element, $item_mods );
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
		if ( ! empty( $exchange->has_cta ) && $exchange->has_cta !== 'no' ) {
			$modifiers['type'] = 'cta';
		} else {
			$modifiers['type'] = $exchange->type;
		}
		$modifiers['data']['date'] = $exchange->date;
		if ( $exchange->has_tags ) {
			// Empty array to store 'all-purpose-tags'.
			$tag_ids = array();
			if ( ! array_key_exists( 'data', $modifiers ) ) {
				$modifiers['data'] = array();
			}
			foreach ( $exchange->ordered_tag_list as $term ) {
				switch ( $term->taxonomy ) {
					case 'language':
					case 'category':
					case 'post_tag':
						$modifiers['data'][ $term->taxonomy ] = $term->term_id;
						break;
					case 'topic':
					case 'tandem':
					case 'discipline':
					case 'output':
					case 'location':
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
