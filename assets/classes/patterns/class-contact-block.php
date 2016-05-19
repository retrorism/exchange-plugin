<?php
/**
 * Contact Block Class
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 18/05/2016
 *
 * @package Exchange Plugin
 **/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
};

/**
 * Contact Class
 *
 *  This pattern creates all Contact blocks
 *
 * @since 0.1.0
 **/
class ContactBlock extends BasePattern {

	/**
	 * User Image
	 *
	 * @var array $user_acf Array with all ACF fields
	 */
	protected $user_acf;

	/**
	 * User Image
	 *
	 * @var object $image Image pattern object.
	 */
	protected $user_image;

	/**
	 * Overwrite initial output value for Contact blocks
	 *
	 * @since 0.1.0
	 * @access protected
	 **/
	protected function create_output() {
		if ( is_array( $this->input ) && ! empty( $this->input['ID'] ) ) {
			$this->set_user_acf();
			$this->set_user_image();
			$this->output_tag_open();
			$this->output .= $this->create_contact_block();
			$this->output_tag_close();
		}
	}

	/**
	 * Set user image
	 */
	protected function set_user_image() {
		$image = $this->get_user_image();
		if ( ! empty( $image ) ) {
			$this->user_image = $image;
		}
	}

	/**
	 * Set extra user fields
	 */
	private function set_user_acf() {
		$user_acf = get_fields( 'user_' . $this->input['ID'] );
		if ( count( $user_acf ) ) {
			$this->user_acf = $user_acf;
		}
	}

	/**
	 * Get user image.
	 *
	 * @return object $image Image Pattern object.
	 */
	private function get_user_image() {
		$acf_image = $this->user_acf['user_image'];
		$image_mods = array(
			'style' => 'rounded',
		);
		$image = new Image( $acf_image, $this->element, $image_mods );
		return $image;
	}

	/**
	 * Retrieve team member details;
	 *
	 * @since 0.1.0
	 *
	 * @return string $contact_block HTML output.
	 **/
	protected function create_contact_block() {
		if ( '' !== locate_template( 'parts/contact-block.php' ) ) {
			ob_start();
			if ( isset( $this->user_image ) ) {
				$user_image = $this->user_image;
			}
			if ( isset( $this->user_acf ) ) {
				$user_acf = $this->user_acf;
			}
			$user_info = $this->input;
			include( locate_template( 'parts/contact-block.php' ) );
			$contact_block = ob_get_contents();
			ob_end_clean();
		}
		return $contact_block;
	}
}
