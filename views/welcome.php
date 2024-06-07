<?php
/**
 * View for the welcome widget.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner;

use Progress_Planner\Settings;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<?php if ( ! \get_option( 'progress_planner_license_key' ) ) : ?>
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
				<p><?php esc_html_e( 'Progress Planner helps you to overcome procrastination. Let\'s get those website maintenance tasks done! We would like to send you weekly emails with your site stats. Next to that, we\'ll give you tips and keep you motivated to continue the work on your website. Let\'s go!', 'progress-planner' ); ?></p>
			</div>
			<span class="separator"></span>
			<?php Onboard::the_form(); ?>
		</div>
	</div>
<?php endif; ?>

<?php if ( isset( $_GET['content-scan'] ) ) : // phpcs:ignore WordPress.Security ?>
	<div class="prpl-widget-wrapper prpl-welcome">
		<button class="button button-primary" id="prpl-scan-button">
			<?php esc_html_e( 'Reset content stats and re-scan content.', 'progress-planner' ); ?>
		</button>
		<div id="progress-planner-scan-progress" style="display:none;padding:20px;">
			<progress value="0" max="100"></progress>
		</div>
	</div>
<?php endif; ?>
