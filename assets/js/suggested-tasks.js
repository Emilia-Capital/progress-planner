/* global customElements, progressPlannerSuggestedTasks, confetti, prplDocumentReady, progressPlannerSuggestedTask */
const PRPL_SUGGESTED_TASKS_MAX_ITEMS = 5;

/**
 * Count the number of items in the list.
 *
 * @return {number} The number of items in the list.
 */
const progressPlannerCountItems = () => {
	const items = document.querySelectorAll( '.prpl-suggested-task' );
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
		.querySelectorAll( '.prpl-suggested-task' )
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
	const Item = customElements.get( 'prpl-suggested-task' );
	const item = new Item(
		details.task_id,
		details.title,
		details.description,
		details.points,
		details.action ?? ''
	);

	/**
	 * @todo Implement the parent task functionality.
	 * Use this code: `const parent = details.parent && '' !== details.parent ? details.parent : null;
	 */
	const parent = false;

	if ( ! parent ) {
		// Inject the item into the list.
		document
			.querySelector( '.prpl-suggested-tasks-list' )
			.insertAdjacentElement( 'beforeend', item );
	} else {
		const parentItem = document.querySelector(
			`.prpl-suggested-task[data-task-id="${ parent }"]`
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
		if ( ! parentItem.querySelector( '.prpl-suggested-task-children' ) ) {
			const childListElement = document.createElement( 'ul' );
			childListElement.classList.add( 'prpl-suggested-task-children' );
			parentItem.appendChild( childListElement );
		}

		// Inject the item into the child list.
		parentItem
			.querySelector( '.prpl-suggested-task-children' )
			.insertAdjacentElement( 'beforeend', item );
	}
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

	document
		.querySelectorAll(
			'.prpl-suggested-task[data-task-action="celebrate"]'
		)
		.forEach( ( item ) => {
			item.classList.add( 'prpl-suggested-task-celebrated' );
		} );

	// WIP: Remove celebrated tasks and add them to the completed tasks.
	setTimeout( () => {
		document
			.querySelectorAll( '.prpl-suggested-task-celebrated' )
			.forEach( ( item ) => {
				const taskId = item.getAttribute( 'data-task-id' );

				const request = wp.ajax.post(
					'progress_planner_suggested_task_action',
					{
						task_id: taskId,
						nonce: progressPlannerSuggestedTask.nonce,
						action_type: 'celebrated',
					}
				);
				request.done( () => {
					const el = document.querySelector(
						`.prpl-suggested-task[data-task-id="${ taskId }"]`
					);

					if ( el ) {
						el.remove();
					}

					// Remove the task from the pending celebration.
					window.progressPlannerSuggestedTasks.tasks.pending_celebration =
						window.progressPlannerSuggestedTasks.tasks.pending_celebration.filter(
							( id ) => id !== taskId
						);

					// Add the task to the completed tasks.
					if (
						window.progressPlannerSuggestedTasks.tasks.completed.indexOf(
							taskId
						) === -1
					) {
						window.progressPlannerSuggestedTasks.tasks.completed.push(
							taskId
						);
					}

					const event = new Event(
						'prplMaybeInjectSuggestedTaskEvent'
					);
					document.dispatchEvent( event );
				} );
			} );

		// WIP: Refresh the list.
		while (
			progressPlannerCountItems() <= PRPL_SUGGESTED_TASKS_MAX_ITEMS &&
			progressPlannerGetNextItem()
		) {
			progressPlannerInjectNextItem();
			const event = new Event( 'prplResizeAllGridItemsEvent' );
			document.dispatchEvent( event );
		}
	}, 2000 );
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

// Handle the monthly badges scrolling.
class BadgeScroller {
	constructor( element ) {
		this.element = element;

		this.badgeButtonUp = this.element.querySelector(
			'.prpl-badge-row-button-up'
		);
		this.badgeButtonDown = this.element.querySelector(
			'.prpl-badge-row-button-down'
		);
		this.badgeRowWrapper = this.element.querySelector(
			'.prpl-badge-row-wrapper'
		);
		this.badgeRowWrapperInner = this.element.querySelector(
			'.prpl-badge-row-wrapper-inner'
		);
		this.badges =
			this.badgeRowWrapperInner.querySelectorAll( '.prpl-badge' );
		this.totalRows = this.badges.length / 3;

		this.init();
	}

	init() {
		this.addEventListeners();

		// On page load.
		this.setWrapperHeight();

		// When popover is opened.
		document
			.querySelector( '#prpl-popover-monthly-badges' )
			.addEventListener( 'toggle', ( event ) => {
				if ( 'open' === event.newState ) {
					this.setWrapperHeight();
				}
			} );

		// Handle window resize.
		window.addEventListener( 'resize', () => {
			this.setWrapperHeight();
		} );
	}

	setWrapperHeight() {
		const computedStyle = window.getComputedStyle(
			this.badgeRowWrapperInner
		);
		const gridGap = parseInt( computedStyle.gap );

		// Set CSS variables for the transform calculation.
		this.badgeRowWrapper.style.setProperty(
			'--row-height',
			`${ this.badges[ 0 ].offsetHeight }px`
		);
		this.badgeRowWrapper.style.setProperty(
			'--grid-gap',
			`${ gridGap }px`
		);

		// Set wrapper height to show 2 rows.
		const twoRowsHeight = this.badges[ 0 ].offsetHeight * 2 + gridGap;
		this.badgeRowWrapperInner.style.height = twoRowsHeight + 'px';
	}

	addEventListeners() {
		this.badgeButtonUp.addEventListener( 'click', () =>
			this.handleUpClick()
		);
		this.badgeButtonDown.addEventListener( 'click', () =>
			this.handleDownClick()
		);
	}

	handleUpClick() {
		const computedStyle = window.getComputedStyle(
			this.badgeRowWrapperInner
		);
		const currentRow =
			computedStyle.getPropertyValue( '--prpl-current-row' );
		const nextRow = parseInt( currentRow ) - 1;

		this.badgeButtonDown
			.closest( '.prpl-badge-row-button-wrapper' )
			.classList.remove( 'prpl-badge-row-button-disabled' );

		this.badgeRowWrapperInner.style.setProperty(
			'--prpl-current-row',
			nextRow
		);

		if ( nextRow <= 1 ) {
			this.badgeButtonUp
				.closest( '.prpl-badge-row-button-wrapper' )
				.classList.add( 'prpl-badge-row-button-disabled' );
		}
	}

	handleDownClick() {
		const computedStyle = window.getComputedStyle(
			this.badgeRowWrapperInner
		);
		const currentRow =
			computedStyle.getPropertyValue( '--prpl-current-row' );
		const nextRow = parseInt( currentRow ) + 1;

		this.badgeButtonUp
			.closest( '.prpl-badge-row-button-wrapper' )
			.classList.remove( 'prpl-badge-row-button-disabled' );

		this.badgeRowWrapperInner.style.setProperty(
			'--prpl-current-row',
			nextRow
		);

		if ( nextRow >= this.totalRows - 1 ) {
			this.badgeButtonDown
				.closest( '.prpl-badge-row-button-wrapper' )
				.classList.add( 'prpl-badge-row-button-disabled' );
		}
	}
}

// Initialize on DOM load
prplDocumentReady( () => {
	document
		.querySelectorAll(
			'.prpl-widget-wrapper:not(.in-popover) > .badge-group-monthly'
		)
		.forEach( ( element ) => {
			new BadgeScroller( element );
		} );
} );

const prplMaybeInjectSuggestedTaskEvent = new Event( // eslint-disable-line no-unused-vars
	'prplMaybeInjectSuggestedTaskEvent'
);

// Listen for the event.
document.addEventListener(
	'prplMaybeInjectSuggestedTaskEvent',
	() => {
		while (
			progressPlannerCountItems() <= PRPL_SUGGESTED_TASKS_MAX_ITEMS &&
			progressPlannerGetNextItem()
		) {
			progressPlannerInjectNextItem();
		}
	},
	false
);
