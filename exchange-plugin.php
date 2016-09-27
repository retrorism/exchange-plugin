<?php
/**
 * Plugin for Tandem Exchange WordPress theme
 *
 * @link    https://github.com/retrorism/exchange-plugin
 * @package Exchange Plugin
 * @version 0.1.0
 *
 * Plugin Name: Exchange Plugin
 * Plugin URI:  https://github.com/retrorism/exchange-plugin
 * Description: This plugin adds all necessary functionality for the Exchange Theme
 * Author:      Bart Bloemers & Willem Prins
 * Version:     0.1.0
 * Author URI:  http://www.somtijds.nl
 **/

if ( ! function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

if ( ! defined( 'EXCHANGE_PLUGIN' ) ) {
	define( 'EXCHANGE_PLUGIN', 'exchange-plugin' );
}

if ( ! defined( 'EXCHANGE_PLUGIN_FILE' ) ) {
	define( 'EXCHANGE_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'EXCHANGE_PLUGIN_PATH' ) ) {
	define( 'EXCHANGE_PLUGIN_PATH', plugin_dir_path( EXCHANGE_PLUGIN_FILE ) );
}

if ( ! class_exists( 'Exchange_Plugin' ) ) {

	class Exchange_Plugin {

		/**
		 * Constructor for Exchange Plugin.
		 *
		 * @since 0.1.0
		 * @access public
		 *
		 * TODO make the require_functions action more OOP
		 **/
		 public function __construct() {

			add_action( 'plugins_loaded', array( $this, 'exchange_require_functions' ) );

			/* Runs on plugin is activated */
	 		register_activation_hook( EXCHANGE_PLUGIN_FILE, array( $this, 'exchange_plugin_activate' ) );

	 		/* Runs on plugin deactivation */
	 		register_deactivation_hook( EXCHANGE_PLUGIN_FILE, array( $this, 'exchange_plugin_deactivate' ) );

			/* Register classes with autoloader */
			spl_autoload_register( array( $this, 'exchange_auto_load' ) );

		}

		/**
		 * Require our function files.
		 */
		function exchange_require_functions() {
			$files = array(
				'globals.php',
				'admin.php',
				'admin-acf.php',
				'admin-gravity.php',
				'admin-options.php',
				'admin-roles.php',
				'taxonomies.php',
				'post-types.php',
				'public.php',
				'import_projects.php',
				'tokens.php',
			);
			$google_api_filter = ABSPATH . 'acf-google-api-key.php';
			if ( file_exists( $google_api_filter ) ) {
				require_once( $google_api_filter );
			}
			foreach ( $files as $file ) {
				require_once( EXCHANGE_PLUGIN_PATH . 'assets/functions/' . $file );
			}
		}

		/**
		 * Runs on activation of the plugin.
		 **/
		public function exchange_plugin_activate() {
			flush_rewrite_rules();
			require_once( EXCHANGE_PLUGIN_PATH . 'assets/functions/admin-roles.php' );
			if ( function_exists( 'exchange_add_user_management_for_editors') ) {
				exchange_add_user_management_for_editors();
			} else {
				die( $debug );
			}
			return;
		}

		/**
		 * Runs on plugin deactivation.
		 **/
		public function exchange_plugin_deactivate() {
			flush_rewrite_rules();
			exchange_remove_user_management_for_editors();
			return;
		}

		/**
		 * Auto-load our class files.
		 *
		 * @param  string $class Class name.
		 * @return void
		 */
		public function exchange_auto_load( $class ) {
			static $classes = null;

			if ( null === $classes ) {
				$classes = array(
					'exchange'  => EXCHANGE_PLUGIN_PATH . 'assets/classes/class-exchange-base.php',
					'story'  => EXCHANGE_PLUGIN_PATH . 'assets/classes/class-story.php',
					'collaboration'  => EXCHANGE_PLUGIN_PATH . 'assets/classes/class-collaboration.php',
					'programme_round'  => EXCHANGE_PLUGIN_PATH . 'assets/classes/class-programme-round.php',
					'participant'  => EXCHANGE_PLUGIN_PATH . 'assets/classes/class-participant.php',

					'basecontroller'  => EXCHANGE_PLUGIN_PATH . 'assets/classes/controllers/class-controller-base.php',
					'storycontroller'  => EXCHANGE_PLUGIN_PATH . 'assets/classes/controllers/class-controller-story.php',
					'participantcontroller'  => EXCHANGE_PLUGIN_PATH . 'assets/classes/controllers/class-controller-participant.php',
					'collaborationcontroller'  => EXCHANGE_PLUGIN_PATH . 'assets/classes/controllers/class-controller-collaboration.php',
					'programme_roundcontroller'  => EXCHANGE_PLUGIN_PATH . 'assets/classes/controllers/class-controller-programme-round.php',

					'basepattern' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-pattern-base.php',
					'basegrid' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-grid-base.php',
					'simplegrid' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-grid-simple.php',
					'relatedgrid' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-grid-related.php',
					'griditem' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-grid-item.php',

					'baseinterview' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-interview-base.php',
					'byline' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-byline.php',
					'contactblock' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-contact-block.php',
					'editorialintro' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-editorial-intro.php',
					'paragraph' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-paragraph.php',
					'translatedparagraph' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-paragraph-translated.php',

					'basequote' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-quote-base.php',
					'pullquote' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-quote-pull.php',
					'blockquote' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-quote-block.php',
					'video' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-video.php',
					'interviewconversation' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-interview-conversation.php',
					'interviewqa' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-interview-qa.php',
					'section' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-section.php',
					'sectionheader' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-header-section.php',
					'subheader' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-header-sub.php',
					'image' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-image.php',
					'headerimage' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-image-header.php',
					'imageduo' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-image-duo.php',
					'caption' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-caption.php',
					'emphasisblock' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-emphasis-block.php',
					'documentblock' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-document-block.php',
					'button' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-button.php',
					'blocklist' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-block-list.php',
					'imagesvg' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-image-svg.php',
					'tag' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-tag.php',

				);
				// Plugin-dependent patterns - check if the plugin exists, add extra pattern clas when available
				if ( class_exists( 'Exchange_Leaflet_Map', false ) ) {
					$classes['basemap'] = EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-map-base.php';
					$classes['simplemap'] = EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-map-simple.php';
				}
			}

			$cn = strtolower( $class );

			if ( isset( $classes[ $cn ] ) ) {
				require_once( $classes[ $cn ] );
			}
		}
	}

	new Exchange_Plugin();
}
