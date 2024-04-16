<?php
/**
 * View for the welcome widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

use ProgressPlanner\Settings;

// If the user is already registered, do not show the welcome widget.
if ( Settings::get( 'license_key' ) ) {
	return;
}

?>
<div class="prpl-widget-wrapper prpl-welcome">
	<h1><?php esc_html_e( 'Welcome to the Progress Planner plugin!', 'progress-planner' ); ?></h1>
	<div class="inner-grid">
		<div class="prpl-welcome__content">
			<p><?php esc_html_e( 'Progress Planner helps you keep track of your progress, and fight procrastination. To help you do that, we will be sending a weekly email to an address of your choice, with things you can do to improve your website.', 'progress-planner' ); ?></p>
			<p><?php esc_html_e( 'When you submit the form, your site will be registered on our server along with your email address, so we can properly send you our weekly emails.', 'progress-planner' ); ?></p>
			<p><?php esc_html_e( 'After submitting the form, we will automatically scan your existing content to generate a baseline for your stats.', 'progress-planner' ); ?></p>
		</div>
		<?php Onboard::the_form(); ?>
	</div>
</div>
