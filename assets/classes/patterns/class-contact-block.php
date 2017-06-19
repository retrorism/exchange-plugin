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
	 * @var array $user_meta Array with all team member details.
	 */
	protected $user_meta;

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
		$this->output_tag_open();
		if ( $this->input instanceof Participant ) {
			// $this->set_participant_meta();
			// $this->set_participant_image();
			$this->output .= $this->create_storyteller_card();
		} elseif ( is_array( $this->input ) && ! empty( $this->input['ID'] ) ) {
			$this->set_user_meta();
			$this->set_user_image();
			$this->output .= $this->create_contact_block();
		}
		$this->output_tag_close();
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

	// /**
	//  * Set participant image
	//  */
	// protected function set_participant_image() {
	// 	if ( ! $this->input->has_featured_image ) {
	// 		return;
	// 	}
	// 	$image = $this->input->featured_image;
	// 	if ( $image instanceof Image ) ) {
	// 		$this->user_image = $image;
	// 	}
	// }

	/**
	 * Set extra user fields
	 */
	private function set_user_meta() {
		$user_meta = get_user_meta( $this->input['ID'] );
		if ( ! empty( $user_meta ) ) {
			$this->user_meta = $user_meta;
		}
	}

	// /**
	//  * Set extra participant fields
	//  */
	// private function set_participant_meta() {
	// 	$user_meta = array();
	// 	$this->input->details;
	// 	if ( ! empty( $user_meta ) ) {
	// 		$this->user_meta = $user_meta;
	// 	}
	// }

	/**
	 * Get user image.
	 *
	 * @return object $image Image Pattern object.
	 */
	private function get_user_image() {
		$image = $this->user_meta['user_image'][0];
		if ( empty( $image ) ) {
			return;
		}
		if ( function_exists( 'acf_get_attachment' ) ) {
			$input = acf_get_attachment( $image );
			if ( empty( $input ) ) {
				return;
			}
			$image_mods = array(
				'style' => 'rounded',
			);
			$image_obj = new Image( $input, $this->element, $image_mods );
			if ( ! $image_obj instanceof Image ) {
				return;
			}
		}
		return $image_obj;
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
			if ( ! empty( $this->user_image ) ) {
				$user_image = $this->user_image;
			}
			if ( ! empty( $this->user_meta ) ) {
				$user_meta = $this->user_meta;
			}
			$user_info = $this->input;
			include( locate_template( 'parts/contact-block.php' ) );
			$contact_block = ob_get_contents();
			ob_end_clean();
		}
		return $contact_block;
	}

		/**
	 * Retrieve team member details;
	 *
	 * @since 0.1.0
	 *
	 * @return string $contact_block HTML output.
	 **/
	protected function create_storyteller_card() {
		if ( '' !== locate_template( 'parts/storyteller-card.php' ) ) {
			ob_start();
			$exchange = $this->input;
			include( locate_template( 'parts/storyteller-card.php' ) );
			$contact_block = ob_get_contents();
			ob_end_clean();
		}
		return $contact_block;
	}
}
