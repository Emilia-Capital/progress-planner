/* global progressPlannerInjectTodoItem, progressPlanner, jQuery */

/**
 * Dismiss a task.
 *
 * @param {string} taskId The task ID.
 */
const progressPlannerDismissTask = ( taskId ) => {
	// Save the todo list to the database
	jQuery.post(
		progressPlanner.ajaxUrl,
		{
			action: 'progress_planner_dismiss_task',
			task_id: taskId,
			nonce: progressPlanner.nonce,
		},
		() => {
			document
				.querySelector( '.prpl-suggested-task-' + taskId )
				.classList.add( 'prpl-suggested-task-dismissed' );
		}
	);
};

/**
 * Dismiss a task.
 *
 * @param {string} taskId The task ID.
 */
const progressPlannerSnoozeTask = ( taskId ) => {
	// Save the todo list to the database
	jQuery.post(
		progressPlanner.ajaxUrl,
		{
			action: 'progress_planner_snooze_task',
			task_id: taskId,
			nonce: progressPlanner.nonce,
		},
		() => {
			document
				.querySelector( '.prpl-suggested-task-' + taskId )
				.classList.add( 'prpl-suggested-task-snoozed' );
		}
	);
};

document
	.querySelectorAll( '.prpl-suggested-task-button' )
	.forEach( function ( button ) {
		button.addEventListener( 'click', function () {
			const taskId = button.getAttribute( 'data-task-id' );
			const taskTitle = button.getAttribute( 'data-task-title' );
			const action = button.getAttribute( 'data-action' );

			if ( 'add-todo' === action ) {
				// Add the task to the todo list.
				progressPlannerInjectTodoItem(
					taskTitle, // The task title.
					false, // Task not done.
					true, // Add to start of list.
					true // Save.
				);

				// Dismiss the task.
				progressPlannerDismissTask( taskId );
			}

			if ( 'dismiss' === action ) {
				// Dismiss the task.
				progressPlannerDismissTask( taskId );
			}

			if ( 'snooze' === action ) {
				// Snooze the task.
				progressPlannerSnoozeTask( taskId );
			}
		} );
	} );
