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
<?php if ( false === \get_option( 'progress_planner_license_key', false ) ) : ?>
	<div class="prpl-widget-wrapper prpl-welcome" popover="manual">
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
		<div class="inner-content">
			<?php Onboard::the_form(); ?>
		</div>
	</div>
	<script>document.querySelector( '.prpl-widget-wrapper.prpl-welcome' ).showPopover();</script>
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
