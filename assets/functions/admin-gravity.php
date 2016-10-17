<?php
	// add_filter( 'gform_upload_path', 'change_upload_path', 10, 2 );
	// function change_upload_path( $path_info, $form_id ) {
	// 	$upload_dir = wp_upload_dir();
	// 	$path_info['path'] = trailingslashit( $upload_dir['path'] );
	// 	$path_info['url'] = trailingslashit( $upload_dir['baseurl'] );
	// 	$collaboration_id = get_query_var('update_id', false);
	// 	if ( ! $collaboration_id ) {
	// 		return $path_info;
	// 	}
	// 	// Add upload to the right programme-round-folder immediately.
	// 	$collaboration_obj = BaseController::exchange_factory( $collaboration_id );
	// 	if ( ! $collaboration_obj instanceof Collaboration ) {
	// 		return $path_info;
	// 	}
	// 	$programme_round_obj = $collaboration_obj->programme_round;
	// 	if ( ! $programme_round_obj instanceof Programme_Round ) {
	// 		return $path_info;
	// 	}
	// 	if ( ! empty( $programme_round_obj->term ) && file_exists( $path_info['path'] . $programme_round_obj->term ) ) {
	// 		$path_info['path'] = trailingslashit( $upload_dir['path'] ) . trailingslashit( $programme_round_obj->term );
	// 		$path_info['url'] = trailingslashit( $upload_dir['baseurl'] ) . trailingslashit( $programme_round_obj->term );
	// 	}
	// 	return $path_info;
	// }

	add_filter( 'gform_pre_render', 'exchange_story_form_populate_storyteller_step_1' );

	//Note: when changing drop down values, we also need to use the gform_pre_validation so that the new values are available when validating the field.
	add_filter( 'gform_pre_validation', 'exchange_story_form_populate_storyteller_step_1' );

	//Note: when changing drop down values, we also need to use the gform_admin_pre_render so that the right values are displayed when editing the entry.
	add_filter( 'gform_admin_pre_render', 'exchange_story_form_populate_storyteller_step_1' );

	//Note: this will allow for the labels to be used during the submission process in case values are enabled
	add_filter( 'gform_pre_submission_filter', 'exchange_story_form_populate_storyteller_step_1' );

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
?>
