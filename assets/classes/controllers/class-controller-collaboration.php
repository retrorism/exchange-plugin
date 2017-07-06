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

		// Add featured image
		if ( ! in_array( $this->container->context, array( 'form-token','relevanssi' ) ) ) {
			$this->set_featured_image();
		}

		// Add participants' locations
		if ( $this->container->has_participants ) {
			$this->set_collaboration_locations();
		}

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
		$this->populate_video_from_uploads();
		$this->set_collaboration_files();
		$this->populate_gallery();
	}

	protected function set_collaboration_files() {
		$post_id = $this->container->post_id;
		$files = get_post_meta( $post_id, 'collaboration_documents', true );
		if ( ! empty( $files ) ) {
			$this->container->files = $files;
			$this->container->has_files = true;
		}
	}

	protected function get_location_coords( $p_obj ) {

		if ( ! class_exists( 'Leaflet_Map_Plugin' ) || ! is_a( $p_obj, 'Participant' ) ) {
			return;
		}
		if ( ! empty( $p_obj->org_coords['address'] ) ) {
			$geocoded = Leaflet_Map_Plugin::geocoder( $p_obj->org_coords['address'] );
            $coords = array( $geocoded->{'lat'}, $geocoded->{'lng'} );
		} elseif ( ! empty( $p_obj->org_city ) ) {
			$geocoded = Leaflet_Map_Plugin::geocoder( $p_obj->org_city );
			$coords = array( $geocoded->{'lat'}, $geocoded->{'lng'} );
		}
		if ( $coords ) {
			return $coords;
		}
	}


	protected function set_collaboration_locations() {
		$locations = array(
			'title' => $this->container->title,
			'link' => $this->container->link,
			'locations' => array(),
		);
		if ( $this->container->has_featured_image && ! empty( $this->container->featured_image->input['sizes'] ) ) {
			$locations['image'] = $this->container->featured_image->input['sizes']['thumbnail'];
		}
		foreach( $this->container->participants as $p_obj ) {
			if ( ! is_a( $p_obj, 'Participant' ) ) {
				continue;
			}
			$p['exchange_id'] = $p_obj->post_id;
			if ( ! empty( $p_obj->name ) ) {
				$p['name'] = $p_obj->name;
			}
			if ( ! empty( $p_obj->org_name ) ) {
				$p['org_name'] = $p_obj->org_name;
			}
			if ( ! empty( $p_obj->org_coords ) ) {
				$lat = floatval( $p_obj->org_coords['lat'] );
				$lng = floatval( $p_obj->org_coords['lng'] );
			}
			if ( ! empty( $lat ) && ! empty( $lng ) ) {
				$p['latlngs'] = array( $lat, $lng );
			} elseif ( ! empty( $p_obj->org_city ) || ! empty( $p_obj->org_coords['address'] ) ) {
				$geocoded_latlngs = $this->get_location_coords( $p_obj );
				if ( ! empty( $geocoded_latlngs ) ) {
					$p['latlngs'] = $geocoded_latlngs;
				}
			}
			if ( ! empty( $p_obj->org_city ) ) {
				$p['org_city'] = $p_obj->org_city;
			}
			if ( $p['latlngs'] ) {
				$locations['locations'][] = $p;
			}
		}
		if ( count( $locations['locations'] ) >= 1 ) {
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
	public function set_participants() {
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
		if ( $add_translation && function_exists('get_field') ) {
			$translations = get_field( 'translations', $post_id );
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
		$related_content = $this->get_related_grid_content_by_tags();
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

	/**
	 * Set video (for collaborations) to populate gallery with
	 *
	 * @since 0.1.0
	 * @author Willem Prins | Somtijds
	 *
	 * @access protected
	 * @return void
	 * @author Willem Prins | Somtijds
	 **/
	protected function populate_video_from_uploads() {
		$video_input = $this->get_video_from_acf();
		if ( empty( $video_input ) ) {
			return;
		}
		$video_obj = new Video( $video_input );
		if ( $video_obj instanceof Video ) {
			$this->container->video[0] = $video_obj;
			$this->container->has_video = true;
		}
	}

	protected function populate_gallery_from_uploads() {
		$unique_arrs = $this->get_gallery_from_acf();
		if ( ! empty( $unique_arrs ) ) {
			$this->prepare_gallery_images( $unique_arrs );
		}
		if ( $this->container->has_video ) {
			foreach( $this->container->video as $video ) {
				if ( $video instanceof Video ) {
					$this->container->gallery[] = clone $video;
				} 
			}
		}
	}

	protected function get_gallery_from_acf() {
		$unique_ids = get_post_meta( $this->container->post_id, $this->container->type . '_gallery', true );
		if ( empty( $unique_ids ) ) {
			return;
		}
		$unique_arrs = array();
		foreach ( $unique_ids as $img_id ) {
			if ( function_exists( 'acf_get_attachment' ) ) {
				$img_arr = acf_get_attachment( $img_id );
				if ( ! empty( $img_arr ) ) {
					$unique_arrs[] = $img_arr;
				}
			}
		}
		return $unique_arrs;
	}

	 /**
	 * Get videos to populate gallery.
	 *
	 * @since 0.1.0
	 * @author Willem Prins | Somtijds
	 *
	 * @access protected
	 * @return array Input array to create Video object with.
	 **/
	protected function get_video_from_acf() {
		// Set empty array for video properties.
		$input = array();
		if ( function_exists( 'get_field' ) ) {
			$video = get_field( $this->container->type . '_video_embed_code', $this->container->post_id );
			$video_caption = get_field( $this->container->type . '_video_caption', $this->container->post_id );
		}
		if ( empty( $video ) || false === strpos( $video, 'iframe' ) ) {
			return;
		}
		$input['video_embed_code'] = $video;
		if ( ! empty( $video_caption ) ) {
			$input['video_caption'] = $video_caption;
		}
		return $input;
	}

	/**
	 * Prepare gallery images from array of unique image arrays.
	 *
	 * @since 0.1.0
	 * @author Willem Prins | Somtijds
	 *
	 * @access protected
	 * @param array $unique_arrs Array with images' properties for each unique image.
	 * @return array $gallery or void
	 **/
	protected function prepare_gallery_images( $unique_arrs ) {
		if ( empty( $unique_arrs ) ) {
			return;
		}
		$index = 1;
		foreach ( $unique_arrs as $img_arr ) {
			$image_mods = array();
			// Add Image post ID and index to gallery item.
			$image_mods['data'] = array(
				'img_id' => $img_arr['ID'],
			 	'index'  => $index,
			);
			$focus_points = exchange_get_focus_points( $img_arr );
			if ( ! empty( $focus_points ) ) {
				$image_mods['data'] = array_merge( $image_mods['data'], $focus_points );
				$image_mods['classes'] = array( 'focus' );
			}
			// Add gallery context.
			$img_obj = new Image( $img_arr, 'gallery', $image_mods );
			if ( $img_obj instanceof Image ) {
				$this->container->gallery[] = $img_obj;
			}
			$index++;
		}
	}

}
