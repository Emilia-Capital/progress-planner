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

<li id="prpl-suggested-task-template">
	<h3>{taskTitle}</h3>
	<div class="actions">
		<button
			type="button"
			class="button prpl-suggested-task-button"
			data-task-id="{taskId}"
			data-task-title="{taskTitle}"
			data-action="dismiss"
			title="<?php esc_html_e( 'Dismiss', 'progress-planner' ); ?>"
		>
			<span class="dashicons dashicons-no"></span>
			<span class="screen-reader-text"><?php esc_html_e( 'Dismiss', 'progress-planner' ); ?></span>
		</button>
		<button
			type="button"
			class="button prpl-suggested-task-button"
			data-task-id="{taskId}"
			data-task-title="{taskTitle}"
			data-action="complete"
			title="<?php esc_html_e( 'Mark as complete', 'progress-planner' ); ?>"
		>
			<span class="dashicons dashicons-yes"></span>
			<span class="screen-reader-text"><?php esc_html_e( 'Mark as complete', 'progress-planner' ); ?></span>
		</button>
		<button
			type="button"
			class="button prpl-suggested-task-button"
			data-task-id="{taskId}"
			data-task-title="{taskTitle}"
			data-action="snooze"
			title="<?php esc_html_e( 'Snooze for a week', 'progress-planner' ); ?>"
		>
			<span class="dashicons dashicons-clock"></span>
			<span class="screen-reader-text"><?php esc_html_e( 'Snooze for a week', 'progress-planner' ); ?></span>
		</button>
	</div>
	<p class="prpl-suggested-task-description" style="display:none;">{taskDescription}</p>
</li>
