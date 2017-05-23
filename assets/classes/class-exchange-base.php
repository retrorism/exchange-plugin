<?php
/**
 * Exchange Class
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 12/4/2016
 *
 * @package Exchange Plugin
 * TODO Proper namespacing (http://stackoverflow.com/a/30647705);
 **/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
};

/**
 * Exchange CPT Class
 *
 * This class serves as the foundation for the 4 Tandem Exchange Content Types
 * storytellers.
 *
 * @since 0.1.0
 **/
class Exchange {

	/**
	 * Contains a reference to the Exchange Base controller, once instantiated.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var object $controller Exchange controller.
	 **/
	public $controller;

	/**
	 * Title.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var string Title.
	 **/
	public $title;

	/**
	 * The permalink.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var string Link.
	 **/
	public $link;

	/**
	 * Has CTA check
	 *
	 * @since 0.1.0
	 * @access public
	 * @var string $has_cta Set when grid should show a CTA block. Defaults to false.
	 **/
	public $has_cta = false;


	/**
	 * Featured image.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var Image $header_image Header image object.
	 **/
	public $featured_image;

	/**
	 * Has featured image check
	 *
	 * @since 0.1.0
	 * @access public
	 * @var Bool $has_featured_image Set when featured image is set.
	 **/
	public $has_featured_image = false;

	/**
	 * Header image.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var Image $header_image Header image object.
	 **/
	public $header_image;

	/**
	 * Has header image check.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var Bool $has_header_image Set to true when header image is set.
	 **/
	public $has_header_image = false;

	/**
	 * Gallery
	 *
	 * @since 0.1.0
	 * @access public
	 * @var array $gallery List of image objects
	 **/
	public $gallery = array();

	/**
	 * Gallery
	 *
	 * @since 0.1.0
	 * @access public
	 * @var array $gallery List of files
	 **/
	public $files = array();

	/**
	 * Files check.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var boolean $has_files. Defaults to false.
	 */
	public $has_files = false;

	/**
	 * Has gallery check.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var Bool $has_header_image Set to true when gallery is set (and includes more than 1 picture).
	 **/
	public $has_gallery = false;

	/**
	 * Has editorial intro check
	 *
	 * @since 0.1.0
	 * @access public
	 * @var Bool $has_editorial_intro Set to true when editorial intro is set.
	 **/
	public $has_editorial_intro = false;

	/**
	 * Editorial Intro text taken from excerpt (needs to allow for links).
	 *
	 * @since 0.1.0
	 * @access public
	 * @var string $editorial_intro Editorial Intro.
	 *
	 * @TODO Allow for links.
	 **/
	public $editorial_intro;


	/**
	 * Ordered array for use in grid / single display.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var array $ordered_tag_list Ordered tag-list.
	 **/
	public $ordered_tag_list = array();

	/**
	 * Array to be filled with tags.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var array Tag-list.
	 **/
	public $tag_list = array();

	/**
	 * Has tags
	 *
	 * @since 0.1.0
	 * @access public
	 * @var Bool $has_tags Set to true when tags are set.
	 **/
	public $has_tags;

	/**
	 * List of sections.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var array $sections Sections list, built up out of pattern classes.
	 **/
	public $sections = array();

	/**
	 * Array of videos for publishing
	 *
	 * @since 0.1.0
	 * @access public
	 * @var array $video
	 */
	public $video = array();

	/**
	 * Video check.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var boolean $has_video. Defaults to false.
	 */
	public $has_video = false;

	/**
	 * Has related content check
	 *
	 * @since 0.1.0
	 * @access public
	 * @var Bool $has_related_content Set when related content is set.
	 **/
	public $has_related_content = false;

	/**
	 * When set, this variable contains a Related Content Grid object.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var object $related_content Related Content Grid object.
	 **/
	public $related_content;


	/**
	 * Geo locations stored in associative array where participant IDs are key, and values
	 * are the organisation's names, lat, and long.
	 *
	 * @since 0.2.0
	 * @access public
	 * @var array $locations List that holds participants' location details.
	 */
	public $locations;

	/**
	 * Geo check.
	 *
	 * @since 0.2.0
	 * @access public
	 * @var boolean $has_locations When there's one or more geolocations added for mapping. Defaults to false.
	 */
	public $has_locations = false;

