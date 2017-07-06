<?php
/**
 * Relevanssi functions and filters
 *
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 04/01/2017
 *
 * @package Exchange Plugin
 **/

add_filter('relevanssi_content_to_index', 'exchange_relevanssi_add_extra_content', 10, 2);

function exchange_relevanssi_add_extra_content( $content, $post ) {
	if ( $post->post_status !== 'publish' ) {
		return $content;
	}
	$add_to_content = array();
    if ( 'collaboration' === $post->post_type ) {
	    $collab = BaseController::exchange_factory( $post, 'relevanssi' );
	    if ( ! $collab instanceof Collaboration ) {
	    	return $content;
	    }
	    $collab->controller->set_participants();
	    if ( empty( $collab->participants ) ) {
	    	return $content;
	    }
	    $add_to_content = array();
	    $index_these_props = array('name','org_name','org_description','org_country');
	    foreach( $collab->participants as $participant ) {
	    	$vars = get_object_vars( $participant );
	    	if ( empty( $vars ) ) {
	    		continue;
	    	}
	    	foreach( $index_these_props as $prop ) {
	    		if ( ! empty( $participant->$prop ) ) {
	    			$add_to_content[] = $participant->$prop;
	    		}
	    	}
	    	$participant = null;
	    }
	    $collab = null;
    } elseif ( 'story' === $post->post_type ) {
    	global $wpdb;
    	$rows = $wpdb->get_results("SELECT meta_value FROM wp_postmeta WHERE post_id = $post->ID AND meta_key LIKE 'sections_%text' OR meta_key LIKE 'sections_%question' OR meta_key LIKE 'sections_%answer'");
    	if ( empty( $rows ) ) {
    		return $content;
		}
		foreach( $rows as $row ) {
			if ( $row->meta_value !== null ) {
				$add_to_content[] = $row->meta_value;
			}
		}
    }    
    if ( empty( $add_to_content ) ) {
		return $content;
	}
	$content .= implode(' | ', $add_to_content );
	$add_to_content = null;
	return $content;
}