<?php
/**
 * Header for the admin page.
 *
 * @package Progress_Planner
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$progress_planner_active_range = isset( $_GET['range'] ) ? \sanitize_text_field( \wp_unslash( $_GET['range'] ) ) : '-6 months';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$progress_planner_active_frequency = isset( $_GET['frequency'] ) ? \sanitize_text_field( \wp_unslash( $_GET['frequency'] ) ) : 'monthly';

do_action( 'progress_planner_admin_page_header_before' );
?>
<?php if ( isset( $_GET['content-scan-finished'] ) ) : // phpcs:ignore WordPress.Security ?>
	<div class="prpl-widget-wrapper prpl-top-notice" id="prpl-content-scan-finished-notice">
		<button class="prpl-close-button" onclick="document.getElementById('prpl-content-scan-finished-notice').remove();">
			<span class="dashicons dashicons-no-alt"></span>
			<span class="screen-reader-text"><?php esc_html_e( 'Close notice', 'progress-planner' ); ?></span>
		</button>
	</div>
<?php endif; ?>

<div class="prpl-header">
	<div class="prpl-header-logo">
		<?php
		// phpcs:ignore PEAR.Files.IncludingFile.UseRequire
		include PROGRESS_PLANNER_DIR . '/assets/images/logo_progress_planner.svg';
		?>
	</div>

	<div class="prpl-header-right">
		<button class="prpl-info-icon" id="prpl-start-tour-icon-button" onclick="prplStartTour()">
			<span class="dashicons dashicons-lightbulb"></span>
			<span class="screen-reader-text"><?php \esc_html_e( 'Start tour', 'progress-planner' ); ?>
		</button>
		<?php new \Progress_Planner\Popups\Settings(); ?>
		<div class="prpl-header-select-range">
			<label for="prpl-select-range" class="screen-reader-text">
				<?php \esc_html_e( 'Select range:', 'progress-planner' ); ?>
			</label>
			<select id="prpl-select-range">
				<?php
				foreach ( [
					'-3 months'  => \esc_html__( 'Activity over the past 3 months', 'progress-planner' ),
					'-6 months'  => \esc_html__( 'Activity over the past 6 months', 'progress-planner' ),
					'-12 months' => \esc_html__( 'Activity over the past 12 months', 'progress-planner' ),
					'-18 months' => \esc_html__( 'Activity over the past 18 months', 'progress-planner' ),
					'-24 months' => \esc_html__( 'Activity over the past 24 months', 'progress-planner' ),
				] as $progress_planner_range => $progress_planner_label ) {
					printf(
						'<option value="%1$s" %2$s>%3$s</option>',
						\esc_attr( $progress_planner_range ),
						\selected( $progress_planner_active_range, $progress_planner_range, false ),
						\esc_html( $progress_planner_label )
					);
				}
				?>
			</select>
			<label for="prpl-select-frequency" class="screen-reader-text">
				<?php \esc_html_e( 'Select frequency:', 'progress-planner' ); ?>
			</label>
			<select id="prpl-select-frequency">
				<?php
				foreach ( [
					'weekly'  => \esc_html__( 'Weekly', 'progress-planner' ),
					'monthly' => \esc_html__( 'Monthly', 'progress-planner' ),
				] as $progress_planner_frequency => $progress_planner_label ) {
					printf(
						'<option value="%1$s" %2$s>%3$s</option>',
						\esc_attr( $progress_planner_frequency ),
						\selected( $progress_planner_active_frequency, $progress_planner_frequency, false ),
						\esc_html( $progress_planner_label )
					);
				}
				?>
			</select>
		</div>
	</div>
</div>
