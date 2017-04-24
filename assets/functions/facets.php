<?php

if ( class_exists('FacetWP' ) ) {

	add_filter( 'facetwp_render_output', function( $output, $params ) {
		$post_ids = (array) FWP()->facet->query_args['post__in'];
		$output['settings']['matches'] = $post_ids;
		return $output;
	}, 10, 2 );

	add_filter( 'facetwp_template_force_load', '__return_true' );

}