	/**
	 * Constructor for all CPT objects.
	 *
	 * @since 0.1.0
	 * @access public
	 * @param object $post CPT WP_Post object.
	 * @param string $context Optional. Where this object is created.
	 * @param object $controller Optional. Add a (modified) controller to be used.
	 **/
	public function __construct( $post, $controller = null ) {
		$this->set_controller( $controller );
		$this->controller->map_basics( $this, $post );
	}

	/**
	 * Set controller property to a new instance of Collaboration controller.
	 *
	 * @since 0.1.0
	 * @access private
	 * @param object $controller Controller object (or null).
	 **/
	protected function set_controller( $controller ) {
		$controller_name = get_class( $this ) . 'Controller';
		if ( null === $controller || get_class( $controller ) !== $controller_name ) {
			$this->controller = new $controller_name;
		} else {
			$this->controller = $controller;
		}
		$this->controller->set_container( $this );
	}

	/**
	 * Publish the featured image.
	 *
	 * @since 0.1.0
	 * @access public
	 * @param string context.
	 **/
	public function publish_featured_image( $context = '' ) {
		if ( $this->has_featured_image ) {
			$this->featured_image->publish( $context );
		}
	}


	// /**
	//  * Add tag to tag list, accompanied by its archive link.
	//  *
	//  * @since 0.1.0
	//  * @access public
	//  *
	//  * @param string $name Term name.
	//  * @param string $link Archive link.
	//  **/
	// public function add_tag( $name, $link ) {
	// 	$this->tag_list[] = array(
	// 		'name' => $name,
	// 		'link' => $link,
	// 	);
	// }

	public function publish_header_image() {
		if ( $this->has_header_image ) {
			$this->header_image->publish();
		}
	}

	public function publish_sections() {
		if ( count( $this->sections ) ) {
			// Loop through sections.
			foreach ( $this->sections as $section ) {
				$section->publish();
			}
		}
	}

