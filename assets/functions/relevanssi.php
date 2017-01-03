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
function exchange_relevanssi_add_extra_content($content, $post) {
    if ( 'collaboration' !== get_post_type( $post ) ) {
    	return $content;
    }
    $collab = BaseController::exchange_factory( $post );
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
    }
    if ( empty( $add_to_content ) ) {
    	return $content;
    }
    $content .= implode(' | ', $add_to_content );
    return $content;
}