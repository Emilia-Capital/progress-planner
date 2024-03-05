<?php
/**
 * Header for the admin page.
 *
 * @package ProgressPlanner
 */

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$prpl_active_range = isset( $_GET['range'] ) ? sanitize_text_field( wp_unslash( $_GET['range'] ) ) : '-6 months';

?>
<div class="logo" style="margin-bottom: 2em; display:flex; justify-content: space-between; align-items: center;">
	<div class="prpl-header-logo">
		<img
			src="<?php echo esc_url( PROGRESS_PLANNER_URL . 'assets/images/logo.png' ); ?>"
			alt="<?php esc_attr_e( 'Progress Planner', 'progress-planner' ); ?>"
			style="max-width: 200px;"
		/>
	</div>

	<div class="prpl-header-select-range">
		<label for="prpl-select-range" class="screen-reader-text">
			<?php esc_html_e( 'Select range:', 'progress-planner' ); ?>
		</label>
		<select id="prpl-select-range">
			<?php
			foreach ( [
				'-6 months'  => esc_html__( 'Activity over the past 6 months', 'progress-planner' ),
				'-12 months' => esc_html__( 'Activity over the past 12 months', 'progress-planner' ),
			] as $prpl_range => $prpl_label ) {
				printf(
					'<option value="%1$s" %2$s>%3$s</option>',
					esc_attr( $prpl_range ),
					selected( $prpl_active_range, $prpl_range, false ),
					esc_html( $prpl_label )
				);
			}
			?>
		</select>
	</div>
</div>
