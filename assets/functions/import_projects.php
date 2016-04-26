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

function tandem_image_importer() {
	$result = array();
	$collab_args = array(
		'post_type' => 'collaboration',
		'posts_per_page' => -1,
		'post_status' => 'draft',
	);
	$query = new WP_Query($collab_args);
	$collabs = $query->posts;
	$upload_path = wp_get_upload_dir();
	if(!empty($collabs)) {
		$fail = 0;
		$success = 0;
		foreach($collabs as $collab) {
			// lookup collab ID
			$name = $collab->post_title;
			// set as metavalue
			$name_lower = strtolower( $name );
			$path = ABSPATH . 'collab-data/' . $name_lower . '.jpg';
			$file_clean = str_replace( '-','_', sanitize_title( $name_lower ) . '.jpg' );
			$path_clean = ABSPATH . 'collab-data/' . $file_clean ;
			$upload_path_clean = $upload_path['path'] . '/' . $file_clean ;
			$post_id = $collab->ID;
			echo 'post_id: ' . $post_id . '<br />';
			echo 'path: ' . $path . '<br />';
			echo 'file_clean: ' . $file_clean . '<br />';
			echo 'path_clean: ' . $path_clean . '<br />';
			echo 'upload_path_clean: ' . $upload_path_clean . '<br />';
			if ( file_exists( $path ) ) {
				rename( $path, $upload_path_clean );

				// Check the type of file. We'll use this as the 'post_mime_type'.
				$filetype = wp_check_filetype( basename( $upload_path_clean ), null );

				// Prepare an array of post data for the attachment.
				$attachment = array(
					'guid'           => $upload_path['url'] . '/' . basename( $file_clean ),
					'post_mime_type' => $filetype['type'],
					'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file_clean ) ),
					'post_content'   => '',
					'post_status'    => 'inherit'
				);

				print_r( $attachment );
				//
				// // Insert the attachment.
				$attach_id = wp_insert_attachment( $attachment, $file_clean, $post_id );
				//
				// // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
				//
				// // Generate the metadata for the attachment, and update the database record.
				$attach_data = wp_generate_attachment_metadata( $attach_id, $file_clean );
				wp_update_attachment_metadata( $attach_id, $attach_data );
				//
				set_post_thumbnail( $post_id, $attach_id );
				if ( has_post_thumbnail( $post_id ) ) {
					echo "yes<br />";
				}
			} else {
				echo "no<br />";
				$fail++;
			}
		}
		echo "<hr>" . $success . " successes and " . $fail . " failures.";
	}
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

function match_organisation_by_pid( $orgs, $pid ) {
	$participant_args = array(
	  'post_type' => 'participant',
	  'posts_per_page' => 1,
	  'post_status' => 'draft',
	  'meta_key' => 'participant_id',
	  'meta_value' => $pid,
	);
	$participant_query = new WP_Query($participant_args);
	if ( empty( $participant_query->posts ) ) {
		return;
	}
	$participant = $participant_query->posts[0];

	if ( $participant->post_type !== 'participant' ) {
		return;
	}

	if ( empty( $orgs[ $pid ] ) ) {
		return;
	}
	$org = $orgs[$pid];
	echo htmlspecialchars( $org ) .'<br />';
	update_field( 'organisation_name', $org, $participant->ID );
}

function tandem_organisation_name_matcher() {
	$fh = fopen( EXCHANGE_PLUGIN_PATH . 'organisation_names.csv', 'r' );

	if ($fh) {
		echo "yup";
		$orgs = array();
		while ( ( $row = fgetcsv( $fh ) ) !== false ) {
			print_r( $row);
			// csv headings, so continue/ignore this iteration:
			if ($line == 0) { // 2
				$line++; // 3
				continue; // 4
			}
			$col = 0;
			$pid = $row[0];
			$org = $row[1];
			$orgs[$pid] = $org;
			match_organisation_by_pid($orgs, $pid);
			// Keep logic here to add to database, line 1 onwards
			$line++;
		}
	}

	fclose($fh);
}
