<?php
/**
 * Template for suggested-todo item.
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
	<div class="tooltip-actions">
		<button
			type="button"
			class="prpl-suggested-task-button"
			data-task-id="{taskId}"
			data-task-title="{taskTitle}"
			data-action="info"
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
			title="<?php \esc_html_e( 'Snooze', 'progress-planner' ); ?>"
		>
			<span class="dashicons dashicons-clock"></span>
			<span class="screen-reader-text"><?php \esc_html_e( 'Snooze', 'progress-planner' ); ?></span>
		</button>

		<div class="prpl-suggested-snooze-duration-selector prpl-tooltip">

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
					<label>
						<input type="radio" name="snooze-duration-{taskId}" value="1-week">
						<?php esc_html_e( '1 week', 'progress-planner' ); ?>
					</label>
					<label>
						<input type="radio" name="snooze-duration-{taskId}" value="1-month">
						<?php esc_html_e( '1 month', 'progress-planner' ); ?>
					</label>
					<label>
						<input type="radio" name="snooze-duration-{taskId}" value="3-months">
						<?php esc_html_e( '3 months', 'progress-planner' ); ?>
					</label>
					<label>
						<input type="radio" name="snooze-duration-{taskId}" value="6-months">
						<?php esc_html_e( '6 months', 'progress-planner' ); ?>
					</label>
					<label>
						<input type="radio" name="snooze-duration-{taskId}" value="1-year">
						<?php esc_html_e( '1 year', 'progress-planner' ); ?>
					</label>
					<label>
						<input type="radio" name="snooze-duration-{taskId}" value="forever">
						<?php esc_html_e( 'forever', 'progress-planner' ); ?>
					</label>
				</div>
			</fieldset>

			<button type="button" class="prpl-suggested-task-button close" data-action="close-snooze">
				<span class="dashicons dashicons-no-alt"></span>
				<span class="screen-reader-text"><?php \esc_html_e( 'Close', 'progress-planner' ); ?></span>
			</button>
		</div>
		<div class="prpl-suggested-task-info prpl-tooltip">
			{taskDescription}
			<button type="button" class="prpl-suggested-task-button close" data-action="close-info">
				<span class="dashicons dashicons-no-alt"></span>
				<span class="screen-reader-text"><?php \esc_html_e( 'Close', 'progress-planner' ); ?></span>
			</button>
		</div>
	</div>
</li>
