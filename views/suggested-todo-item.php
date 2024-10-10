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
			title="<?php \esc_html_e( 'Snooze for a week', 'progress-planner' ); ?>"
		>
			<span class="dashicons dashicons-clock"></span>
			<span class="screen-reader-text"><?php \esc_html_e( 'Snooze for a week', 'progress-planner' ); ?></span>
		</button>
	</div>
	<div class="prpl-suggested-task-info hidden">
		{taskDescription}
		<button type="button" class="button prpl-suggested-task-button close" data-action="close-info">
			<span class="dashicons dashicons-no-alt"></span>
			<span class="screen-reader-text"><?php \esc_html_e( 'Close', 'progress-planner' ); ?></span>
		</button>
	</div>
</li>
