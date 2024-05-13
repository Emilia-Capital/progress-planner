<?php
/**
 * View for the welcome widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

use ProgressPlanner\Settings;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// If the user is already registered, do not show the welcome widget.
if ( Settings::get( 'license_key' ) ) {
	return;
}

?>
<div class="prpl-widget-wrapper prpl-welcome">
	<div class="welcome-header">
		<h1><?php esc_html_e( 'Welcome to the Progress Planner plugin!', 'progress-planner' ); ?></h1>
		<span class="welcome-header-icon">
			<span class="slant"></span>
			<?php
			// phpcs:ignore PEAR.Files.IncludingFile.UseRequire
			include PROGRESS_PLANNER_DIR . '/assets/images/icon_progress_planner.svg';
			?>
		</span>
	</div>
	<div class="welcome-subheader">
		<div>
			<span class="icon dashicons dashicons-chart-line"></span>
			<span><?php esc_html_e( 'make real progress', 'progress-planner' ); ?></span>
		</div>
		<div>
			<span class="icon dashicons dashicons-calendar-alt"></span>
			<span><?php esc_html_e( 'Overcome procrastination', 'progress-planner' ); ?></span>
		</div>
		<div>
			<span class="icon dashicons dashicons-chart-bar"></span>
			<span><?php esc_html_e( 'gain insight', 'progress-planner' ); ?></span>
		</div>
		<div>
			<span class="icon dashicons dashicons-shield-alt"></span>
			<span><?php esc_html_e( 'earn badges', 'progress-planner' ); ?></span>
		</div>
	</div>
	<div class="inner-content">
		<div class="prpl-welcome-content">
			<p><?php esc_html_e( 'Progress Planner helps you overcome procrastination and helps you make real progress with your site! To do so, we would like to send you a weekly email with your site\'s stats. You will get badges to highlight your progress and we will guide you in not just making but also keeping your site healthy.', 'progress-planner' ); ?></p>
		</div>
		<span class="separator"></span>
		<?php Onboard::the_form(); ?>
	</div>
</div>
