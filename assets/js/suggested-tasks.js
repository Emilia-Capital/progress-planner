/* global progressPlannerInjectTodoItem, progressPlanner, jQuery */

/**
 * Modify a task.
 *
 * @param {string} taskId    The task ID.
 * @param {string} action    The action to perform.
 * @param {string} className The class to add to the task element after the action is performed.
 */
const progressPlannerModifyTask = ( taskId, action, className ) => {
	// Save the todo list to the database
	jQuery.post(
		progressPlanner.ajaxUrl,
		{
			action: `progress_planner_${ action }_task`,
			task_id: taskId,
			nonce: progressPlanner.nonce,
		},
		() => {
			document
				.querySelector( '.prpl-suggested-task-' + taskId )
				.classList.add( className );
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
				progressPlannerModifyTask(
					taskId,
					'dismiss',
					'prpl-suggested-task-dismissed'
				);
			}

			if ( 'dismiss' === action ) {
				// Dismiss the task.
				progressPlannerModifyTask(
					taskId,
					'dismiss',
					'prpl-suggested-task-dismissed'
				);
			}

			if ( 'snooze' === action ) {
				// Snooze the task.
				progressPlannerModifyTask(
					taskId,
					'snooze',
					'prpl-suggested-task-snoozed'
				);
			}

			if ( 'complete' === action ) {
				// Complete the task.
				progressPlannerModifyTask(
					taskId,
					'complete',
					'prpl-suggested-task-completed'
				);
			}
		} );
	} );
