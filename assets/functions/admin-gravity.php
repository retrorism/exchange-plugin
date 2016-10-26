<?php
	add_filter( 'gform_upload_path', 'change_upload_path', 10, 2 );

	add_filter( 'gform_pre_render', 'exchange_story_form_populate_storyteller_step_1' );

	//Note: when changing drop down values, we also need to use the gform_pre_validation so that the new values are available when validating the field.
	add_filter( 'gform_pre_validation', 'exchange_story_form_populate_storyteller_step_1' );

	//Note: when changing drop down values, we also need to use the gform_admin_pre_render so that the right values are displayed when editing the entry.
	add_filter( 'gform_admin_pre_render', 'exchange_story_form_populate_storyteller_step_1' );

	//Note: this will allow for the labels to be used during the submission process in case values are enabled
	add_filter( 'gform_pre_submission_filter', 'exchange_story_form_populate_storyteller_step_1' );

	// Add oembed key
	add_filter( 'gform_post_data', 'exchange_add_oembed_field_key', 11, 3 );

	// Create unique identifier for stories added by f

	add_filter( 'gform_post_data', 'exchange_add_form_entry_id_and_create_story_submission_hash', 12, 3 );

	/**
	* Attach images uploaded through Gravity Form to ACF Gallery Field
	*
	* @author Joshua David Nelson, josh@joshuadnelson.com
	* @return void
	*/
	$collaboration_form_id = get_option( 'options_collaboration_update_form' );
	if ( $collaboration_form_id && function_exists('exchange_set_gravity_collaboration_attachments') ) {
		add_filter( 'gform_after_submission_' . $collaboration_form_id , 'exchange_set_gravity_collaboration_attachments', 10, 1 );
	}

	/**
	* Attach images uploaded through Gravity Form to newly created story
	*
	* @author Joshua David Nelson, josh@joshuadnelson.com
	* @return void
	*/
	$story_form_id = get_option( 'options_story_update_form' );
	if ( $story_form_id && function_exists('exchange_set_gravity_story_attachments') ) {
		add_filter( 'gform_after_submission_' . $story_form_id, 'exchange_set_gravity_story_attachments', 10, 1 );
	}

	function change_upload_path( $path_info, $form_id ) {
		$upload_dir = wp_upload_dir();
		$path_info['path'] = trailingslashit( $upload_dir['path'] );
		$path_info['url'] = trailingslashit( $upload_dir['baseurl'] );
		$collaboration_id = get_query_var('update_id', false);
		if ( ! $collaboration_id ) {
			return $path_info;
		}
		// Add upload to the right programme-round-folder immediately.
		$collaboration_obj = BaseController::exchange_factory( $collaboration_id );
		if ( ! $collaboration_obj instanceof Collaboration ) {
			return $path_info;
		}
		$programme_round_obj = $collaboration_obj->programme_round;
		if ( ! $programme_round_obj instanceof Programme_Round ) {
			return $path_info;
		}
		if ( ! empty( $programme_round_obj->term ) && file_exists( $path_info['path'] . $programme_round_obj->term ) ) {
			$path_info['path'] = trailingslashit( $upload_dir['path'] ) . trailingslashit( $programme_round_obj->term );
			$path_info['url'] = trailingslashit( $upload_dir['baseurl'] ) . trailingslashit( $programme_round_obj->term );
		}
		return $path_info;
	}

	function exchange_story_form_populate_storyteller_step_1( $form ) {

		/* Retrieve story form ID from options and verify */
		$story_form_id = get_option( 'options_story_update_form' );
		if ( $story_form_id && $form['id'] !== intval( $story_form_id ) ) {
			return $form;
		}

		// Verify programme round token with collaboration id to ensure that we're only returning storytellers to people who came in with a programme_round token.
		$collaboration_id = get_query_var('update_id', false);
		$pr_reference = get_query_var('pr_ref', false);
		if ( ! $collaboration_id || ! $pr_reference ) {
			return $form;
		}
		$collaboration_obj = BaseController::exchange_factory( $collaboration_id );
		if ( ! $collaboration_obj instanceof Collaboration ) {
			return $form;
		}
		$programme_round_obj = $collaboration_obj->programme_round;
		if ( ! $programme_round_obj ) {
			return $form;
		}
		$pr_token_from_cid = $programme_round_obj->controller->get_programme_round_token();

		if ( empty( $collaboration_obj->participants || $pr_token_from_cid !== $pr_reference ) ) {
			return $form;
		}

		foreach ( $form['fields'] as $field ) {
			$class = $field->cssClass;
			switch ( $class ) {
				case 'storyteller-select-step-1' :
					$choices = array( array( 'text' => 'Form submitted via programme round token', 'value' => 'Form submitted via programme round token', 'isSelected' => true ) );
					$field['choices'] = $choices;
					break;
				case 'storyteller-select-step-2' :
					$choices = array( array( 'text' => 'Select your name below', 'value' => 0, 'isSelected' => true ) );
					foreach ( $collaboration_obj->participants as $participant ) {
						$choices[] = array( 'text' => $participant->name, 'value' => $participant->post_id, 'isSelected' => false );
					}
					$field['choices'] = $choices;
					break;
				default:
					break;
			}
		}
		return $form;
	}

	/**
	* Add oEmbed field type key to directly show the right output in the frontend.
	*
	* @return $post_data.
	*/
	function exchange_add_oembed_field_key( $post_data, $form, $entry ) {
		$acf_field_key = $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['ACF']['fields']['collaboration-oembed'];
		if ( is_int( $post_data['ID'] ) && ! empty( $post_data['post_custom_fields']['collaboration_video_embed_code'] ) ) {
			$update_acf_field_key = update_post_meta( $post_data['ID'], '_collaboration_video_embed_code', $acf_field_key );
		}
		return $post_data;
	}

	/**
	* Add submission hash and form id for file attachments.
	*
	* @return $post_data.
	*/
	function exchange_add_form_entry_id_and_create_story_submission_hash( $post_data, $form, $entry ) {

		$story_form_id = get_option( 'options_story_update_form' );
		if ( $form['id'] !== intval( $story_form_id ) ) {
			return $post_data;
		}
		if ( empty( $entry['id'] ) ) {
			return $post_data;
		}
		$gf_story_title_field_id = $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['GRAVITY_FORMS']['fields']['story-title'];

		$hash = sha1( $entry['id'] . $entry[ $gf_story_title_field_id ] );
		$post_data['post_custom_fields']['form_entry_id'] = $entry['id'];
		$post_data['post_custom_fields']['story_form_submission_hash'] = $hash;
		return $post_data;
	}

	/**
	* Create the image attachment and return the new media upload id.
	*
	* @author Joshua David Nelson, josh@joshuadnelson.com
	*
	* @see http://codex.wordpress.org/Function_Reference/wp_insert_attachment#Example
	*
	* @link https://joshuadnelson.com/programmatically-add-images-to-media-library/
	*
	* @param string $image_url The url to the image you're adding to the Media library.
	* @param int $parent_post_id Optional. Use to attach the media file to a specific post.
	*/
	function exchange_create_image_id( $image_url, $parent_post_id = null ) {

		// Bail if the image url isn't valid.
		if ( empty( $image_url ) || ! esc_url( $image_url ) ) {
			return false;
		}
		// Escape the url, just to be safe.
		$image_url = esc_url( $image_url );

		// Cache info on the wp uploads dir.
		$wp_upload_dir = wp_get_upload_dir();
		$wp_upload_path = $wp_upload_dir['basedir'];

		// Get the file path.
		$path_array = explode( 'uploads', $image_url );

		// File base name, e.g. image.jpg.
		$file_base_name = basename( $image_url );

		// Combine the two to get the uploaded file path.
		$uploaded_file_path = $wp_upload_path . $path_array[1];

		// Check the type of file. We'll use this as the 'post_mime_type'.
		$filetype = wp_check_filetype( $file_base_name, null );
		// Error check.
		if ( ! empty( $filetype ) && is_array( $filetype ) ) {
			// Create attachment title - basically, pull out the text.
			$post_title = preg_replace( '/\.[^.]+$/', '', $file_base_name );
			// Prepare an array of post data for the attachment.
			$attachment = array(
				'guid'           => $wp_upload_dir['url'] . '/' . basename( $uploaded_file_path ),
				'post_mime_type' => $filetype['type'],
				'post_title'     => esc_attr( $post_title ),
				'post_content'   => '',
				'post_status'    => 'inherit',
			);
			// Set the post parent id if there is one.
			if ( ! is_null( $parent_post_id ) && absint( $parent_post_id ) ) {
				$attachment['post_parent'] = absint( $parent_post_id );
			}
			// Insert the attachment.
			$attach_id = wp_insert_attachment( $attachment, $uploaded_file_path );
			// Error check.
			if ( ! is_wp_error( $attach_id ) ) {
				// Generate wp attachment meta data.
				if ( file_exists( ABSPATH . 'wp-admin/includes/image.php' ) && file_exists( ABSPATH . 'wp-admin/includes/media.php' ) ) {
					require_once( ABSPATH . 'wp-admin/includes/image.php' );
					require_once( ABSPATH . 'wp-admin/includes/media.php' );
					$attach_data = wp_generate_attachment_metadata( $attach_id, $uploaded_file_path );
					wp_update_attachment_metadata( $attach_id, $attach_data );
				} // End if file exists check.
				$attachment_acf = acf_get_attachment( $attach_id );
			}
			return $attach_id;
		} else {
			return false;
		} // End if $filetype.
	}

	function exchange_set_gravity_collaboration_attachments( $entry ) {

		$gf_gallery_field_id = $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['GRAVITY_FORMS']['fields']['collaboration-gallery']; // the gallery upload field id
		$gf_documents_field_id = $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['GRAVITY_FORMS']['fields']['collaboration-documents']; // the file upload field id
		$acf_gallery_field_id = $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['ACF']['fields']['collaboration-gallery']; // the acf gallery field id
		$acf_documents_field_id = $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['ACF']['fields']['collaboration-documents']; // the acf documents field id


		//Retrieve story form ID from options and verify.
		$collaboration_form_id = get_option( 'options_collaboration_update_form' );
		if ( empty( $collaboration_form_id ) || intval( $entry['form_id'] ) !== intval( $collaboration_form_id ) ) {
			return;
		}

		// get post
		if ( isset( $entry['post_id'] ) ) {
			$post = get_post( $entry['post_id'] );
			if ( is_null( $post ) ) {
				return;
			}
		} else {
			return;
		}

		// Clean up images upload and create array for gallery field
		if ( isset( $entry[ $gf_gallery_field_id ] ) ) {
			$images = stripslashes( $entry[ $gf_gallery_field_id ] );
			$images = json_decode( $images, true );
			if ( ! empty( $images ) && is_array( $images ) ) {
				// Retrieve existing documents.
				$gallery = get_post_meta( $post->ID, 'collaboration_gallery', true );
				if ( ! is_array( $gallery ) ) {
					$gallery = array();
				}
				foreach( $images as $key => $value ) {
					// This is the other function you need: https://gist.github.com/joshuadavidnelson/164a0a0744f0693d5746
					if( function_exists( 'exchange_create_image_id' ) ) {
						$image_id = exchange_create_image_id( $value, $post->ID );
						if( $image_id ) {
							$gallery[] = $image_id;
						}
					}
				}
			}
			// Update gallery field with array
			if( ! empty( $gallery ) ) {
				update_field( $acf_gallery_field_id, $gallery, $post->ID );
			}
		}

		// Clean up document upload and create array for documents field
		if ( isset( $entry[ $gf_documents_field_id ] ) ) {
			$files = stripslashes( $entry[ $gf_documents_field_id ] );
			$files = json_decode( $files, true );
			if ( ! empty( $files) && is_array( $files ) ) {
				// Retrieve existing documents.
				$documents = get_post_meta( $post->ID, 'collaboration_documents', true );
				if ( ! is_array( $documents ) ) {
					$documents = array();
				}
				foreach( $files as $key => $value ) {
					// This is the other function you need: https://gist.github.com/joshuadavidnelson/164a0a0744f0693d5746
					if( function_exists( 'exchange_create_image_id' ) ) {
						$file_id = exchange_create_image_id( $value, $post->ID );
						if( $file_id ) {
							$documents[] = $file_id;
						}
					}
				}
			}
			if ( ! empty( $documents ) ) {
				update_field( $acf_documents_field_id, $documents, $post->ID );
			}
		}

		if ( isset( $gallery ) || isset( $documents ) ) {
			wp_update_post( $post );
		}
	}

	function exchange_set_gravity_story_attachments( $entry ) {
		$gf_story_images_field_id = $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['GRAVITY_FORMS']['fields']['story-images']; // the story attachments
		$gf_story_title_field_id = $GLOBALS['EXCHANGE_PLUGIN_CONFIG']['GRAVITY_FORMS']['fields']['story-title'];

		//Retrieve story form ID from options and verify.
		$story_form_id = get_option( 'options_story_update_form' );
		if ( empty( $story_form_id ) || intval( $entry['form_id'] ) !== intval( $story_form_id ) ) {
			return;
		}
		$post_id = $entry['post_id'];
		$story_submission_hash = get_post_meta( $post_id, 'story_form_submission_hash', true );
		if ( sha1( $entry['id'] . $entry[ $gf_story_title_field_id ] ) !== $story_submission_hash ) {
			return;
		}
		// Delete submission hash.
		delete_post_meta( $post_id, 'story_form_submission_hash' );

		$images = JSON_decode( $entry[ $gf_story_images_field_id ] );
		if ( ! is_array( $images ) || count( $images ) < 1 ) {
			return;
		}
		// Attach images to post.
		foreach ( $images as $image ) {
			$attachment_id = exchange_create_image_id( $image, $post_id );
		}
	}



?>
