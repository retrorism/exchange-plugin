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

add_action( 'plugins_loaded','tandem_require_functions' );

/**
 * Require our function files.
 */
function tandem_require_functions() {
	$files = array(
		'globals.php',
		'admin.php',
		'admin-acf.php',
		'admin-options.php',
		'post-types.php',
		'public.php',
		'taxonomies.php',
	);
	foreach ( $files as $file ) {
		require_once( EXCHANGE_PLUGIN_PATH . 'assets/functions/' . $file );
	}
}

/* Runs on plugin is activated */
register_activation_hook( EXCHANGE_PLUGIN_FILE, 'tandem_activate' );

/* Runs on plugin deactivation */
register_deactivation_hook( EXCHANGE_PLUGIN_FILE, 'tandem_deactivate' );

/**
 * Runs on activation of the plugin.
 **/
function tandem_activate() {
	return;
}

/**
 * Runs on plugin deactivation.
 **/
function tandem_deactivate() {
	return;
}

/**
 * Auto-load our class files.
 *
 * @param  string $class Class name.
 * @return void
 */
function tandem_auto_load( $class ) {
	static $classes = null;

	if ( null === $classes ) {
		$classes = array(
			'exchange'  => EXCHANGE_PLUGIN_PATH . 'assets/classes/class-exchange-base.php',
			'story'  => EXCHANGE_PLUGIN_PATH . 'assets/classes/class-story.php',
			'collaboration'  => EXCHANGE_PLUGIN_PATH . 'assets/classes/class-collaboration.php',
			'programmeround'  => EXCHANGE_PLUGIN_PATH . 'assets/classes/class-programme-round.php',
			'participant'  => EXCHANGE_PLUGIN_PATH . 'assets/classes/class-participant.php',

			'basepattern' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-pattern-base.php',
			'basegrid' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-grid-base.php',
			'relatedgrid' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-grid-related.php',
			'griditem' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-grid-item.php',

			'baseinterview' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-interview-base.php',
			'byline' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-byline.php',
			'editorialintro' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-editorial-intro.php',
			'paragraph' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-paragraph.php',
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
			'button' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-button.php',
			'blocklist' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-block-list.php',
			'imagesvg' => EXCHANGE_PLUGIN_PATH . 'assets/classes/patterns/class-image-svg.php',

			'basecontroller'  => EXCHANGE_PLUGIN_PATH . 'assets/classes/controllers/class-controller-base.php',
			'storycontroller'  => EXCHANGE_PLUGIN_PATH . 'assets/classes/controllers/class-controller-story.php',
			'participantcontroller'  => EXCHANGE_PLUGIN_PATH . 'assets/classes/controllers/class-controller-participant.php',
			'collaborationcontroller'  => EXCHANGE_PLUGIN_PATH . 'assets/classes/controllers/class-controller-collaboration.php',
			'programmeroundcontroller'  => EXCHANGE_PLUGIN_PATH . 'assets/classes/controllers/class-controller-programme-round.php',

		);
	}

	$cn = strtolower( $class );

	if ( isset( $classes[ $cn ] ) ) {
		require_once( $classes[ $cn ] );
	}
}

if ( function_exists( 'tandem_auto_load' ) ) {
	spl_autoload_register( 'tandem_auto_load' );
}
