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
			<p><?php esc_html_e( 'Lalala lalali lalalo.', 'progress-planner' ); ?></p>
		</div>
		<?php Onboard::the_form(); ?>
	</div>
</div>
