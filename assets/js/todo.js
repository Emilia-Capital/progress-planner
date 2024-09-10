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
		<button class="trash" aria-label="${ deleteTaskAriaLabel }"><span class="dashicons dashicons-trash"></span></button>
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
		}
	} );

	// When the '#create-todo-item' form is submitted,
	// add a new todo item to the list
	jQuery( '#create-todo-item' ).submit( function ( event ) {
		event.preventDefault();
		progressPlannerInjectTodoItem(
			jQuery( '#new-todo-content' ).val(),
			false, // Not done.
			true, // Add to start.
			true // Save.
		);

		jQuery( '#new-todo-content' ).val( '' );
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
} );
