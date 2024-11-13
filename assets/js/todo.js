/* global progressPlannerTodo, jQuery */

/**
 * Run a function when the DOM is ready.
 * Similar to jQuery's $( document ).ready() - which has been deprecated.
 *
 * @param {Function} fn The function to run when the DOM is ready.
 */
const progressPlannerDomReady = ( fn ) => {
	if ( document.readyState !== 'loading' ) {
		fn();
	} else {
		document.addEventListener( 'DOMContentLoaded', fn );
	}
};

/**
 * Save the todo list to the database.
 */
const progressPlannerSaveTodoList = () => {
	let todoList = [];

	jQuery( '#todo-list li' ).each( function () {
		const content = jQuery( this ).find( '.content' ).text();

		todoList.push( {
			content,
			done: jQuery( this )
				.find( 'input[type="checkbox"]' )
				.prop( 'checked' ),
		} );

		// Update aria-labels
		jQuery( this )
			.find( 'input[type="checkbox"]' )
			.attr( 'aria-label', content );
		jQuery( this )
			.find( '.trash' )
			.attr(
				'aria-label',
				progressPlannerTodo.i18n.taskDelete.replace( '%s', content )
			);
	} );

	if ( todoList.length === 0 ) {
		todoList = 'empty';
	}

	// Save the todo list to the database
	wp.ajax.post( 'progress_planner_save_todo_list', {
		todo_list: todoList,
		nonce: progressPlannerTodo.nonce,
	} );

	const event = new Event( 'prplResizeAllGridItemsEvent' );
	document.dispatchEvent( event );
};

/**
 * Initialize the sortable functionality for the todo list.
 */
const progressPlannerInitSortable = () => {
	jQuery( '#todo-list' ).sortable( {
		axis: 'y',
		handle: '.prpl-todo-drag-handle',
		update() {
			// Add a 'data-order' attribute to each todo item
			// based on its position in the list
			jQuery( '#todo-list li' ).each( function ( index ) {
				jQuery( this ).attr( 'data-order', index );
			} );
		},
		stop: () => {
			progressPlannerSaveTodoList();
		},
	} );
};

/**
 * Inject a todo item into the DOM.
 *
 * @param {string}  content    The content of the todo item.
 * @param {boolean} done       Whether the todo item is done.
 * @param {boolean} addToStart Whether to add the todo item to the start of the list.
 * @param {boolean} save       Whether to save the todo list to the database.
 */
const progressPlannerInjectTodoItem = ( content, done, addToStart, save ) => {
	content = content
		.trim()
		.replace( /</g, '&lt;' )
		.replace( />/g, '&gt;' )
		.replace( '"', '&quot;' );

	const deleteTaskAriaLabel = progressPlannerTodo.i18n.taskDelete.replace(
		'%s',
		content
	);
	const moveUpTaskAriaLabel = progressPlannerTodo.i18n.taskMoveUp.replace(
		'%s',
		content
	);
	const moveDownTaskAriaLabel = progressPlannerTodo.i18n.taskMoveDown.replace(
		'%s',
		content
	);

	const todoItemElement = jQuery( '<li></li>' ).html( `
		<span class="prpl-todo-drag-handle" aria-label="${
			progressPlannerTodo.i18n.drag
		}">
			<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
				<path d="M8 7h2V5H8v2zm0 6h2v-2H8v2zm0 6h2v-2H8v2zm6-14v2h2V5h-2zm0 8h2v-2h-2v2zm0 6h2v-2h-2v2z"></path>
			</svg>
		</span>
		<input type="checkbox" aria-label="'${ content }'" ${ done ? 'checked' : '' }>
		<span class="content" contenteditable="plaintext-only">${ content }</span>
		<span class="prpl-move-buttons">
			<button class="move-up" aria-label="${ moveUpTaskAriaLabel }"><span class="dashicons dashicons-arrow-up-alt2"></span></button>
			<button class="move-down" aria-label="${ moveDownTaskAriaLabel }"><span class="dashicons dashicons-arrow-down-alt2"></span></button>
		</span>
		<button class="trash" aria-label="${ deleteTaskAriaLabel }">
			<svg role="img" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"><path fill="#9ca3af" d="M32.99 47.88H15.01c-3.46 0-6.38-2.7-6.64-6.15L6.04 11.49l-.72.12c-.82.14-1.59-.41-1.73-1.22-.14-.82.41-1.59 1.22-1.73.79-.14 1.57-.26 2.37-.38h.02c2.21-.33 4.46-.6 6.69-.81v-.72c0-3.56 2.74-6.44 6.25-6.55 2.56-.08 5.15-.08 7.71 0 3.5.11 6.25 2.99 6.25 6.55v.72c2.24.2 4.48.47 6.7.81.79.12 1.59.25 2.38.39.82.14 1.36.92 1.22 1.73-.14.82-.92 1.36-1.73 1.22l-.72-.12-2.33 30.24c-.27 3.45-3.18 6.15-6.64 6.15Zm-17.98-3h17.97c1.9 0 3.51-1.48 3.65-3.38l2.34-30.46c-2.15-.3-4.33-.53-6.48-.7h-.03c-5.62-.43-11.32-.43-16.95 0h-.03c-2.15.17-4.33.4-6.48.7l2.34 30.46c.15 1.9 1.75 3.38 3.65 3.38ZM24 7.01c2.37 0 4.74.07 7.11.22v-.49c0-1.93-1.47-3.49-3.34-3.55-2.5-.08-5.03-.08-7.52 0-1.88.06-3.34 1.62-3.34 3.55v.49c2.36-.15 4.73-.22 7.11-.22Zm5.49 32.26h-.06c-.83-.03-1.47-.73-1.44-1.56l.79-20.65c.03-.83.75-1.45 1.56-1.44.83.03 1.47.73 1.44 1.56l-.79 20.65c-.03.81-.7 1.44-1.5 1.44Zm-10.98 0c-.8 0-1.47-.63-1.5-1.44l-.79-20.65c-.03-.83.61-1.52 1.44-1.56.84 0 1.52.61 1.56 1.44l.79 20.65c.03.83-.61 1.52-1.44 1.56h-.06Z"/></svg>
		</button>
	` );

	if ( addToStart ) {
		jQuery( '#todo-list' ).prepend( todoItemElement );
	} else {
		jQuery( '#todo-list' ).append( todoItemElement );
	}

	// Focus the new task's content element after it is added to the DOM
	setTimeout( () => {
		todoItemElement.find( 'input[type="checkbox"]' ).focus();
	}, 0 );

	if ( save ) {
		progressPlannerSaveTodoList();
	}
};