	/**
	 * Publish galkert
	 *
	 * @param string $context
	 * @return void
	 * @TODO add filter instead of hardcoding the Orbit references
	 */
	public function publish_gallery( $context = '' ) {
		// Add foundation orbit gallery element.
		$context_class = $context . ' orbit-container';
		$output = '';
		$items = count( $this->gallery );
		if ( $this->type = 'collaboration' && $this->has_video ) {
			foreach( $this->video as $video ) {
				if ( $video instanceof Video ) {
					$items++;
				}
			}
		}
		if ( $items >= 1 ) {
			$output .=  '<button class="orbit-previous gallery__navigation gallery__navigation--prev"><svg class="hide-for-sr" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 67.274742 99.65184"><path d="M13.3 0h1.3c0 .3.8 0 .8.7h1.2c0 .8 1.6.4 1.7 1.2h2.5c.2 1.4 2.8 1 3.2 2.3 1.3-.3.8 1 2 .8.3.6 1 .8 1.7 1 0 .5.5 1 1 1s.4.6.5 1c.4 0 .4 0 .4.4 1-.2.8.7 2 .4.2.4.5.7 1.2.7 0 .4.5.3.8.4 0 .3.2.4.4.4.6.2.7.7 1.3.8.2.3.5.3.8.4h.5s.3 1.3 1.5 1c.5.8 1.3 1.3 2.5 1.5 0 .5.5.4.8.5 0 .4.2.4.4.4 0 .3.5.2 1 .3-.2.4 0 .4.3.4.7.6 2 .6 2.4 1.7.7.6 2 .6 2.4 1.6 1 .5 1.6 1.2 3 1.3.4 1.3 1.7 1.7 2.7 2.5.2.8 1.5.3 1.4 1.2.4.3 1 .7 1 1.3 1.3 0 1.3 1 2.5.8.2 1 1.2 1 1.3 1.8.3.2.7.4.8.8.6 0 .8 0 1 .5.5 0 .3-1 .3-1.6-.5 0-.4-.8-1-.8.4-.7.8 0 1.3 0 .2.4.4.7 1 .8 0 .6.6.7.7 1.2.3 1.3-1.2.8-.8 2 0 .2-.2.8 0 1 0 0 .3-.2.4 0l.4.7c.2.3.5.2.8.4 0 0 0 .6.4.4 0 1.3.4 2 .8 2.6h.4v.4s.4-.2.4 0v.3c.2.3.4.5.4.8.3 1.5-.4 1.7-.4 2.7-.4 0-.3.5-.4.8 0 .4-.4.4-.4.8 0 2 .7 3.2.4 5.4-.4 0-.3.5-.4.8-.3 0-.4.2-.4.4-.4.2-1 .3-1.6.3 0 2.2-.7 3.7-.4 6.2 0 1.5-1.4 2-1.3 3.7-.6.3-1 .7-1.2 1.3-.7 0-.2 1.3-1 1.2v.8h-.5c-.3.2-.4.7-.5.8-.3.4-.6.4-1 .6 0 0 0 .4-.3.3 0 0 .2.7 0 .8h-.4v1l-.8.3v.8c-.4 0-.3.6-.4 1-.3 0-.3 0-.3.4-.3.4-.5 1-.4 1.7-1 .8-2 1.7-2.5 3-1.3.5-2.2 1.3-3 2.4-1 .6-2.2 1-2.3 2.5-1 0-1.3.5-2 .7 0 .8-1.4.3-1.3 1.2-.8 0-.5 1-1.6 1-.2.4-.6 1-1.2 1-.5.4-1.5.4-1.6 1.2-.8 0-.5 1-1.7.8-.2 1.3-2 1.2-2.3 2.5-1 0-1.2 1-2.4 1-1 .2-1 1.3-2 1.6-.6 0-.7.8-1.2 1-.3 0-.7.2-1 .7-.5 0-1 .3-1 .8-.6 0-1.2 0-1.2.4-1.2-.3-1 1.2-2.4.7-.7.4-1.2 1-2 1.3-.8.3-1.3 1-1.7 1.6-1.4 0-1.2 1.2-2 1.3-.2 1-1.2 1-1.4 1.6-2-.2-2.3 1.4-3.6 2-1.2.3-1-.8-1.7-.8-1-.2-1 .8-2 .4 0-1-1.6-.6-1.7-1.6-1.2-.3-1.2.7-2.4.4 0-.5 0-.7-.5-.8-.4 0-.4-.2-.4-.4 0-.7.4-.7.5-1.3-1-.2-1 .2-1.7.3-.2.8-.6 0-1 0 0-.7-.6-.8-.5-1.6.5 0 .3-1 .4-1.6-.2-.4-1-.3-1.3-.3-.4 0-.4-.5-1-.5.2-.6 0-1.5.6-1.6-.4-.3-.3-1-1-1.2.3-.6 1-.7 1-1.2-.2-.7.3-.8.4-1.3-.2-1.5 0-2.4.4-3.4s-.6-1-.3-2.3c0-1.7.8-2.5.8-4 .8-.4 1-1.2 1.2-2.2A3.7 3.7 0 0 0 5 73 3.7 3.7 0 0 0 7 71.2c.6 0 .7-.4 1.2-.5 0-.4.8-.2.8-.8.6-.4 1.6-.4 1.7-1.2.8-.2 1.3-1 2-1.2.4-.5.6-1.2 1.7-1.2 0-1.2.8-1.6 1.2-2.4.8-.4 1.6-1 2.5-1.2.2-1 1.5-1 2.4-1.3 0-1.2 1.7-.8 2-1.7.4-.4 1.2-.3 1.3-.8.3-.3.7-.5 1.2-.4 1 0 .7-1.4 2-1.2 0-.8 1-.8 1.2-1.6.5 0 .7 0 .8-.4 1-.3 2.2-.4 3-.8 1 0 .4-1.2 1.2-1.3 0-.4.5-.3.8-.4 0-.4.2-.5.4-.5.3-1.2 1.8-1 1.7-2.6v-1.3c0-1-.4-1.6-1.5-1.6 0-1-1-1-2-.7 0-1-1.6-.4-1.2-1.7-.6-.3-1.6-.3-1.7-1-1 0-1-1-2-.5-.2-.5-.7-1-1.2-1.2-.6 0-.7-.7-1.3-.8 0-.2-.5-.2-.7-.4l-.4-.8h-1.2v-.4H22c-.3-.6-.7-1-1.3-1.2-.3-.5-.6-1-1.3-1-.4-.8-1.2-1.3-2.5-1.5-.3-.5-.7-1-1.3-1-.3-.5-1-.4-1.2-1-1-.4-2-1-2.5-2-1 .3-.8-.8-1.6-.8-.6-.3-.7-.8-1.3-1-.2-.3-1-.2-1-1-.6 0-.7-.5-1.3-.6v-2c0-.4-.5-.3-.8-.4 0-.5 0-.8-.5-1 0-.3 0-.6-.5-.7-.3 0-.3 0-.4-.4-1-.4-1-2-2-2 .2-2-1-2.7-1.8-3.8.2-1 .5-1.7 1-2.5v-5c.2.2.3 0 .3-.3-.2-1 1-.8 1.3-1.2.2-.6.6-1 1.2-1.3 0-.7 0-1-.5-1.3.2-.3.5-.7.4-1.2 1 0 .6-1.6 1.2-2 2.3 0 5 .3 4.5-2.5 1.7-.4 1.3 1.2 3 .8v-1z" fill="#ffbc00" fill-rule="evenodd"/><image src="' . get_template_directory_uri() .'/assets/images/png/T_arrows_Single_WEB.png" xlink:href=""></svg><span class="show-for-sr">Previous Image</span></button>' . PHP_EOL
						.'<button class="orbit-next gallery__navigation gallery__navigation--next"><svg class="hide-for-sr" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 67.274742 99.65184"><path d="M13.3 0h1.3c0 .3.8 0 .8.7h1.2c0 .8 1.6.4 1.7 1.2h2.5c.2 1.4 2.8 1 3.2 2.3 1.3-.3.8 1 2 .8.3.6 1 .8 1.7 1 0 .5.5 1 1 1s.4.6.5 1c.4 0 .4 0 .4.4 1-.2.8.7 2 .4.2.4.5.7 1.2.7 0 .4.5.3.8.4 0 .3.2.4.4.4.6.2.7.7 1.3.8.2.3.5.3.8.4h.5s.3 1.3 1.5 1c.5.8 1.3 1.3 2.5 1.5 0 .5.5.4.8.5 0 .4.2.4.4.4 0 .3.5.2 1 .3-.2.4 0 .4.3.4.7.6 2 .6 2.4 1.7.7.6 2 .6 2.4 1.6 1 .5 1.6 1.2 3 1.3.4 1.3 1.7 1.7 2.7 2.5.2.8 1.5.3 1.4 1.2.4.3 1 .7 1 1.3 1.3 0 1.3 1 2.5.8.2 1 1.2 1 1.3 1.8.3.2.7.4.8.8.6 0 .8 0 1 .5.5 0 .3-1 .3-1.6-.5 0-.4-.8-1-.8.4-.7.8 0 1.3 0 .2.4.4.7 1 .8 0 .6.6.7.7 1.2.3 1.3-1.2.8-.8 2 0 .2-.2.8 0 1 0 0 .3-.2.4 0l.4.7c.2.3.5.2.8.4 0 0 0 .6.4.4 0 1.3.4 2 .8 2.6h.4v.4s.4-.2.4 0v.3c.2.3.4.5.4.8.3 1.5-.4 1.7-.4 2.7-.4 0-.3.5-.4.8 0 .4-.4.4-.4.8 0 2 .7 3.2.4 5.4-.4 0-.3.5-.4.8-.3 0-.4.2-.4.4-.4.2-1 .3-1.6.3 0 2.2-.7 3.7-.4 6.2 0 1.5-1.4 2-1.3 3.7-.6.3-1 .7-1.2 1.3-.7 0-.2 1.3-1 1.2v.8h-.5c-.3.2-.4.7-.5.8-.3.4-.6.4-1 .6 0 0 0 .4-.3.3 0 0 .2.7 0 .8h-.4v1l-.8.3v.8c-.4 0-.3.6-.4 1-.3 0-.3 0-.3.4-.3.4-.5 1-.4 1.7-1 .8-2 1.7-2.5 3-1.3.5-2.2 1.3-3 2.4-1 .6-2.2 1-2.3 2.5-1 0-1.3.5-2 .7 0 .8-1.4.3-1.3 1.2-.8 0-.5 1-1.6 1-.2.4-.6 1-1.2 1-.5.4-1.5.4-1.6 1.2-.8 0-.5 1-1.7.8-.2 1.3-2 1.2-2.3 2.5-1 0-1.2 1-2.4 1-1 .2-1 1.3-2 1.6-.6 0-.7.8-1.2 1-.3 0-.7.2-1 .7-.5 0-1 .3-1 .8-.6 0-1.2 0-1.2.4-1.2-.3-1 1.2-2.4.7-.7.4-1.2 1-2 1.3-.8.3-1.3 1-1.7 1.6-1.4 0-1.2 1.2-2 1.3-.2 1-1.2 1-1.4 1.6-2-.2-2.3 1.4-3.6 2-1.2.3-1-.8-1.7-.8-1-.2-1 .8-2 .4 0-1-1.6-.6-1.7-1.6-1.2-.3-1.2.7-2.4.4 0-.5 0-.7-.5-.8-.4 0-.4-.2-.4-.4 0-.7.4-.7.5-1.3-1-.2-1 .2-1.7.3-.2.8-.6 0-1 0 0-.7-.6-.8-.5-1.6.5 0 .3-1 .4-1.6-.2-.4-1-.3-1.3-.3-.4 0-.4-.5-1-.5.2-.6 0-1.5.6-1.6-.4-.3-.3-1-1-1.2.3-.6 1-.7 1-1.2-.2-.7.3-.8.4-1.3-.2-1.5 0-2.4.4-3.4s-.6-1-.3-2.3c0-1.7.8-2.5.8-4 .8-.4 1-1.2 1.2-2.2A3.7 3.7 0 0 0 5 73 3.7 3.7 0 0 0 7 71.2c.6 0 .7-.4 1.2-.5 0-.4.8-.2.8-.8.6-.4 1.6-.4 1.7-1.2.8-.2 1.3-1 2-1.2.4-.5.6-1.2 1.7-1.2 0-1.2.8-1.6 1.2-2.4.8-.4 1.6-1 2.5-1.2.2-1 1.5-1 2.4-1.3 0-1.2 1.7-.8 2-1.7.4-.4 1.2-.3 1.3-.8.3-.3.7-.5 1.2-.4 1 0 .7-1.4 2-1.2 0-.8 1-.8 1.2-1.6.5 0 .7 0 .8-.4 1-.3 2.2-.4 3-.8 1 0 .4-1.2 1.2-1.3 0-.4.5-.3.8-.4 0-.4.2-.5.4-.5.3-1.2 1.8-1 1.7-2.6v-1.3c0-1-.4-1.6-1.5-1.6 0-1-1-1-2-.7 0-1-1.6-.4-1.2-1.7-.6-.3-1.6-.3-1.7-1-1 0-1-1-2-.5-.2-.5-.7-1-1.2-1.2-.6 0-.7-.7-1.3-.8 0-.2-.5-.2-.7-.4l-.4-.8h-1.2v-.4H22c-.3-.6-.7-1-1.3-1.2-.3-.5-.6-1-1.3-1-.4-.8-1.2-1.3-2.5-1.5-.3-.5-.7-1-1.3-1-.3-.5-1-.4-1.2-1-1-.4-2-1-2.5-2-1 .3-.8-.8-1.6-.8-.6-.3-.7-.8-1.3-1-.2-.3-1-.2-1-1-.6 0-.7-.5-1.3-.6v-2c0-.4-.5-.3-.8-.4 0-.5 0-.8-.5-1 0-.3 0-.6-.5-.7-.3 0-.3 0-.4-.4-1-.4-1-2-2-2 .2-2-1-2.7-1.8-3.8.2-1 .5-1.7 1-2.5v-5c.2.2.3 0 .3-.3-.2-1 1-.8 1.3-1.2.2-.6.6-1 1.2-1.3 0-.7 0-1-.5-1.3.2-.3.5-.7.4-1.2 1 0 .6-1.6 1.2-2 2.3 0 5 .3 4.5-2.5 1.7-.4 1.3 1.2 3 .8v-1z" fill="#ffbc00" fill-rule="evenodd"/><image src="' . get_template_directory_uri() .'/assets/images/png/T_arrows_Single_WEB.png" xlink:href=""></svg><span class="show-for-sr">Next Image</span></button>';
		}
		if ( $this->has_gallery ) {
			$output .= '<ul class="gallery--' . $context_class . '">';
			foreach( $this->gallery as $img ) {
				$img_string = $img->embed('gallery');
				$output .= $img_string;
			}
			if ( $this->has_video && $this->type = 'collaboration' ) {
				foreach( $this->video as $video ) {
					$output .= $video->embed('gallery');
				}
			}
			$output .= '</ul>';
		}
		echo $output;
	}

