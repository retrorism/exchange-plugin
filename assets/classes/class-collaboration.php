<?php
/**
 * Collaboration Class
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 11/2/2016
 *
 * @package Exchange Plugin
 **/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
};

/**
 * Collaboration CPT Class
 *
 * This class serves as the foundation for Tandem collaborations and other
 * storytellers.
 *
 * @since 0.1.0
 **/
class Collaboration extends Exchange {

	/**
	 * Ordered array for use in grid / single display.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var array Ordered tag-list.
	 **/
	public $ordered_tag_list = array();

	/**
	 * The programme round this collaboration was a part of.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var integer $programme_round Programme round post ID, defined as parent_id.
	 */
	public $programme_round;

	/**
	 * The participants that formed this collaboration.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var array $participants List of 2-4 participant IDs
	 */
	public $participants = array();

	/**
	 * Participant check.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var boolean $has_participants Whether there are any connected participants. Defaults to false.
	 */
	public $has_participants = false;

	/**
	 * Geo locations stored in associative array where participant IDs are key, and values
	 * are the organisation's names, lat, and long.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var array $locations List that holds participants' location details.
	 */
	public $locations;

	/**
	 * Geo check.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var boolean $has_locations When there's two or more geolocations added for mapping. Defaults to false.
	 */
	public $has_locations = false;

	/**
	 * Stories list.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var array $stories For gathering all related stories
	 */
	public $stories = array();

	/**
	 * Story check.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var boolean $has_stories When there's one or more stories shared by this collaboration.
	 */
	public $has_stories = false;

	/**
	 * Collab description
	 *
	 * @since 0.1.0
	 * @access public
	 * @var string $description Text describing the collaboration's (final) plan / outcome.
	 */
	public $description;

	/**
	 * Collab website
	 *
	 * @since 0.1.0
	 * @access public
	 * @var string $website URL for the project website.
	 */
	public $website;

	/**
	 * Collab description check
	 *
	 * @since 0.1.0
	 * @access public
	 * @var bool $has_description See if description is available.
	 */
	public $description_length = 0;

	/**
	 * Map
	 *
	 * @since 0.1.0
	 * @access public
	 * @var array $map_data
	 */
	public $map_data;

	/**
	 * Update form link
	 *
	 * @since 0.1.0
	 * @access protected
	 * @var string $update_form_link
	 */
	 private $update_form_link;


	/**
	 * Constructor for collaboration objects.
	 *
	 * @since 0.1.0
	 * @access public
	 * @param object $post Collaboration post object.
	 * @param string $context Optional. Added context for modifications.
	 * @param object $controller Optional. Add existing controller if you want.
	 **/
	public function __construct( $post, $context = '', $controller = null ) {
		Parent::__construct( $post, $controller );
		// Add standard WordPress data
		$this->controller->map_collaboration_basics();
		// Add featured image.
		if ( ! in_array( $context, array( 'token-form', 'griditem', 'simplemap' ) ) ) {
			$this->controller->map_full_collaboration();
		}
	}

	public function publish_related_stories( $context = '' ) {
		$grid_mods = array(
			'related' => 'has_stories'
		);
		if ( ! $this->has_stories ) {
			return;
		}
		$grid = new RelatedGrid( $this->stories, $this->type, $grid_mods );
		$grid->publish( $context );

	}

	/**
	 * Publish collaboration map.
	 *
	 * @param string context Context
	 * @return void if no participants are found
	 */
	public function publish_collab_map( $context = '' ) {
		$collab_map_caption = $this->controller->create_map_caption();
		$num_participants = count( $this->participants );
		if ( $num_participants <= 1 ) {
			return;
		}
		$input = array(
			'map_style' => 'network',
			'map_size'  => 'wide',
			'map_markers' => false,
			'map_collaborations' => array(
				0 => $this->post_id
			),
			'map_caption' => $collab_map_caption,
		);
		$map = new SimpleMap( $input, 'collaboration' );
		if ( ! empty( $map ) ) {
			$map->publish();
		}
	}

	public function publish_collab_media_gallery( $context = '' ) {
		if ( ! $this->has_gallery && ! $this->has_video ) {
			return;
		}
		if ( $this->has_gallery ) {
		// Clone gallery items for embedding in the collaboration grid
			foreach ( $this->gallery as $gallery_image ) {
				$item = clone $gallery_image;
				if ( ! $item instanceof Image ) {
					continue;
				}
				$griditem = new GridItem( $item, 'collaboration', $grid_mods );
				$griditem->publish();
			}
		}
	}

	public function publish_collab_video( $context = '' ) {
		if ( $this->has_video ) {
			// Clone first video item for embedding in the collaboration grid
			$item = clone $this->video;
			if ( $item instanceof Video ) {
				$griditem = new GridItem( $item, 'collaboration', $grid_mods );
				$griditem->publish();
			}
		}
	}

	public function publish_collab_files( $context = '' ) {
		if ( ! $this->has_files ) {
			return;
		}
		$doc_block_input = array(
			'add_file' => array(),
		);
		foreach( $this->files as $file ) {
			$doc_block_input['add_file'][] = array( 'file' => $file );
		}
		$doc_block = BasePattern::pattern_factory( $doc_block_input, 'uploaded_files', 'collaboration', true);
		if ( $doc_block instanceof Documentblock ) {
			$grid_mods = array(
				'type' => 'documentblock',
			);
			$griditem = new GridItem( $doc_block, 'collaboration', $grid_mods );
			$griditem->publish();
		}
	}

	public function set_update_form_link( $link ) {
		if ( ! empty( $link ) ) {
			$this->update_form_link = $link;
		}
	}

	public function get_update_form_link() {
		$link = $this->update_form_link;
		return $link;
	}
}
