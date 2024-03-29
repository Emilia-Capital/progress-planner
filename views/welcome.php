<?php
/**
 * View for the welcome widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

use ProgressPlanner\Settings;

// If the user is already registered, do not show the welcome widget.
if ( Settings::get( 'registered' ) ) {
	return;
}

$prpl_api_url = \site_url() . '/wp-json/progress-planner/v1/get-stats/token/' . API::get_api_token();

?>
<div class="prpl-widget-wrapper prpl-welcome">
	<h1><?php esc_html_e( 'Welcome to the Progress Planner plugin!', 'progress-planner' ); ?></h1>
	<pre>
	WIP - This is where the subscription form will be.

	The form will contain the following:
		- Email field
		- Name field (optional, placeholder prepopulated from their profile)
		- Consent checkbox to send the data to the remote server
		- Submit button: Send the data and start scanning existing content to calculate the user's activity score as a baseline.

	DEV NOTE: REST-API endpoint to get the data: <a href="<?php echo esc_url( $prpl_api_url ); ?>" target="_blank"><?php echo esc_html( $prpl_api_url ); ?></a>
	</pre>

	<?php Onboard::the_form(); ?>

	<?php include 'admin-page-form-scan.php'; ?>
</div>
