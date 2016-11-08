<?php

/**
 * Provide a admin area view for the Tandem Exchange plugin
 *
 * This file is used to markup the import-functions.
 *
 * @link       http://www.somtijds.nl
 * @since      0.1.0
 *
 * @package    Exchange Plugin
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">
	<?php $collabs_and_tags = exchange_importer(); ?>
	<h1>Results go here</h1>
	<?php var_dump( $collabs_and_tags ); ?>
</div>
