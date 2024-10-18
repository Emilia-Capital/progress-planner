<?php
/**
 * View for the welcome widget.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( false === \get_option( 'progress_planner_license_key', false ) ) {
	return;
}

// Enqueue welcome styles.
\wp_enqueue_style(
	'progress-planner-welcome',
	PROGRESS_PLANNER_URL . '/assets/css/welcome.css',
	[],
	filemtime( PROGRESS_PLANNER_DIR . '/assets/css/welcome.css' )
);

?>
<div class="prpl-widget-wrapper prpl-welcome" popover="manual">
	<div class="welcome-header">
		<h1><?php \esc_html_e( 'Welcome to the Progress Planner plugin!', 'progress-planner' ); ?></h1>
		<span class="welcome-header-icon">
			<span class="slant"></span>
			<?php include PROGRESS_PLANNER_DIR . '/assets/images/icon_progress_planner.svg'; // phpcs:ignore PEAR.Files.IncludingFile.UseRequire ?>
		</span>
	</div>
	<div class="inner-content">
		<div class="left">
			<?php Onboard::the_form(); ?>
		</div>
		<div class="right">
			<img src="<?php echo \esc_url( PROGRESS_PLANNER_URL . '/assets/images/image_onboaring_block.png' ); ?>" alt="" class="onboarding" />
		</div>
	</div>
</div>
<script>document.querySelector( '.prpl-widget-wrapper.prpl-welcome' ).showPopover();</script>
