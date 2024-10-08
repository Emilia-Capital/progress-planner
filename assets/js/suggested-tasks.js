/* global progressPlannerSuggestedTasks, jQuery */
const PRPL_SUGGESTED_CLASS_PREFIX = 'prpl-suggested-task';
const PRPL_SUGGESTED_TASKS_CLASSES = {
	SNOOZED: `${ PRPL_SUGGESTED_CLASS_PREFIX }-snoozed`,
};
const PRPL_SUGGESTED_TASKS_MAX_ITEMS = 5;

/**
 * Count the number of items in the list.
 *
 * @return {number} The number of items in the list.
 */
const progressPlannerCountItems = () => {
	const items = document.querySelectorAll(
		`.${ PRPL_SUGGESTED_CLASS_PREFIX }`
	);
	return items.length;
};

/**
 * Get the next item to inject.
 *
 * @return {Object} The next item to inject.
 */
const progressPlannerGetNextItem = () => {
	// Remove completed, dismissed and snoozed items.
	const tasks = progressPlannerSuggestedTasks.tasks;
	const items = tasks.details;
	const completed = tasks.completed;
	const dismissed = tasks.dismissed;
	const snoozed = tasks.snoozed;

	// Create an array of items that are in the list.
	const inList = [];
	document
		.querySelectorAll( `.${ PRPL_SUGGESTED_CLASS_PREFIX }` )
		.forEach( function ( item ) {
			inList.push( parseInt( item.getAttribute( 'data-task-id' ) ) );
		} );

	items.forEach( function ( item ) {
		if (
			completed.includes( item.task_id ) ||
			dismissed.includes( item.task_id ) ||
			snoozed.includes( item.task_id ) ||
			inList.includes( item.task_id )
		) {
			items.splice( items.indexOf( item ), 1 );
		}
	} );

	// Get items with a priority set to `high`.
	const highPriorityItems = items.filter( function ( item ) {
		return 'high' === item.priority;
	} );

	// If there are high priority items, return the first one.
	if ( highPriorityItems.length ) {
		return highPriorityItems[ 0 ];
	}

	// Get items with a priority set to `medium`.
	const mediumPriorityItems = items.filter( function ( item ) {
		return 'medium' === item.priority;
	} );

	// If there are medium priority items, return the first one.
	if ( mediumPriorityItems.length ) {
		return mediumPriorityItems[ 0 ];
	}

	// Return the first item.
	return items[ 0 ];
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
			if (
				'dismiss' === actionTask ||
				'complete' === actionTask ||
				'snooze' === actionTask
			) {
				document
					.querySelector(
						`.${ PRPL_SUGGESTED_CLASS_PREFIX }-${ taskId }`
					)
					.remove();

				while (
					progressPlannerCountItems() <
						PRPL_SUGGESTED_TASKS_MAX_ITEMS &&
					progressPlannerGetNextItem()
				) {
					progressPlannerInjectNextItem();
				}
			}
		}
	);
};

/**
 * Inject the next item.
 */
const progressPlannerInjectNextItem = () => {
	const nextItem = progressPlannerGetNextItem();
	if ( ! nextItem ) {
		return;
	}

	progressPlannerInjectSuggestedTodoItem( nextItem );
};

/**
 * Inject a todo item.
 *
 * @param {Object} details The details of the todo item.
 */
const progressPlannerInjectSuggestedTodoItem = ( details ) => {
	const tasks = progressPlannerSuggestedTasks.tasks;
	// Don not render items that have been dismissed or completed.
	if (
		tasks.dismissed.includes( details.task_id ) ||
		tasks.completed.includes( details.task_id )
	) {
		return;
	}

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
	tasks.snoozed.forEach( function ( snoozedTask ) {
		if ( snoozedTask.id === details.task_id ) {
			item.classList.add( PRPL_SUGGESTED_TASKS_CLASSES.SNOOZED );
		}
	} );

	// Replace placeholders with the actual values.
	const itemHTML = item.outerHTML
		.replace( new RegExp( '{taskTitle}', 'g' ), details.title )
		.replace( new RegExp( '{taskId}', 'g' ), details.task_id )
		.replace( new RegExp( '{taskDescription}', 'g' ), details.description )
		.replace( new RegExp( '{taskPriority}', 'g' ), details.priority );

	/**
	 * @todo Implement the parent task functionality.
	 * Use this code: `const parent = details.parent && '' !== details.parent ? details.parent : null;
	 */
	const parent = false;

	if ( ! parent ) {
		// Inject the item into the list.
		document
			.querySelector(
				`.prpl-suggested-todos-list.priority-${ details.priority }`
			)
			.insertAdjacentHTML( 'beforeend', itemHTML );
	} else {
		const parentItem = document.querySelector(
			`.${ PRPL_SUGGESTED_CLASS_PREFIX }-${ parent }`
		);
		// If we could not find the parent item, try again after 500ms.
		window.progressPlannerRenderAttempts =
			window.progressPlannerRenderAttempts || 0;
		if ( window.progressPlannerRenderAttempts > 500 ) {
			return;
		}
		if ( ! parentItem ) {
			setTimeout( () => {
				progressPlannerInjectSuggestedTodoItem( details );
				window.progressPlannerRenderAttempts++;
			}, 10 );
			return;
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

// Populate the list on load.
document.addEventListener( 'DOMContentLoaded', () => {
	// Inject items, until we reach the maximum number of items.
	while (
		progressPlannerCountItems() < PRPL_SUGGESTED_TASKS_MAX_ITEMS &&
		progressPlannerGetNextItem()
	) {
		progressPlannerInjectNextItem();
	}
} );
