<?php
/**
 * Progress_Planner widget.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Widgets;

use Progress_Planner\Suggested_Tasks as Root_Suggested_Tasks;

/**
 * Published Content Widget.
 */
final class Suggested_Tasks extends Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'suggested-todo';

	/**
	 * Render the widget content.
	 *
	 * @return void
	 */
	protected function the_content() {
		?>
		<h2 class="prpl-widget-title">
			<?php esc_html_e( 'Suggested tasks', 'progress-planner' ); ?>
		</h2>

		<ul style="display:none">
			<li id="prpl-suggested-task-template">
				<h3>{taskTitle}</h3>
				<p class="prpl-suggested-task-description">{taskDescription}</p>
				<div class="actions">
					<button
						type="button"
						class="button prpl-suggested-task-button"
						data-task-id="{taskId}"
						data-task-title="{taskTitle}"
						data-action="add-todo"
						title="<?php esc_attr_e( 'Add to todo list', 'progress-planner' ); ?>"
					>
						<span class="dashicons dashicons-list-view"></span>
						<span class="screen-reader-text"><?php esc_html_e( 'Add to todo list', 'progress-planner' ); ?></span>
					</button>
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
			</li>
		</ul>

		<ul class="prpl-suggested-todos-list priority-high"></ul>
		<ul class="prpl-suggested-todos-list priority-medium"></ul>
		<ul class="prpl-suggested-todos-list priority-low"></ul>
		<?php
	}
}
