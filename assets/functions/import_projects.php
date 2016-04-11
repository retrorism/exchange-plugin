<?php
/**
 * Import functions
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 10/04/2016
 *
 * @package Exchange Plugin
 * TODO Cleanup and automation / cron_scheduling / hooking.
 **/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
};

function tandem_importer() {
  $result = array();
  $args = array(
    'post_type' => 'participant',
    'posts_per_page' => -1,
    'post_status' => 'draft'

  );
  $collab_args = array(
    'post_type' => 'collaboration',
    'posts_per_page' => 1,
    'post_status' => 'draft',
    'meta_key' => 'collaboration_id',
    'fields' => 'ids'
  );
  $query = new WP_Query($args);
  $participants = $query->posts;
  if(!empty($participants)) {
    foreach($participants as $participant) {
      // lookup collab ID
      $cid = get_field('collaboration_id', $participant->ID);
      // set as metavalue
      $collab_args['meta_value'] = $cid;
      // create new query with this metavalue
      $collab_query = new WP_Query($collab_args);
      // result is collab_post_id;
      $collab = $collab_query->posts[0];
      // get relationship data from collab post
      $party = get_field('participants',$collab,false);
      if (!empty($party)) {
        array_push($party,$participant->ID);
      }
      else {
        $party[0] = $participant->ID;
      }
      update_field('field_56b9b7c755a9f', $party, $collab);
      $result['collab_post_id: '.$collab]['participants1'] = get_field('participants',$collab,false);
      $result['collab_post_id: '.$collab]['title'] = get_the_title($collab);
      $result['collab_post_id: '.$collab]['collab_id'] = $cid;
      $result['collab_post_id: '.$collab]['participants2'] = $party;
    }
  }
  else {
    $result[0] = "No participants found";
  }
  return $result;
}

	function add_taxo( $taxonomy, $term ) {
		$term_id = term_exists( htmlspecialchars( $term ), $taxonomy );
		if ( $term_id > 0 ) {
			//echo "existing term found";
			return $term_id;
		} else {
			//echo "adding " . $term . " to " . $taxonomy ;
			$result = wp_insert_term( htmlspecialchars( $term ), $taxonomy );
		}
	}

	function add_term_to_cid( $cid, $arr ) {
		$collab_args = array(
		  'post_type' => 'collaboration',
		  'posts_per_page' => 1,
		  'post_status' => 'draft',
		  'meta_key' => 'collaboration_id',
		);
		$collab_args['meta_value'] = $cid;
		// create new query with this metavalue
		$collab_query = new WP_Query($collab_args);
		// result is collab_post_id;
		if ( ! empty( $collab_query->posts ) ) {
			$collab = $collab_query->posts[0];
			if ( $collab->post_type == 'collaboration' ) {
				//print_r( $arr );
				foreach ( $arr as $tax => $terms ) {
					$ints = array();
					foreach ( $terms as $term ) {
						echo $term;
						echo htmlspecialchars( $term );
						$obj = get_term_by( 'name', htmlspecialchars( $term ), $tax, 'slug');
						if ( is_object( $obj ) && get_class( $obj ) === 'WP_Term' ) {
							if ( ! empty( $obj->term_id ) ) {
								$ints[] = $obj->term_id;
							}
						}
					}
					$ints = array_map( 'intval', $ints );
					$ints = array_unique( $ints );
					var_dump( $ints );
					var_dump( $tax );
					echo $collab->post_title;
					$result = wp_set_object_terms( $collab->ID, $ints, $tax );
					print_r($result);
				}
			}
		}
	}

	function tandem_tag_importer() {
		$fh = fopen( EXCHANGE_PLUGIN_PATH . 'taxonomies.csv', 'r' );

		if ($fh) {
			$line = 0; // 1
			while ( ( $row = fgetcsv( $fh ) ) !== false ) {
				// csv headings, so continue/ignore this iteration:
				if ($line == 0) { // 2
					$line++; // 3
					continue; // 4
				}
				$col = 0;
				$cid = $row[0];
				$arr = array();
				foreach ( $row as $term ) {
					if ( ! empty( $term ) && $col > 0 ) {
						if ( $col < 3 )	{
							$tax = 'location';
						}
						$arr[$tax][] = $term;
						echo add_taxo( $tax, $term );
						add_term_to_cid( $cid, $arr );
					}
					$col++;
				}
				// Keep logic here to add to database, line 1 onwards
			}
		}

		fclose($fh);
	}
