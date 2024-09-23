/* global progressPlannerInjectTodoItem, progressPlannerSuggestedTasks, jQuery */
const PRPL_SUGGESTED_CLASS_PREFIX = 'prpl-suggested-task';
const PRPL_SUGGESTED_TASKS_CLASSES = {
	DISMISSED: `${ PRPL_SUGGESTED_CLASS_PREFIX }-dismissed`,
	SNOOZED: `${ PRPL_SUGGESTED_CLASS_PREFIX }-snoozed`,
	COMPLETED: `${ PRPL_SUGGESTED_CLASS_PREFIX }-completed`,
};
/**
 * Modify a task.
 *
 * @param {string} taskId     The task ID.
 * @param {string} actionTask The action to perform.
 */
const progressPlannerModifyTask = ( taskId, actionTask ) => {
	// Save the todo list to the database
	jQuery.post(
		progressPlannerSuggestedTasks.ajaxUrl,
		{
			action: 'progress_planner_suggested_task_action',
			task_id: taskId,
			nonce: progressPlannerSuggestedTasks.nonce,
			action_type: actionTask,
		},
		() => {
			const classNames = {
				dismiss: PRPL_SUGGESTED_TASKS_CLASSES.DISMISSED,
				snooze: PRPL_SUGGESTED_TASKS_CLASSES.SNOOZED,
				complete: PRPL_SUGGESTED_TASKS_CLASSES.COMPLETED,
			};
			document
				.querySelector(
					`.${ PRPL_SUGGESTED_CLASS_PREFIX }-${ taskId }`
				)
				.classList.add( classNames[ actionTask ] );
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
	const template = document.getElementById(
		`${ PRPL_SUGGESTED_CLASS_PREFIX }-template`
	);
	const tasks = progressPlannerSuggestedTasks.tasks;
	// Clone the template element.
	const item = template.cloneNode( true );
	item.classList.add(
		PRPL_SUGGESTED_CLASS_PREFIX,
		`${ PRPL_SUGGESTED_CLASS_PREFIX }-${ details.id }`
	);
	item.removeAttribute( 'id' );

	// Add classes to the element.
	if ( tasks.dismissed.includes( details.id ) ) {
		item.classList.add( PRPL_SUGGESTED_TASKS_CLASSES.DISMISSED );
	}
	tasks.snoozed.forEach( function ( snoozedTask ) {
		if ( snoozedTask.id === details.id ) {
			item.classList.add( PRPL_SUGGESTED_TASKS_CLASSES.SNOOZED );
		}
	} );
	if ( tasks.completed.includes( details.id ) ) {
		item.classList.add( PRPL_SUGGESTED_TASKS_CLASSES.COMPLETED );
	}

	// Replace placeholders with the actual values.
	const itemHTML = item.outerHTML
		.replace( new RegExp( '{taskTitle}', 'g' ), details.title )
		.replace( new RegExp( '{taskId}', 'g' ), details.id )
		.replace( new RegExp( '{taskDescription}', 'g' ), details.description )
		.replace( new RegExp( '{taskPriority}', 'g' ), details.priority );

	// Get the parent item.
	const parent =
		details.parent && '' !== details.parent ? details.parent : null;

	if ( parent ) {
		const parentItem = document.querySelector(
			`.${ PRPL_SUGGESTED_CLASS_PREFIX }-${ parent }`
		);
		// If we could not find the parent item, try again after 500ms.
		if ( ! parentItem ) {
			setTimeout( () => {
				progressPlannerInjectSuggestedTodoItem( details );
			}, 500 );
		}

		// Check if the parent item has a child list.
		const childList = parentItem.querySelector(
			`.${ PRPL_SUGGESTED_CLASS_PREFIX }-children`
		);
		// If the child list does not exist, create it.
		if ( ! childList ) {
			const childListElement = document.createElement( 'ul' );
			childListElement.classList.add(
				`${ PRPL_SUGGESTED_CLASS_PREFIX }-children`
			);
			parentItem.appendChild( childListElement );
		}

		// Inject the item into the child list.
		parentItem
			.querySelector( `.${ PRPL_SUGGESTED_CLASS_PREFIX }-children` )
			.insertAdjacentHTML( 'beforeend', itemHTML );
	} else {
		// Inject the item into the list.
		list.insertAdjacentHTML( 'beforeend', itemHTML );
	}

	// Add listeners to the item.
	prplSuggestedTodoItemListeners(
		document.querySelector(
			`.${ PRPL_SUGGESTED_CLASS_PREFIX }-${ details.id }`
		)
	);
};

const prplSuggestedTodoItemListeners = ( item ) => {
	item.querySelectorAll( `.${ PRPL_SUGGESTED_CLASS_PREFIX }-button` ).forEach(
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
						progressPlannerModifyTask( taskId, action );
						break;

					case 'snooze':
						progressPlannerModifyTask( taskId, action );
						break;

					case 'complete':
						progressPlannerModifyTask( taskId, action );
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
