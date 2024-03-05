<?php
/**
 * Header for the admin page.
 *
 * @package ProgressPlanner
 */

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$prpl_active_range = isset( $_GET['range'] ) ? sanitize_text_field( wp_unslash( $_GET['range'] ) ) : '-6 months';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$prpl_active_frequency = isset( $_GET['frequency'] ) ? sanitize_text_field( wp_unslash( $_GET['frequency'] ) ) : 'monthly';

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
				'-3 months'  => esc_html__( 'Activity over the past 3 months', 'progress-planner' ),
				'-6 months'  => esc_html__( 'Activity over the past 6 months', 'progress-planner' ),
				'-12 months' => esc_html__( 'Activity over the past 12 months', 'progress-planner' ),
				'-18 months' => esc_html__( 'Activity over the past 18 months', 'progress-planner' ),
				'-24 months' => esc_html__( 'Activity over the past 24 months', 'progress-planner' ),
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
		<label for="prpl-select-frequency" class="screen-reader-text">
			<?php esc_html_e( 'Select frequency:', 'progress-planner' ); ?>
		</label>
		<select id="prpl-select-frequency">
			<?php
			foreach ( [
				'weekly'  => esc_html__( 'Weekly', 'progress-planner' ),
				'monthly' => esc_html__( 'Monthly', 'progress-planner' ),
			] as $prpl_frequency => $prpl_label ) {
				printf(
					'<option value="%1$s" %2$s>%3$s</option>',
					esc_attr( $prpl_frequency ),
					selected( $prpl_active_frequency, $prpl_frequency, false ),
					esc_html( $prpl_label )
				);
			}
			?>
		</select>
	</div>
</div>
