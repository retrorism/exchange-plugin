<?php

function exchange_force_facet_search_override() {

	if ( is_search() ) {
		$search_query = urlencode( get_query_var( 's' ) );
		$search_string = is_string( $search_query ) ? $search_query : '';
		wp_redirect( home_url( '/archive/' . '?fwp_search=' . $search_string ) );
		exit();
	}
}

add_action( 'template_redirect', 'exchange_force_facet_search_override' );

if ( class_exists('FacetWP' ) ) {

	add_filter( 'facetwp_render_output', function( $output, $params ) {
		$post_ids = (array) FWP()->facet->query_args['post__in'];
		$facets = FWP()->facet->ajax_params['facets'];
		$selected_facets = array();
		foreach( $facets as $facet ) {
			if ( empty( $facet['selected_values'] ) ) {
				continue;
			}
			$facet_name = $facet['facet_name'];
			if ( 'search' === $facet_name ) {
				$selected_facets[ $facet_name ] = $facet['selected_values'];
				continue;
			}
			$selected_facets[ $facet_name ] = array();
			foreach( $facet['selected_values'] as $value ) {
				$term = get_term_by('slug', $value, $facet_name );
				if ( ! $term instanceof WP_Term ) {
					continue;
				}
				$term_obj = new StdClass;
				$term_obj->slug = $value;
				$term_obj->name = $term->name;
				$selected_facets[$facet_name][] = $term_obj;
			}
		}
		$output['settings']['matches'] = $post_ids;
		$output['settings']['selected_facets'] = $selected_facets;

		return $output;
	}, 10, 2 );

	add_filter( 'facetwp_template_force_load', '__return_true' );

	add_filter( 'facetwp_template_use_archive', '__return_true' );

	add_filter( 'facetwp_facet_html', function( $output, $params ) {
	    if ( 'search' == $params['facet']['name'] ) {
	        $output = '<span class="facetwp-search-wrap"><input type="text" class="facetwp-search" value="" placeholder=""><i class="facetwp-btn--exchange">';
	        $output .= exchange_build_svg( get_stylesheet_directory() . '/assets/images/svg/exchange_search.svg' );
	        $output .= '</i></span>';
	    }
	    return $output;
	}, 10, 2 );

}



