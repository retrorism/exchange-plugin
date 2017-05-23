<?php
/**
 * Participant Class
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
 * Participant CPT Class
 *
 * This class serves as the foundation for Tandem participants and other
 * storytellers.
 *
 * @since 0.1.0
 **/
class Participant extends Exchange {

	/**
	 * Contains a reference to the Participant controller, once instantiated.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var object $controller Story controller.
	 **/
	public $controller;

	/**
	 * Name.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var string Name.
	 **/
	public $name;

	/**
	 * E-mail Address
	 *
	 * @since 0.1.0
	 * @access protected
	 * @var string Contactme
	 **/
	protected $contactme;

	/**
	 * E-mail Address Check
	 *
	 * @since 0.1.0
	 * @access public
	 * @var bool Contactme
	 **/
	public $has_contactme = false;

	/**
	 * The collaboration this person is involved in. Can only be changed from the
	 * Collaboration admin page.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var int $collaboration Collaboration ID.
	 */
	public $collaboration;

	/**
	 * The organisation this person works/worked for at the time of participation in a Tandem Programme
	 * Participant admin page.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var string $org_name
	 */
	public $org_name;

	/**
	 * The abbreviated version of this name
	 * Participant admin page.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var string $org_name
	 */
	public $org_short_name;

	/**
	 * The coordinates of the organisation (not always accurate).
	 *
	 * @since 0.1.0
	 * @access public
	 * @var string $org_name
	 */
	public $org_coords = array();

	/**
	 * The city where this organisation is based.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var string $org_name
	 */
	public $org_city;

	/**
	 * The country where this organisation is based.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var string $org_country
	 */
	public $org_country;

	/**
	 * The organisation's website.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var string $org_website
	 */
	public $org_website;

	/**
	 * The organisation's description
	 *
	 * @since 0.1.0
	 * @access public
	 * @var string $org_website
	 */
	public $org_description;
	
	/**
	 * Any other personal details collected by a theme
	 *
	 * @since 0.1.0
	 * @access public
	 * @var array $detais
	 */
	public $details;

	/**
	 * Participant type: container for optional taxonomy.
	 *
	 * @since 0.1.0
	 * @access public
	 * @var object participant type
	 */
	public $participant_type;

	/**
	 * Update form link
	 *
	 * @since 0.1.0
	 * @access protected
	 * @var string $update_form_link
	 */
	 private $update_form_link;

	/**
	 * Constructor for participant objects.
	 *
	 * @since 0.1.0
	 * @access public
	 * @param object $postobj Participant post object.
	 **/
	 public function __construct( $post, $context = '', $controller = null ) {
 		Parent::__construct( $post );
		$this->controller->map_participant_basics( $post );
	}

	public function publish_org_description() {
		$desc = $this->org_description;
		if ( ! empty( $desc ) ) {
			$desc_obj = new Paragraph( $desc,'participant__organisation__description' );
			$desc_obj->publish_stripped();
		}
	}

	public function publish_name() {
		if ( null !== $this->name ) {
			echo esc_html( $this->name );
		}
	}

	public function set_contactme( $e ) {
		$post_id = $this->post_id;

		if ( current_theme_supports( 'exchange_participant_toggle_email' ) ) {
			$show_contactme = get_post_meta( $post_id, 'participant_toggle_email', true );
			if ( '1' !== $show_contactme ) {
				return;
			}
		}

		$this->contactme = $e;
		$this->has_contactme = true;
	}

	public function get_contactme() {
		if ( $this->has_contactme ) {
			return $this->get_contactme;
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

	public function publish_contactme() {
		if ( $this->has_contactme ) {
			if ( function_exists( 'eae_encode_str' ) ) {
				$cm = eae_encode_str( $this->contactme );
			} else {
				$cm = $this->contactme;
			}
			echo '<a href="mailto:' . $cm . '">' . $cm . '</a>';
		}
	}
}
