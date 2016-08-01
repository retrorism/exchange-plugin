<?php
/**
 * Options Page creation for the Exchange plugin
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

function exchange_helper_plugins() {
	return array(
		'exchange-leaflet-map' => 'Exchange_Leaflet_Map',
		'exchange-flickr-images' => 'Exchange_Flickr_Images',
		'exchange-svg-support' => 'Exchange_SVG_Support',
	);
}

function exchange_add_options_page() {
	add_menu_page(
		__( 'Exchange settings', EXCHANGE_PLUGIN ),
		__( 'Exchange ', EXCHANGE_PLUGIN ),
		'manage_options',
		EXCHANGE_PLUGIN,
		'exchange_display_options_page'
	);
	$helper_plugins = exchange_helper_plugins();
	foreach( $helper_plugins as $helper_plugin => $helper_plugin_class ) {
		if ( ! class_exists( $helper_plugin_class ) ) {
			continue;
		}
		if ( method_exists( $helper_plugin, 'get_instance' ) ) {
			$plugin_object = $helpder_plugin::get_instance();
		} else {
			$plugin_object = new $helper_plugin_class;
		}
		if ( method_exists( $plugin_object, 'admin_init' ) ) {
			$plugin_object->admin_init();
		}
		if ( method_exists( $plugin_object, 'admin_menu' ) ) {
			$plugin_object->admin_menu();
		}
	}
}

/**
 * Render the options page for plugin
 *
 * @since  0.1.0
 */
function exchange_display_options_page() {
	include_once EXCHANGE_PLUGIN_PATH . 'parts/admin-display.php';
}

/**
 * Register all related settings for the plugin
 *
 * @since     0.1.0
 * @return    boolean    The version number of the plugin.
 */
function exchange_register_settings() {

	// Add a General section
	add_settings_section(
		EXCHANGE_PLUGIN . '_general',
		__( 'Tandem Exchange theme Settings', EXCHANGE_PLUGIN ),
		'exchange_settings_general_cb' ,
		EXCHANGE_PLUGIN
	);

	add_settings_field(
		EXCHANGE_PLUGIN . '_byline_template_past',
		__( 'Byline template (past)', EXCHANGE_PLUGIN ),
		'exchange_settings_byline_template_past_cb',
		EXCHANGE_PLUGIN,
		EXCHANGE_PLUGIN . '_general',
		array( 'label_for' => EXCHANGE_PLUGIN . '_byline_template_past' )
	);

	add_settings_field(
		EXCHANGE_PLUGIN . '_byline_template_present',
		__( 'Byline template (present)', EXCHANGE_PLUGIN ),
		'exchange_settings_byline_template_present_cb',
		EXCHANGE_PLUGIN,
		EXCHANGE_PLUGIN . '_general',
		array( 'label_for' => EXCHANGE_PLUGIN . '_byline_template_present' )
	);

	register_setting( EXCHANGE_PLUGIN, EXCHANGE_PLUGIN . '_byline_template_present', 'exchange_settings_sanitize_byline_template' );
	register_setting( EXCHANGE_PLUGIN, EXCHANGE_PLUGIN . '_byline_template_past', 'exchange_settings_sanitize_byline_template' );

}

	/**
 * Render the text for the general section
 *
 * @since  0.1.0
 */
function exchange_settings_general_cb() {
	echo '<p>' . __( 'Display options for the Tandem website', EXCHANGE_PLUGIN ) . '</p>';
}

/**
 * Render the input field the present tense byline template.
 *
 * @since  0.1.0
 */
function exchange_settings_byline_template_present_cb() {
	$present = get_option( EXCHANGE_PLUGIN . '_byline_template_present' );
	?>
	<fieldset>
		<label for="<?php echo EXCHANGE_PLUGIN .'_byline_template_present' ?>">
			<?php _e( 'Byline template for stories told by current participants. Use [[storyteller]], [[programme_round]] and [[collaboration]] as placeholders for the byline specifics.', EXCHANGE_PLUGIN ); ?>
		</label>
		<textarea style="display: block; width: 100%; min-height: 4em;"
			name="<?php echo EXCHANGE_PLUGIN . '_byline_template_present' ?>"
			id="<?php echo EXCHANGE_PLUGIN . '_byline_template_present' ?>"
			placeholder="<?php _e( 'Use [[...]]', EXCHANGE_PLUGIN ); ?>"><?php if ( !empty( $present ) ) {
																				echo esc_textarea( $present ) . '</textarea>';
																			} else {
																				echo '</textarea>';
																			} ?>
	</fieldset>
<?php

}

/**
 * Render the input field for the past tense byline template.
 *
 * @since  0.1.0
 */
function exchange_settings_byline_template_past_cb() {
	$past = get_option( EXCHANGE_PLUGIN . '_byline_template_past' );
	?>
	<fieldset>
		<label for="<?php echo EXCHANGE_PLUGIN .'_byline_template_past' ?>">
			<?php _e( 'Byline template for stories told by alumni. Use [[storyteller]], [[programme_round]] and [[collaboration]] as placeholders for the byline specifics.', EXCHANGE_PLUGIN ); ?>
		</label>
		<textarea style="display: block; width: 100%; min-height: 4em;"
			name="<?php echo EXCHANGE_PLUGIN . '_byline_template_past' ?>"
			id="<?php echo EXCHANGE_PLUGIN . '_byline_template_past' ?>"
			placeholder="<?php _e( 'Use [[...]]', EXCHANGE_PLUGIN ); ?>"><?php if ( !empty( $past ) ) {
																				echo esc_textarea( $past ) . '</textarea>';
																			} else {
																				echo '</textarea>';
																			} ?>
	</fieldset>
<?php
}

/**
 * Sanitize the url value before being saved to database
 *
 * @param  string $byline_template $_POST value
 * @since  0.1.0
 * @return string Sanitized value
 */
function exchange_settings_sanitize_byline_template( $byline_template ) {
	return $byline_template;
}