progressPlannerDomReady( () => {
	const announce = ( message ) => {
		jQuery( '#todo-aria-live-region' ).text( message );
	};

	// Inject the existing todo list items into the DOM
	progressPlannerTodo.listItems.forEach( ( todoItem, index, array ) => {
		jQuery( '#todo-list' ).append(
			progressPlannerInjectTodoItem(
				todoItem.content,
				todoItem.done,
				false,
				false
			)
		);

		// If this is the last item in the array, initialize the sortable
		if ( index === array.length - 1 ) {
			progressPlannerInitSortable();
			const event = new Event( 'prplResizeAllGridItemsEvent' );
			document.dispatchEvent( event );
		}
	} );

	// When the '#create-todo-item' form is submitted,
	// add a new todo item to the list
	document
		.getElementById( 'create-todo-item' )
		.addEventListener( 'submit', ( event ) => {
			event.preventDefault();
			progressPlannerInjectTodoItem(
				document.getElementById( 'new-todo-content' ).value,
				false, // Not done.
				true, // Add to start.
				true // Save.
			);

			document.getElementById( 'new-todo-content' ).value = '';
		} );

	// When an item is marked as done, move it to the end of the list.
	// When an item is marked as not done, move it to the start of the list.
	jQuery( '#todo-list' ).on( 'change', 'input[type="checkbox"]', function () {
		const todoItem = jQuery( this ).closest( 'li' );
		const todoItemContent = todoItem.find( '.content' ).text();
		const todoItemDone = jQuery( this ).prop( 'checked' );

		todoItem.remove();
		progressPlannerInjectTodoItem(
			todoItemContent,
			todoItemDone,
			! todoItemDone,
			true // Save.
		);

		// Announce the status change and move focus to the moved item
		const announcement = todoItemDone
			? progressPlannerTodo.i18n.taskCompleted.replace(
					'%s',
					todoItemContent
			  )
			: progressPlannerTodo.i18n.taskNotCompleted.replace(
					'%s',
					todoItemContent
			  );
		announce( announcement );
	} );

	// When an item's contenteditable element is edited,
	// save the new content to the database
	jQuery( '#todo-list' ).on( 'input', '.content', function () {
		progressPlannerSaveTodoList();
	} );

	// When the trash button is clicked, remove the todo item from the list
	jQuery( '#todo-list' ).on( 'click', '.trash', function () {
		const todoItem = jQuery( this ).closest( 'li' );
		const nextItem = todoItem.next( 'li' );
		const prevItem = todoItem.prev( 'li' );

		todoItem.remove();
		progressPlannerSaveTodoList();

		// Shift focus to the next item if available, otherwise to the previous item, otherwise to the input field
		if ( nextItem.length ) {
			nextItem.find( 'input[type="checkbox"]' ).focus();
		} else if ( prevItem.length ) {
			prevItem.find( 'input[type="checkbox"]' ).focus();
		} else {
			jQuery( '#new-todo-content' ).focus();
		}
	} );

	// When the move up button is clicked, move the todo item up the list
	jQuery( '#todo-list' ).on( 'click', '.move-up', function () {
		const todoItem = jQuery( this ).closest( 'li' );
		todoItem.insertBefore( todoItem.prev( 'li' ) );
		progressPlannerSaveTodoList();
		wp.a11y.speak( progressPlannerTodo.i18n.taskMovedUp );
	} );

	// When the move down button is clicked, move the todo item down the list
	jQuery( '#todo-list' ).on( 'click', '.move-down', function () {
		const todoItem = jQuery( this ).closest( 'li' );
		todoItem.insertAfter( todoItem.next( 'li' ) );
		progressPlannerSaveTodoList();
		wp.a11y.speak( progressPlannerTodo.i18n.taskMovedDown );
	} );
} );
