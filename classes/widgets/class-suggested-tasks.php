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
				<details>
					<summary>{taskTitle}</summary>
					<p class="prpl-suggested-task-description">{taskDescription}</p>
					<p class="prpl-suggested-task-priority">
						<?php
						printf(
							/* translators: %s: priority */
							esc_html__( 'Priority: %s', 'progress-planner' ),
							'{taskPriority}'
						);
						?>
					</p>
					<button
						type="button"
						class="button prpl-suggested-task-button"
						data-task-id="{taskId}"
						data-task-title="{taskTitle}"
						data-action="add-todo"
					>
						<?php esc_html_e( 'Add to my to-do list', 'progress-planner' ); ?>
					</button>
					<button
						type="button"
						class="button prpl-suggested-task-button"
						data-task-id="{taskId}"
						data-task-title="{taskTitle}"
						data-action="dismiss"
					>
						<?php esc_html_e( 'Dismiss', 'progress-planner' ); ?>
					</button>
					<button
						type="button"
						class="button prpl-suggested-task-button"
						data-task-id="{taskId}"
						data-task-title="{taskTitle}"
						data-action="complete"
					>
						<?php esc_html_e( 'Mark as complete', 'progress-planner' ); ?>
					</button>
					<button
						type="button"
						class="button prpl-suggested-task-button"
						data-task-id="{taskId}"
						data-task-title="{taskTitle}"
						data-action="snooze"
					>
						<?php esc_html_e( 'Snooze for a week', 'progress-planner' ); ?>
					</button>
				</details>
			</li>
		</ul>

		<ul class="prpl-suggested-todos-list priority-high"></ul>
		<ul class="prpl-suggested-todos-list priority-medium"></ul>
		<ul class="prpl-suggested-todos-list priority-low"></ul>
		<?php
	}
}
