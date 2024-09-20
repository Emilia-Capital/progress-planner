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
			const action = button.getAttribute( 'data-action' );

			switch ( action ) {
				case 'add-todo':
					progressPlannerInjectTodoItem(
						button.getAttribute( 'data-task-title' ), // The task title.
						false, // Task not done.
						true, // Add to start of list.
						true // Save.
					);
				// falls through.
				case 'dismiss':
					progressPlannerModifyTask(
						taskId,
						'dismiss',
						'prpl-suggested-task-dismissed'
					);
					break;

				case 'snooze':
					progressPlannerModifyTask(
						taskId,
						'snooze',
						'prpl-suggested-task-snoozed'
					);
					break;

				case 'complete':
					progressPlannerModifyTask(
						taskId,
						'complete',
						'prpl-suggested-task-completed'
					);
					break;
			}
		} );
	} );
