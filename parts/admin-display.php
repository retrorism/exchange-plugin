<?php

/**
 * Provide a admin area view for the Tandem Exchange plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://www.somtijds.nl
 * @since      0.1.0
 *
 * @package    Exchange Plugin
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">
	    <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	    <form action="options.php" method="post">
	        <?php
	            settings_fields( TANDEM_NAME );
	            do_settings_sections( TANDEM_NAME );
	            submit_button();
	        ?>
	    </form>
	</div>
