<?php
/**
 * Template for suggested-task item.
 *
 * @package Progress_Planner
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<li id="prpl-suggested-task-template" class="prpl-suggested-task" data-task-id="{taskId}">
	<h3>{taskTitle}</h3>
	<div class="prpl-suggested-task-actions">
		<div class="tooltip-actions">
			<button
				type="button"
				class="prpl-suggested-task-button"
				data-task-id="{taskId}"
				data-task-title="{taskTitle}"
				data-action="info"
				data-target="info"
				title="<?php \esc_html_e( 'Info', 'progress-planner' ); ?>"
			>
				<span class="dashicons dashicons-info"></span>
				<span class="screen-reader-text"><?php \esc_html_e( 'Info', 'progress-planner' ); ?></span>
			</button>
			<button
				type="button"
				class="prpl-suggested-task-button"
				data-task-id="{taskId}"
				data-task-title="{taskTitle}"
				data-action="snooze"
				data-target="snooze"
				title="<?php \esc_html_e( 'Snooze', 'progress-planner' ); ?>"
			>
				<span class="dashicons dashicons-clock"></span>
				<span class="screen-reader-text"><?php \esc_html_e( 'Snooze', 'progress-planner' ); ?></span>
			</button>

			<div class="prpl-suggested-task-snooze prpl-tooltip">

				<fieldset>
					<legend>
						<span>
							<?php esc_html_e( 'Snooze this task?', 'progress-planner' ); ?>
						</span>
						<button type="button" class="prpl-toggle-radio-group">
							<span class="prpl-toggle-radio-group-text">
								<?php esc_html_e( 'How long?', 'progress-planner' ); ?>
							</span>
							<span class="prpl-toggle-radio-group-arrow">
								&rsaquo;
							</span>
						</button>
					</legend>

					<div class="prpl-snooze-duration-radio-group">
						<?php
						foreach ( [
							'1-week'   => __( '1 week', 'progress-planner' ),
							'1-month'  => __( '1 month', 'progress-planner' ),
							'3-months' => __( '3 months', 'progress-planner' ),
							'6-months' => __( '6 months', 'progress-planner' ),
							'1-year'   => __( '1 year', 'progress-planner' ),
							'forever'  => __( 'forever', 'progress-planner' ),
						] as $prpl_snooze_duration_options_value => $prpl_snooze_duration_options_label ) :
							?>
							<label>
								<input type="radio" name="snooze-duration-{taskId}" value="<?php echo \esc_attr( $prpl_snooze_duration_options_value ); ?>">
								<?php echo \esc_html( $prpl_snooze_duration_options_label ); ?>
							</label>
						<?php endforeach; ?>
					</div>
				</fieldset>

				<button type="button" class="prpl-suggested-task-button prpl-tooltip-close" data-action="close-snooze" data-target="snooze">
					<span class="dashicons dashicons-no-alt"></span>
					<span class="screen-reader-text"><?php \esc_html_e( 'Close', 'progress-planner' ); ?></span>
				</button>
			</div>
			<div class="prpl-suggested-task-info prpl-tooltip" data-target="info">
				{taskDescription}
				<button type="button" class="prpl-suggested-task-button prpl-tooltip-close" data-action="close-info" data-target="info">
					<span class="dashicons dashicons-no-alt"></span>
					<span class="screen-reader-text"><?php \esc_html_e( 'Close', 'progress-planner' ); ?></span>
				</button>
			</div>
		</div>
		<span class="prpl-suggested-task-points">
			+{taskPoints}
		</span>
	</div>
</li>
