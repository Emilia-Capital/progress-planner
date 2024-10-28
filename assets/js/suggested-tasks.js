/* global progressPlannerSuggestedTasks, jQuery, confetti */
const PRPL_SUGGESTED_TASK_CLASSNAME = 'prpl-suggested-task';
const PRPL_SUGGESTED_TASKS_MAX_ITEMS = 5;

/**
 * Count the number of items in the list.
 *
 * @return {number} The number of items in the list.
 */
const progressPlannerCountItems = () => {
	const items = document.querySelectorAll(
		`.${ PRPL_SUGGESTED_TASK_CLASSNAME }`
	);
	return items.length;
};

/**
 * Get the next item to inject.
 *
 * @return {Object} The next item to inject.
 */
const progressPlannerGetNextItem = () => {
	// Remove completed and snoozed items.
	const tasks = progressPlannerSuggestedTasks.tasks;
	const items = tasks.details;
	const completed = tasks.completed;
	const snoozed = tasks.snoozed;

	// Create an array of items that are in the list.
	const inList = [];
	document
		.querySelectorAll( `.${ PRPL_SUGGESTED_TASK_CLASSNAME }` )
		.forEach( function ( item ) {
			inList.push( item.getAttribute( 'data-task-id' ).toString() );
		} );

	items.forEach( function ( item ) {
		if (
			completed.includes( item.task_id.toString() ) ||
			inList.includes( item.task_id.toString() )
		) {
			items.splice( items.indexOf( item ), 1 );
		}
		snoozed.forEach( ( snoozedItem ) => {
			if ( item.task_id.toString() === snoozedItem.id ) {
				items.splice( items.indexOf( item ), 1 );
			}
		} );
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
 * Snooze a task.
 *
 * @param {string} taskId   The task ID.
 * @param {string} duration The duration to snooze the task for.
 */
const progressPlannerSnoozeTask = ( taskId, duration ) => {
	taskId = taskId.toString();
	// Save the todo list to the database
	jQuery.post(
		progressPlannerSuggestedTasks.ajaxUrl,
		{
			action: 'progress_planner_suggested_task_action',
			task_id: taskId.toString(),
			nonce: progressPlannerSuggestedTasks.nonce,
			action_type: 'snooze',
			duration,
		},
		() => {
			const el = document.querySelector(
				`.${ PRPL_SUGGESTED_TASK_CLASSNAME }[data-task-id="${ taskId }"]`
			);

			if ( el ) {
				el.remove();
			}

			// Update the global var.
			if (
				progressPlannerSuggestedTasks.tasks.snoozed.indexOf(
					taskId
				) === -1
			) {
				progressPlannerSuggestedTasks.tasks.snoozed.push( taskId );
			}

			while (
				progressPlannerCountItems() <= PRPL_SUGGESTED_TASKS_MAX_ITEMS &&
				progressPlannerGetNextItem()
			) {
				progressPlannerInjectNextItem();
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
	// Clone the template element.
	const item = document
		.getElementById( `${ PRPL_SUGGESTED_TASK_CLASSNAME }-template` )
		.cloneNode( true );

	// Remove the ID attribute.
	item.removeAttribute( 'id' );

	// Hide the info button if the description is empty.
	if (
		'string' === typeof details.description &&
		'' === details.description.trim()
	) {
		const infoButton = item.querySelector( 'button[data-action="info"]' );
		if ( !! infoButton ) {
			infoButton.style.display = 'none';
		}
	}

	// Replace placeholders with the actual values.
	const itemHTML = item.outerHTML
		.replace( new RegExp( '{taskTitle}', 'g' ), details.title )
		.replace( new RegExp( '{taskId}', 'g' ), details.task_id.toString() )
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
			.querySelector( '.prpl-suggested-tasks-list' )
			.insertAdjacentHTML( 'beforeend', itemHTML );
	} else {
		const parentItem = document.querySelector(
			`.${ PRPL_SUGGESTED_TASK_CLASSNAME }[data-task-id="${ parent }"]`
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
				`.${ PRPL_SUGGESTED_TASK_CLASSNAME }-children`
			)
		) {
			const childListElement = document.createElement( 'ul' );
			childListElement.classList.add(
				`${ PRPL_SUGGESTED_TASK_CLASSNAME }-children`
			);
			parentItem.appendChild( childListElement );
		}

		// Inject the item into the child list.
		parentItem
			.querySelector( `.${ PRPL_SUGGESTED_TASK_CLASSNAME }-children` )
			.insertAdjacentHTML( 'beforeend', itemHTML );
	}

	// Add listeners to the item.
	prplSuggestedTodoItemListeners(
		document.querySelector(
			`.${ PRPL_SUGGESTED_TASK_CLASSNAME }[data-task-id="${ details.task_id.toString() }"]`
		)
	);
};

const prplSuggestedTodoItemListeners = ( item ) => {
	item.querySelectorAll(
		`.${ PRPL_SUGGESTED_TASK_CLASSNAME }-button`
	).forEach( function ( button ) {
		button.addEventListener( 'click', function () {
			const action = button.getAttribute( 'data-action' );

			switch ( action ) {
				case 'snooze':
					item.querySelector( '.tooltip-actions' ).classList.remove(
						'prpl-suggested-task-info-open'
					);
					item.querySelector( '.tooltip-actions' ).classList.toggle(
						'prpl-suggested-task-snooze-open'
					);
					break;

				case 'close-snooze':
					item.querySelector( '.tooltip-actions' ).classList.remove(
						'prpl-suggested-task-snooze-open'
					);
					item.querySelector(
						'.prpl-suggested-snooze-duration-selector.prpl-toggle-radio-group-open'
					)?.classList.remove( 'prpl-toggle-radio-group-open' );
					break;

				case 'info':
				case 'close-info':
					item.querySelector( '.tooltip-actions' ).classList.remove(
						'prpl-suggested-task-snooze-open'
					);
					item.querySelector( '.tooltip-actions' ).classList.toggle(
						'prpl-suggested-task-info-open'
					);
					break;
			}
		} );
	} );

	// Toggle snooze duration radio group.
	item.querySelector( '.prpl-toggle-radio-group' ).addEventListener(
		'click',
		function () {
			this.closest(
				'.prpl-suggested-snooze-duration-selector'
			).classList.toggle( 'prpl-toggle-radio-group-open' );
		}
	);

	// Handle snooze duration radio group change.
	item.querySelectorAll(
		'.prpl-snooze-duration-radio-group input[type="radio"]'
	).forEach( ( radioElement ) => {
		radioElement.addEventListener( 'change', function () {
			progressPlannerSnoozeTask(
				item.getAttribute( 'data-task-id' ),
				this.value
			);
		} );
	} );
};

const prplTriggerConfetti = () => {
	const prplConfettiDefaults = {
		spread: 360,
		ticks: 50,
		gravity: 0,
		decay: 0.94,
		startVelocity: 30,
		shapes: [ 'star' ],
		colors: [ 'FFE400', 'FFBD00', 'E89400', 'FFCA6C', 'FDFFB8' ],
	};

	const progressPlannerRenderAttemptshoot = () => {
		confetti( {
			...prplConfettiDefaults,
			particleCount: 40,
			scalar: 1.2,
			shapes: [ 'star' ],
		} );

		confetti( {
			...prplConfettiDefaults,
			particleCount: 10,
			scalar: 0.75,
			shapes: [ 'circle' ],
		} );
	};

	setTimeout( progressPlannerRenderAttemptshoot, 0 );
	setTimeout( progressPlannerRenderAttemptshoot, 100 );
	setTimeout( progressPlannerRenderAttemptshoot, 200 );
};

const prplPendingCelebration =
	progressPlannerSuggestedTasks.tasks.pending_celebration;
if ( prplPendingCelebration && prplPendingCelebration.length ) {
	setTimeout( () => {
		// Trigger the celebration event.
		document.dispatchEvent( new Event( 'prplCelebrateTasks' ) );
	}, 3000 );
}

// Create a new custom event to trigger the celebration.
document.addEventListener( 'prplCelebrateTasks', () => {
	prplTriggerConfetti();
} );

// Populate the list on load.
document.addEventListener( 'DOMContentLoaded', () => {
	// Inject items, until we reach the maximum number of items.
	while (
		progressPlannerCountItems() <= PRPL_SUGGESTED_TASKS_MAX_ITEMS &&
		progressPlannerGetNextItem()
	) {
		progressPlannerInjectNextItem();
		const event = new Event( 'prplResizeAllGridItemsEvent' );
		document.dispatchEvent( event );
	}
} );
