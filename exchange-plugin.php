<?php
/**
 * Plugin for Tandem Exchange WordPress theme
 *
 * @link    http//github.com/retrorism/exchange
 * @package Exchange Plugin
 * @version 0.1.0
 *
 * Plugin Name: Tandem
 * Plugin URI:  http://www.badjo.nl/plugins/exchange-plugin/
 * Description: This plugin adds all necessary functionality for the Exchange Theme
 * Author:      Bart Bloemers
 * Author:      Willem Prins
 * Version:     0.1.0
 * Author URI:  http://www.badjo.nl/
 **/

if ( ! function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

if ( ! defined( 'TANDEM_NAME' ) ) {
	define( 'TANDEM_NAME', 'exchange-plugin' );
}

if ( ! defined( 'TANDEM_FILE' ) ) {
	define( 'TANDEM_FILE', __FILE__ );
}

if ( ! defined( 'TANDEM_PATH' ) ) {
	define( 'TANDEM_PATH', plugin_dir_path( TANDEM_FILE ) );
}

add_action( 'plugins_loaded','tandem_require_functions' );

/**
 * Require our function files.
 */
function tandem_require_functions() {
	$files = array(
		'admin.php',
		'admin-acf.php',
		'admin-options.php',
		'globals.php',
		'post-types.php',
		'public.php',
		'taxonomies.php',
	);
	foreach ( $files as $file ) {
		require_once( TANDEM_PATH . 'assets/functions/' . $file );
	}
}

/* Runs on plugin is activated */
register_activation_hook( TANDEM_FILE, 'tandem_activate' );

/* Runs on plugin deactivation */
register_deactivation_hook( TANDEM_FILE, 'tandem_deactivate' );

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
			'participant'  => TANDEM_PATH . 'assets/classes/class-participant.php',
			'story'  => TANDEM_PATH . 'assets/classes/class-story.php',
			'basepattern' => TANDEM_PATH . 'assets/classes/patterns/class-pattern-base.php',
			'paragraph' => TANDEM_PATH . 'assets/classes/patterns/class-paragraph.php',
			'pullquote' => TANDEM_PATH . 'assets/classes/patterns/class-quote-pull.php',
			'blockquote' => TANDEM_PATH . 'assets/classes/patterns/class-quote-block.php',
			'section' => TANDEM_PATH . 'assets/classes/patterns/class-section.php',
			'sectionheader' => TANDEM_PATH . 'assets/classes/patterns/class-header-section.php',
			'subheader' => TANDEM_PATH . 'assets/classes/patterns/class-header-sub.php',
			'image' => TANDEM_PATH . 'assets/classes/patterns/class-image.php',
			'imageduo' => TANDEM_PATH . 'assets/classes/patterns/class-image-duo.php',
			'caption' => TANDEM_PATH . 'assets/classes/patterns/class-caption.php',
			'basecontroller'  => TANDEM_PATH . 'assets/classes/controllers/class-controller-base.php',
			'storycontroller'  => TANDEM_PATH . 'assets/classes/controllers/class-controller-story.php',
			'participantcontroller'  => TANDEM_PATH . 'assets/classes/controllers/class-controller-participant.php',
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
