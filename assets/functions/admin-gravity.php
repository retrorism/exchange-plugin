<?php
// add_filter( 'gform_upload_path', 'exchange_change_gravityforms_upload_path', 10, 2 );
// function exchange_change_gravityforms_upload_path( $path_info, $form_id ) {
//    $upload_dir = wp_upload_dir();
//    $path_info['path'] = $upload_dir['path'] . '/';
//    $path_info['url'] = $upload_dir['baseurl'];
//    return $path_info;
// }

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
function jdn_create_image_id( $image_url, $parent_post_id = null ) {
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
} // end function jdn_create_image_id

/**
* Attach images uploaded through Gravity Form to ACF Gallery Field
*
* @author Joshua David Nelson, josh@joshuadnelson.com
* @return void
*/
add_filter( "gform_after_submission_3", 'jdn_set_collaboration_acf_gallery_and_documents_field', 10, 2 );

function jdn_set_collaboration_acf_gallery_and_documents_field( $entry, $form ) {

	$gf_gallery_field_id = 23; // the gallery upload field id
	$gf_documents_field_id = 24; // the file upload field id

	$acf_documents_field_id = 'field_57e6561ec9866'; // the acf gallery field id
	$acf_gallery_field_id = 'field_577e3c937d7d6'; // the acf gallery field id

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
				if( function_exists( 'jdn_create_image_id' ) ) {
					$image_id = jdn_create_image_id( $value, $post->ID );
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
				if( function_exists( 'jdn_create_image_id' ) ) {
					$file_id = jdn_create_image_id( $value, $post->ID );
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



?>
