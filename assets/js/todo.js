/* global prplTodo, customElements, prplDocumentReady */

/**
 * Save the todo list to the database.
 */
const prplSaveTodoList = () => {
	let todoList = [];

	document
		.querySelectorAll( '#todo-list prpl-todo-item' )
		.forEach( ( todoItem ) => {
			const content = todoItem.querySelector( '.content' ).textContent;

			todoList.push( {
				content,
				done: todoItem.querySelector( 'input[type="checkbox"]' )
					.checked,
			} );
		} );

	if ( todoList.length === 0 ) {
		todoList = 'empty';
	}

	// Save the todo list to the database
	wp.ajax.post( 'prpl_save_todo_list', {
		todo_list: todoList,
		nonce: prplTodo.nonce,
	} );

	const event = new Event( 'prplResizeAllGridItemsEvent' );
	document.dispatchEvent( event );
};

/**
 * Inject a todo item into the DOM.
 *
 * @param {string}  content    The content of the todo item.
 * @param {boolean} done       Whether the todo item is done.
 * @param {boolean} addToStart Whether to add the todo item to the start of the list.
 * @param {boolean} save       Whether to save the todo list to the database.
 */
const prplInjectTodoItem = ( content, done, addToStart, save ) => {
	const Item = customElements.get( 'prpl-todo-item' );
	const todoItemElement = new Item( content, done );

	if ( addToStart ) {
		document.getElementById( 'todo-list' ).prepend( todoItemElement );
	} else {
		document.getElementById( 'todo-list' ).appendChild( todoItemElement );
	}

	// Focus the new task's content element after it is added to the DOM
	setTimeout( () => {
		todoItemElement.querySelector( 'input[type="checkbox"]' ).focus();
	}, 0 );

	if ( save ) {
		prplSaveTodoList();
	}
};

prplDocumentReady( () => {
	// Inject the existing todo list items into the DOM
	prplTodo.listItems.forEach( ( todoItem, index, array ) => {
		prplInjectTodoItem(
			todoItem.content,
			todoItem.done,
			false,
			false
		);

		// If this is the last item in the array, resize the grid items.
		if ( index === array.length - 1 ) {
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
			prplInjectTodoItem(
				document.getElementById( 'new-todo-content' ).value,
				false, // Not done.
				true, // Add to start.
				true // Save.
			);

			document.getElementById( 'new-todo-content' ).value = '';
		} );
} );
