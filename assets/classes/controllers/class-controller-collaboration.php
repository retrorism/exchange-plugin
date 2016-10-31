<?php
/**
 * Collaboration Controller
 * Author: Willem Prins | SOMTIJDS
 * Project: Tandem
 * Date created: 31/03/2016
 *
 * @package Exchange Plugin
 **/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
};

/**
 * Collaboration Controller class
 *
 * This controller contains all collaboration logic
 *
 * @since 0.1.0
 **/
class CollaborationController extends BaseController {

	public static function get_collaboration_by_participant_id( $participant_id ) {
		$args = array(
			'post_type' => 'collaboration',
			'post_status' => 'publish',
			'meta_query' => array(
				array(
					'key' => 'participants',
					'value' => '"' . $participant_id . '"', // Matches exaclty "123", not just 123. This prevents a match for "1234".
					'compare' => 'LIKE',
				)
			),
		);
		if ( ! $participant_id >= 1  ) {
			//throw new Exception( 'This is not a valid participant ID' );
			return;
		} else {
			$collab_query = new WP_Query( $args );

			if ( ! empty( $collab_query->posts ) ) {
				return new Collaboration( $collab_query->posts[0] );
			} else {
				return false;
			}
		}

	}

	public function map_collaboration_basics() {

		// Add participants.
		$this->set_participants();

		// Add participants' locations
		if ( $this->container->has_participants ) {
			$this->set_collaboration_locations();
		}

		// Add featured image
		$this->set_featured_image();

		// Add tags
		$this->set_ordered_tag_list();

		// Add update token
		$this->set_collaboration_update_form_link();
	}

	public function map_full_collaboration() {
		// Store post ID in a variable for faster access.
		$post_id = $this->container->post_id;

		// Set project website.
		$acf_website = get_post_meta( $post_id, 'collaboration_website', true );
		if ( ! empty( $acf_website ) ) {
			$this->container->website = $acf_website;
		}

		// Set sections.
		//$acf_sections = get_post_meta( $post_id, 'sections', true );
		// if ( ! empty( $acf_sections ) ) {
		// 	$this->set_sections( $acf_sections );
		// }

		// Add description.
		$this->set_description();

		// Add header image.
		$this->set_header_image( 'collaboration__header' );

		// Set video and gallery.
		$this->set_media();

		// Set shared stories.
		if ( $this->container->has_participants ) {
			$this->set_collaboration_stories();
		}

		// Set related content.
		$this->set_related_content();

	}

	protected function set_media() {
		$this->set_video();
		$this->set_gallery();
		$this->set_collaboration_files();
	}

	protected function set_collaboration_files() {
		$post_id = $this->container->post_id;
		$files = get_post_meta( $post_id, 'collaboration_documents', true );
		if ( ! empty( $files ) ) {
			$this->container->files = $files;
			$this->container->has_files = true;
		}
	}

	public function google_geocode ( $address ) {
	/* Google */

	$geocode_url = 'https://maps.googleapis.com/maps/api/geocode/json?address=';
	$geocode_url .= $address;
	$json = file_get_contents($geocode_url);
	$json = json_decode($json);

	/* found location */
	if ($json->{'status'} == 'OK') {
		$results = $json->{'results'};
		$location = $results[0]->{'geometry'}->{'location'};
		$location->address = $results[0]->formatted_address;
		return $location;
	}

	/* else */
	return array('lat' => 0, 'lng' => 0);
}


	protected function set_collaboration_locations() {
		$locations = array();
		foreach( $this->container->participants as $p_obj ) {
			$p_id = $p_obj->post_id;
			if ( ! $p_obj instanceof Participant ) {
				continue;
			}

			if ( !empty( $p_obj->org_name ) ) {
				$locations[$p_id]['org_name'] = $p_obj->org_name;
			}
			if ( ! empty( $p_obj->org_coords ) ) {
				$locations[$p_id]['org_lat'] = $p_obj->org_coords['lat'];
				$locations[$p_id]['org_lng'] = $p_obj->org_coords['lng'];
				$locations[$p_id]['org_address'] = $p_obj->org_coords['address'];
			} else {
				$geocode = true;
			}
			if ( ! empty( $p_obj->org_city ) ) {
				$locations[$p_id]['org_city'] = $p_obj->org_city;
			}
			if ( isset( $geocode ) && ! empty( $p_obj->org_city ) ) {
				$geocoded_coords = $this->google_geocode( urlencode( $p_obj->org_city ) );
				if ( ! empty( $geocoded_coords->lat )
					&& ! empty( $geocoded_coords->lng )
					&& ! empty( $geocoded_coords->address ) ) {
						$locations[$p_id]['org_lat'] = $geocoded_coords->lat;
						$locations[$p_id]['org_lng'] = $geocoded_coords->lng;
						$locations[$p_id]['org_address'] = $geocoded_coords->address;
						$geocoded = array(
							'address' => $geocoded_coords->address,
							'lat' => $geocoded_coords->lat,
							'lng' => $geocoded_coords->lng,
						);
						add_post_meta( $p_id, 'organisation_location', $geocoded, false );
					}
}
		}
		if ( count( $locations ) > 1 ) {
			$this->container->locations = $locations;
			$this->container->has_locations = true;
		}
	}

