/* global progressPlannerSuggestedTasks, jQuery */
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
			document
				.querySelector(
					`.${ PRPL_SUGGESTED_CLASS_PREFIX }-${ taskId }`
				)
				.classList.add(
					{
						dismiss: PRPL_SUGGESTED_TASKS_CLASSES.DISMISSED,
						snooze: PRPL_SUGGESTED_TASKS_CLASSES.SNOOZED,
						complete: PRPL_SUGGESTED_TASKS_CLASSES.COMPLETED,
					}[ actionTask ]
				);
		}
	);
};

/**
 * Inject a todo item.
 *
 * @param {Object} details The details of the todo item.
 */
const progressPlannerInjectSuggestedTodoItem = ( details ) => {
	const tasks = progressPlannerSuggestedTasks.tasks;
	// Clone the template element.
	const item = document
		.getElementById( `${ PRPL_SUGGESTED_CLASS_PREFIX }-template` )
		.cloneNode( true );

	// Remove the ID attribute.
	item.removeAttribute( 'id' );

	// Add classes to the element.
	item.classList.add(
		PRPL_SUGGESTED_CLASS_PREFIX,
		`${ PRPL_SUGGESTED_CLASS_PREFIX }-${ details.task_id }`,
		`${ PRPL_SUGGESTED_CLASS_PREFIX }-completion-${ details.completion_type }`
	);
	if ( tasks.dismissed.includes( details.task_id ) ) {
		item.classList.add( PRPL_SUGGESTED_TASKS_CLASSES.DISMISSED );
	}
	tasks.snoozed.forEach( function ( snoozedTask ) {
		if ( snoozedTask.id === details.task_id ) {
			item.classList.add( PRPL_SUGGESTED_TASKS_CLASSES.SNOOZED );
		}
	} );
	if ( tasks.completed.includes( details.task_id ) ) {
		item.classList.add( PRPL_SUGGESTED_TASKS_CLASSES.COMPLETED );
	}

	// If the `completion_type` is set to `auto`, remove the button with `data-action="complete"`.
	if ( 'auto' === details.completion_type ) {
		item.querySelector(
			`.${ PRPL_SUGGESTED_CLASS_PREFIX }-button[data-action="complete"]`
		).remove();
	}

	// Replace placeholders with the actual values.
	const itemHTML = item.outerHTML
		.replace( new RegExp( '{taskTitle}', 'g' ), details.title )
		.replace( new RegExp( '{taskId}', 'g' ), details.task_id )
		.replace( new RegExp( '{taskDescription}', 'g' ), details.description )
		.replace( new RegExp( '{taskPriority}', 'g' ), details.priority );

	// Get the parent item.
	const parent =
		details.parent && '' !== details.parent ? details.parent : null;

	if ( null !== parent ) {
		const parentItem = document.querySelector(
			`.${ PRPL_SUGGESTED_CLASS_PREFIX }-${ parent }`
		);
		// If we could not find the parent item, try again after 500ms.
		if ( ! parentItem ) {
			setTimeout( () => {
				progressPlannerInjectSuggestedTodoItem( details );
			}, 500 );
		}

		// If the child list does not exist, create it.
		if (
			! parentItem.querySelector(
				`.${ PRPL_SUGGESTED_CLASS_PREFIX }-children`
			)
		) {
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
		document
			.querySelector(
				`.prpl-suggested-todos-list.priority-${ details.priority }`
			)
			.insertAdjacentHTML( 'beforeend', itemHTML );
	}

	// Add listeners to the item.
	prplSuggestedTodoItemListeners(
		document.querySelector(
			`.${ PRPL_SUGGESTED_CLASS_PREFIX }-${ details.task_id }`
		)
	);
};

const prplSuggestedTodoItemListeners = ( item ) => {
	item.querySelectorAll( `.${ PRPL_SUGGESTED_CLASS_PREFIX }-button` ).forEach(
		function ( button ) {
			button.addEventListener( 'click', function () {
				const action = button.getAttribute( 'data-action' );

				progressPlannerModifyTask(
					button.getAttribute( 'data-task-id' ),
					action
				);
			} );
		}
	);
};

// Inject the suggested tasks.
Object.keys( progressPlannerSuggestedTasks.tasks.details ).forEach(
	( task ) => {
		const taskDetails = progressPlannerSuggestedTasks.tasks.details[ task ];
		progressPlannerInjectSuggestedTodoItem( taskDetails );
	}
);
