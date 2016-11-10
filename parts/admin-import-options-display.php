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
	<?php $match_collabs_and_participants = exchange_participant_matcher(); ?>
	<h1>Results go here</h1>
	<?php echo $match_collabs_and_participants; ?>
</div>
