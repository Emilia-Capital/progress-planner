/* global progressPlannerInjectTodoItem, progressPlannerSuggestedTasks, jQuery */

const PRPL_SUGGESTED_TASKS_CLASSES = {
	DISMISSED: 'prpl-suggested-task-dismissed',
	SNOOZED: 'prpl-suggested-task-snoozed',
	COMPLETED: 'prpl-suggested-task-completed',
};
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
		progressPlannerSuggestedTasks.ajaxUrl,
		{
			action: `progress_planner_${ action }_task`,
			task_id: taskId,
			nonce: progressPlannerSuggestedTasks.nonce,
		},
		() => {
			document
				.querySelector( `.prpl-suggested-task-${ taskId }` )
				.classList.add( className );
		}
	);
};

/**
 * Inject a todo item.
 *
 * @param {Object} details The details of the todo item.
 */
const progressPlannerInjectSuggestedTodoItem = ( details ) => {
	const list = document.querySelector(
		`.prpl-suggested-todos-list.priority-${ details.priority }`
	);
	const template = document.getElementById( 'prpl-suggested-task-template' );

	// Clone the template element.
	const item = template.cloneNode( true );
	item.classList.add( `prpl-suggested-task-${ details.id }` );

	// Add classes to the element.
	if (
		progressPlannerSuggestedTasks.tasks.dismissed.includes( details.id )
	) {
		item.classList.add( PRPL_SUGGESTED_TASKS_CLASSES.DISMISSED );
	}
	progressPlannerSuggestedTasks.tasks.snoozed.forEach(
		function ( snoozedTask ) {
			if ( snoozedTask.id === details.id ) {
				item.classList.add( PRPL_SUGGESTED_TASKS_CLASSES.SNOOZED );
			}
		}
	);
	if (
		progressPlannerSuggestedTasks.tasks.completed.includes( details.id )
	) {
		item.classList.add( PRPL_SUGGESTED_TASKS_CLASSES.COMPLETED );
	}

	// Replace placeholders with the actual values.
	const itemHTML = item.outerHTML
		.replace( new RegExp( '{taskTitle}', 'g' ), details.title )
		.replace( new RegExp( '{taskId}', 'g' ), details.id )
		.replace( new RegExp( '{taskDescription}', 'g' ), details.description )
		.replace( new RegExp( '{taskPriority}', 'g' ), details.priority );

	// Inject the item into the list.
	list.insertAdjacentHTML( 'beforeend', itemHTML );

	// Add listeners to the item.
	prplSuggestedTodoItemListeners(
		document.querySelector( `.prpl-suggested-task-${ details.id }` )
	);
};

const prplSuggestedTodoItemListeners = ( item ) => {
	item.querySelectorAll( '.prpl-suggested-task-button' ).forEach(
		function ( button ) {
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
							PRPL_SUGGESTED_TASKS_CLASSES.DISMISSED
						);
						break;

					case 'snooze':
						progressPlannerModifyTask(
							taskId,
							'snooze',
							PRPL_SUGGESTED_TASKS_CLASSES.SNOOZED
						);
						break;

					case 'complete':
						progressPlannerModifyTask(
							taskId,
							'complete',
							PRPL_SUGGESTED_TASKS_CLASSES.COMPLETED
						);
						break;
				}
			} );
		}
	);
};

// Inject the suggested tasks.
Object.keys( progressPlannerSuggestedTasks.tasks.details ).forEach(
	( task ) => {
		const taskDetails = progressPlannerSuggestedTasks.tasks.details[ task ];
		taskDetails.id = task;
		progressPlannerInjectSuggestedTodoItem( taskDetails );
	}
);
