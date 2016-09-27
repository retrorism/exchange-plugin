<?php
	add_filter( 'gform_upload_path', 'exchange_change_gravityforms_upload_path', 10, 2 );
	function exchange_change_gravityforms_upload_path( $path_info, $form_id ) {
	   $upload_dir = wp_upload_dir();
	   $path_info['path'] = $upload_dir['path'];
	   $path_info['url'] = $upload_dir['baseurl'];
	   return $path_info;
	}
?>
