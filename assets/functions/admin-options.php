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
		__( 'Exchange settings', TANDEM_NAME ),
		__( 'Exchange ', TANDEM_NAME ),
		'manage_options',
		TANDEM_NAME,
		'tandem_display_options_page'
	);
}

/**
 * Render the options page for plugin
 *
 * @since  0.1.0
 */
function tandem_display_options_page() {
	include_once TANDEM_PATH . 'parts/admin-display.php';
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
		TANDEM_NAME . '_general',
		__( 'Tandem Exchange theme Settings', TANDEM_NAME ),
		'tandem_settings_general_cb' ,
		TANDEM_NAME
	);

	add_settings_field(
		TANDEM_NAME . '_byline_template_past',
		__( 'Byline template (past)', TANDEM_NAME ),
		'tandem_settings_byline_template_past_cb',
		TANDEM_NAME,
		TANDEM_NAME . '_general',
		array( 'label_for' => TANDEM_NAME . '_byline_template_past' )
	);

	add_settings_field(
		TANDEM_NAME . '_byline_template_present',
		__( 'Byline template (present)', TANDEM_NAME ),
		'tandem_settings_byline_template_present_cb',
		TANDEM_NAME,
		TANDEM_NAME . '_general',
		array( 'label_for' => TANDEM_NAME . '_byline_template_present' )
	);

	register_setting( TANDEM_NAME, TANDEM_NAME . '_byline_template_present', 'tandem_settings_sanitize_byline_template' );
	register_setting( TANDEM_NAME, TANDEM_NAME . '_byline_template_past', 'tandem_settings_sanitize_byline_template' );

}

	/**
 * Render the text for the general section
 *
 * @since  0.1.0
 */
function tandem_settings_general_cb() {
	echo '<p>' . __( 'Display options for the Tandem website', TANDEM_NAME ) . '</p>';
}

/**
 * Render the input field the present tense byline template.
 *
 * @since  0.1.0
 */
function tandem_settings_byline_template_present_cb() {
	$present = get_option( TANDEM_NAME . '_byline_template_present' );
	?>
	<fieldset>
		<label for="<?php echo TANDEM_NAME .'_byline_template_present' ?>">
			<?php _e( 'Byline template for stories told by current participants', TANDEM_NAME ); ?>
		</label>
		<textarea style="display: block;"
			name="<?php echo TANDEM_NAME . '_byline_template_present' ?>"
			id="<?php echo TANDEM_NAME . '_byline_template_present' ?>"
			placeholder="<?php _e( 'Use [[...]]', TANDEM_NAME ); ?>"
			<?php if ( !empty( $present ) ) : ?>
				><?php echo esc_html( $present ); ?></textarea>
			<?php else: ?>
				></textarea>
			<?php endif; ?>
	</fieldset>
<?php

}

/**
 * Render the input field for the past tense byline template.
 *
 * @since  0.1.0
 */
function tandem_settings_byline_template_past_cb() {
	$past = get_option( TANDEM_NAME . '_byline_template_past' );
	?>
	<fieldset>
		<label for="<?php echo TANDEM_NAME .'_byline_template_past' ?>">
			<?php _e( 'Byline template for stories told by alumni', TANDEM_NAME ); ?>
		</label>
		<textarea style="display: block;"
			name="<?php echo TANDEM_NAME . '_byline_template_past' ?>"
			id="<?php echo TANDEM_NAME . '_byline_template_past' ?>"
			placeholder="<?php _e( 'Use [[...]]', TANDEM_NAME ); ?>"
			<?php if ( !empty( $past ) ) : ?>
				value="<?php echo esc_html( $past ) ; ?>">
			<?php else: ?>
				></textarea>
			<?php endif; ?>
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
