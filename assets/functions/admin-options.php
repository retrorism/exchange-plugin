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


function tandem_add_options_page() {
	add_theme_page(
		__( 'Exchange settings', EXCHANGE_PLUGIN ),
		__( 'Exchange ', EXCHANGE_PLUGIN ),
		'manage_options',
		EXCHANGE_PLUGIN,
		'tandem_display_options_page'
	);
}

/**
 * Render the options page for plugin
 *
 * @since  0.1.0
 */
function tandem_display_options_page() {
	include_once EXCHANGE_PLUGIN_PATH . 'parts/admin-display.php';
}

/**
 * Register all related settings for the plugin
 *
 * @since     0.1.0
 * @return    boolean    The version number of the plugin.
 */
function tandem_register_settings() {

	// Add a General section
	add_settings_section(
		EXCHANGE_PLUGIN . '_general',
		__( 'Tandem Exchange theme Settings', EXCHANGE_PLUGIN ),
		'tandem_settings_general_cb' ,
		EXCHANGE_PLUGIN
	);

	add_settings_field(
		EXCHANGE_PLUGIN . '_byline_template_past',
		__( 'Byline template (past)', EXCHANGE_PLUGIN ),
		'tandem_settings_byline_template_past_cb',
		EXCHANGE_PLUGIN,
		EXCHANGE_PLUGIN . '_general',
		array( 'label_for' => EXCHANGE_PLUGIN . '_byline_template_past' )
	);

	add_settings_field(
		EXCHANGE_PLUGIN . '_byline_template_present',
		__( 'Byline template (present)', EXCHANGE_PLUGIN ),
		'tandem_settings_byline_template_present_cb',
		EXCHANGE_PLUGIN,
		EXCHANGE_PLUGIN . '_general',
		array( 'label_for' => EXCHANGE_PLUGIN . '_byline_template_present' )
	);

	register_setting( EXCHANGE_PLUGIN, EXCHANGE_PLUGIN . '_byline_template_present', 'tandem_settings_sanitize_byline_template' );
	register_setting( EXCHANGE_PLUGIN, EXCHANGE_PLUGIN . '_byline_template_past', 'tandem_settings_sanitize_byline_template' );

}

	/**
 * Render the text for the general section
 *
 * @since  0.1.0
 */
function tandem_settings_general_cb() {
	echo '<p>' . __( 'Display options for the Tandem website', EXCHANGE_PLUGIN ) . '</p>';
}

/**
 * Render the input field the present tense byline template.
 *
 * @since  0.1.0
 */
function tandem_settings_byline_template_present_cb() {
	$present = get_option( EXCHANGE_PLUGIN . '_byline_template_present' );
	?>
	<fieldset>
		<label for="<?php echo EXCHANGE_PLUGIN .'_byline_template_present' ?>">
			<?php _e( 'Byline template for stories told by current participants. Use [[storyteller]], [[programme_round]] and [[collaboration]] as placeholders for the byline specifics.', EXCHANGE_PLUGIN ); ?>
		</label>
		<textarea style="display: block;"
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
function tandem_settings_byline_template_past_cb() {
	$past = get_option( EXCHANGE_PLUGIN . '_byline_template_past' );
	?>
	<fieldset>
		<label for="<?php echo EXCHANGE_PLUGIN .'_byline_template_past' ?>">
			<?php _e( 'Byline template for stories told by alumni. Use [[storyteller]], [[programme_round]] and [[collaboration]] as placeholders for the byline specifics.', EXCHANGE_PLUGIN ); ?>
		</label>
		<textarea style="display: block;"
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
function tandem_settings_sanitize_byline_template( $byline_template ) {
	return $byline_template;
}