	public function publish_related_content( $context = '' ) {
		if ( $this->has_related_content ) {
			$this->related_content->publish( $context );
		}
	}

	public function publish_grid_cta( $modifier = '' ) {
		$properties = array();
		if ( function_exists( 'get_field' ) ) {
			$properties = array(
				'block_type' => 'cta',
				'block_alignment' => false,
				'cta_block_elements' => get_field( 'cta_block_elements', $this->post_id ),
				'cta_colour' => get_post_meta( $this->post_id, 'cta_colour', true ),
			);
		}
		if ( $modifier === 'grid_full' ) {
			$properties['block_alignment'] = 'full';
		}
		if ( empty( $properties['cta_block_elements'] ) ) {
			return;
		} else {
			$length = count( $properties['cta_block_elements'] );
			for( $i = 0; $i < $length; $i++ ) {
				if ( 'block_button' === $properties['cta_block_elements'][$i]['acf_fc_layout'] ) {
					// Add permalink to button if link left empty.
					if ( empty( $properties['cta_block_elements'][$i]['button_link'] ) ) {
						$properties['cta_block_elements'][$i]['button_link'] = $this->link;
					}
					if ( empty( $properties['cta_block_elements'][$i]['button_size'] ) ) {
						$properties['cta_block_elements'][$i]['button_size'] = 'small';
					}
				}
			}
		}
		$block = BasePattern::pattern_factory( $properties, 'emphasis_block', 'griditem' );
		echo $block;
	}

