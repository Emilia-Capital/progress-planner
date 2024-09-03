/* global progressPlannerInjectTodoItem */

document
	.querySelectorAll( '.prpl-suggested-task-button' )
	.forEach( function ( button ) {
		button.addEventListener( 'click', function () {
			// const taskId = button.getAttribute( 'data-task-id' );
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
			}
		} );
	} );