	protected function set_collaboration_stories() {
		if ( ! $this->container->participants ) {
			return;
		}
		foreach ( $this->container->participants as $p_obj ) {
			$meta_query_arr[] = $p_obj->post_id;
		}
		$args = array(
			'post_type' => 'story',
			'post_status' => 'publish',
	    	'meta_query' => array(
				array(
					'key'   => 'storyteller',
					'value' => $meta_query_arr,
					'compare' => 'IN'
				),
			)
		);
		$query = new WP_Query( $args );
		if ( empty( $query->posts ) ) {
			return;
		}
		foreach( $query->posts as $post ) {
			$this->container->stories[] = $post;
		}
		if ( count( $this->container->stories ) > 0 ) {
			$this->container->has_stories = true;
		}
	}

	/**
	 * Set participant IDs
	 *
	 * @return void.
	 */
	protected function set_participants() {
		$acf_participants = get_post_meta($this->container->post_id, 'participants', true );
		if ( empty( $acf_participants ) ) {
			return;
		}
		foreach( $acf_participants as $participant ) {
			$participant_obj = self::exchange_factory( $participant );
			if ( $participant_obj ) {
				$this->container->participants[] = $participant_obj;
			}
		}
		if ( ! empty( $this->container->participants ) ) {
			$this->container->has_participants = true;
		}
	}

	/**
	 * Set collaboration description
	 *
	 * @return void.
	 */
	protected function set_description() {
		$post_id = $this->container->post_id;
		$description = get_post_meta(  $post_id, 'description', true );
		if ( empty( $description ) || ! is_string( $description ) ) {
			return;
		}
		$length = str_word_count( $description );
		$add_translation = get_post_meta(  $post_id, 'add_translation', true );
		if ( $add_translation ) {
			$translations = get_post_meta( $post_id, 'translations', true );
		}
		if ( ! isset( $translations ) || empty( $translations ) ) {
			$this->container->description = new Paragraph( $description );
			$this->container->description_length = $length;
		} else {
			$desc_mods = array(
					'type' => 'has_translations',
				);
			$input = array(
				'text'            => $description,
				'add_translation' => true,
				'translations'    => $translations,
			);

			// Replace desc. length with that of a translation (if it exceeds the original text's length.
			foreach( $translations as $translation ) {
				$text = $translation['translation_text'];
				if ( ! empty( $text ) && is_string( $text ) && str_word_count( $text ) > $length ) {
					$length = str_word_count( $text );
				}
			}
			$translated_description = new TranslatedParagraph($input, 'collaboration__description', $desc_mods );
			$this->container->description = $translated_description;


			$this->container->description_length = $length;
		}
	}

	public function create_map_caption() {
		$collab_map_caption = '"' . $this->container->title . '"';
		$tandem_length = count( $this->container->participants );
		if ( $this->container->has_participants && $tandem_length > 1 ) {
			$collab_map_caption .= ': a connection between ';
			for( $i = 0; $i < $tandem_length; $i++ ) {
				$city = $this->container->participants[$i]->org_city;
				$country = $this->container->participants[$i]->org_country;

				if ( empty ( $city ) ) {
					continue;
				}
				if ( ! empty( $country ) ) {
					$city .= ' (' . $country .')';
				}
				if ( $i !== $tandem_length - 1 ) {
					$collab_map_caption .= $city . ' & ';
				} else {
					$collab_map_caption .= $city;
				}
			}
		}
		return $collab_map_caption;
	}

	protected function set_related_content() {
		$post_id = $this->container->post_id;
		if ( get_post_meta(  $post_id, 'related_content_auto_select', $post_id, true ) ) {
			$related_content = $this->get_related_grid_content_by_tags();
		} else {
			$related_content = get_post_meta( 'related_content', $post_id, true );
		}
		if ( is_array( $related_content ) && count( $related_content ) > 0 ) {
			$this->container->has_related_content = true;
			$this->set_related_grid_content( $related_content );
		}
	}


	protected function set_tag_from_programme_round() {
		$slug = $this->container->programme_round->term;
		if ( empty( $slug ) ) {
			return;
		}
		$term_id = wp_set_object_terms( $this->container->post_id, $slug, 'post_tag' );
		if ( ! empty( $term_id ) ) {
			return $term_id;
		}
	}

	protected function set_collaboration_update_form_link() {
		$post_id = $this->container->post_id;
		$link = get_post_meta( $post_id, 'collaboration_update_form_link', true );
		$this->container->set_update_form_link( $link );
	}

}
