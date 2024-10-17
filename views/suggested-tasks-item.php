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
	<div class="actions">
		<button
			type="button"
			class="button prpl-suggested-task-button"
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
			class="button prpl-suggested-task-button"
			data-task-id="{taskId}"
			data-task-title="{taskTitle}"
			data-action="snooze"
			title="<?php \esc_html_e( 'Snooze', 'progress-planner' ); ?>"
		>
			<span class="dashicons dashicons-clock"></span>
			<span class="screen-reader-text"><?php \esc_html_e( 'Snooze', 'progress-planner' ); ?></span>
		</button>
	</div>
	<div class="prpl-suggested-snooze-duration-selector hidden">
		<select class="prpl-suggested-snooze-duration">
			<option value=""><?php \esc_html_e( 'Select duration', 'progress-planner' ); ?></option>
			<option value="1-week"><?php \esc_html_e( '1 week', 'progress-planner' ); ?></option>
			<option value="1-month"><?php \esc_html_e( '1 month', 'progress-planner' ); ?></option>
			<option value="3-months"><?php \esc_html_e( '3 months', 'progress-planner' ); ?></option>
			<option value="6-months"><?php \esc_html_e( '6 months', 'progress-planner' ); ?></option>
			<option value="1-year"><?php \esc_html_e( '1 year', 'progress-planner' ); ?></option>
			<option value="forever"><?php \esc_html_e( 'forever', 'progress-planner' ); ?></option>
		</select>
	</div>
	<div class="prpl-suggested-task-info hidden">
		{taskDescription}
		<button type="button" class="button prpl-suggested-task-button close" data-action="close-info">
			<span class="dashicons dashicons-no-alt"></span>
			<span class="screen-reader-text"><?php \esc_html_e( 'Close', 'progress-planner' ); ?></span>
		</button>
	</div>
</li>