	public static function publish_grid_featured( $exchange, $context = '', $modifiers = array() ) {
		$griditem = new Griditem( $exchange, $context, $modifiers );
		$griditem->publish();
	}

	public function publish_video( $context = '' ) {
		if ( $this->has_video ) {
			$this->video->publish();
		}
	}

	public function publish_files( $context = '' ) {
		if ( $this->has_video ) {
			$this->files->publish();
		}
	}

	public function publish_tags( $context = '' ) {
		if ( $this->has_tags ) {
			$output = "<ol>" . PHP_EOL;
			if ( 'griditem' === $context ) {
			 	$list = $this->controller->get_tag_short_list( $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['TAXONOMIES']['grid_tax_max'] );
			} elseif ( 'collaboration' === $context ) {
			 	$list = $this->controller->get_tag_short_list( $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['TAXONOMIES']['collaboration_tax_max'] );
			} else {
				$list = $this->ordered_tag_list;
			}
			foreach ( $list as $term ) {
				$tag_mods = $this->controller->prepare_tag_modifiers( $term, $context );
				$tag = new Tag( $term, $context, $tag_mods );
				$output .= "<li>" . $tag->embed() . "</li>";
			}
			$output .= "</ol>";
			echo $output;
		}
	}

	/**
	 * Publish editorial intro, if available.
	 *
	 * @param string $context Optional. Context for the object.
	 * @return void
	 */
	public function publish_intro( $context = '' ) {
		if ( $this->has_editorial_intro ) {
			$this->editorial_intro->publish();
		}
	}

	/**
	 * Publish sharing buttons
	 *
	 * @param string $context Optional.
	 * @TODO add print options.
	 */
	public function publish_sharing_buttons( $context = '' ) {
		//echo exchange_build_social_icons( $context, array('facebook','twitter','email','print'), $this );
		echo exchange_build_social_icons( $context, array('facebook','twitter','email'), $this );
	}
}
