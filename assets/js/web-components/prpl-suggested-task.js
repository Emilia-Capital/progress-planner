/* global customElements, progressPlannerSuggestedTask, HTMLElement */

/**
 * Suggested task.
 */
customElements.define(
	'prpl-suggested-task',
	class extends HTMLElement {
		constructor(
			taskId,
			taskTitle,
			taskDescription,
			taskPoints,
			taskAction = ''
		) {
			// Get parent class properties
			super();

			this.setAttribute( 'role', 'listitem' );

			this.innerHTML = `
			<li class="prpl-suggested-task" data-task-id="${ taskId }" data-task-action="${ taskAction }">
				<h3>${ taskTitle }</h3>
				<div class="prpl-suggested-task-actions">
					<div class="tooltip-actions">
						<button
							type="button"
							class="prpl-suggested-task-button"
							data-task-id="${ taskId }"
							data-task-title="${ taskTitle }"
							data-action="info"
							data-target="info"
							title="${ progressPlannerSuggestedTask.i18n.info }"
						>
							<span class="dashicons dashicons-info"></span>
							<span class="screen-reader-text">${ progressPlannerSuggestedTask.i18n.info }</span>
						</button>
						<button
							type="button"
							class="prpl-suggested-task-button"
							data-task-id="${ taskId }"
							data-task-title="${ taskTitle }"
							data-action="snooze"
							data-target="snooze"
							title="${ progressPlannerSuggestedTask.i18n.snooze }"
						>
							<span class="dashicons dashicons-clock"></span>
							<span class="screen-reader-text">${ progressPlannerSuggestedTask.i18n.snooze }</span>
						</button>

						<div class="prpl-suggested-task-snooze prpl-tooltip">

							<fieldset>
								<legend>
									<span>
										${ progressPlannerSuggestedTask.i18n.snoozeThisTask }
									</span>
									<button type="button" class="prpl-toggle-radio-group">
										<span class="prpl-toggle-radio-group-text">
											${ progressPlannerSuggestedTask.i18n.howLong }
										</span>
										<span class="prpl-toggle-radio-group-arrow">
											&rsaquo;
										</span>
									</button>
								</legend>

								<div class="prpl-snooze-duration-radio-group">
									<label>
										<input type="radio" name="snooze-duration-${ taskId }" value="1-week">
										${ progressPlannerSuggestedTask.i18n.snoozeDuration.oneWeek }
									</label>
									<label>
										<input type="radio" name="snooze-duration-${ taskId }" value="1-month">
										${ progressPlannerSuggestedTask.i18n.snoozeDuration.oneMonth }
									</label>
									<label>
										<input type="radio" name="snooze-duration-${ taskId }" value="3-months">
										${ progressPlannerSuggestedTask.i18n.snoozeDuration.threeMonths }
									</label>
									<label>
										<input type="radio" name="snooze-duration-${ taskId }" value="6-months">
										${ progressPlannerSuggestedTask.i18n.snoozeDuration.sixMonths }
									</label>
									<label>
										<input type="radio" name="snooze-duration-${ taskId }" value="1-year">
										${ progressPlannerSuggestedTask.i18n.snoozeDuration.oneYear }
									</label>
									<label>
										<input type="radio" name="snooze-duration-${ taskId }" value="forever">
										${ progressPlannerSuggestedTask.i18n.snoozeDuration.forever }
									</label>
								</div>
							</fieldset>

							<button type="button" class="prpl-suggested-task-button prpl-tooltip-close" data-action="close-snooze" data-target="snooze">
								<span class="dashicons dashicons-no-alt"></span>
								<span class="screen-reader-text">${ progressPlannerSuggestedTask.i18n.close }</span>
							</button>
						</div>
						<div class="prpl-suggested-task-info prpl-tooltip" data-target="info">
							${ taskDescription }
							<button type="button" class="prpl-suggested-task-button prpl-tooltip-close" data-action="close-info" data-target="info">
								<span class="dashicons dashicons-no-alt"></span>
								<span class="screen-reader-text">${ progressPlannerSuggestedTask.i18n.close }</span>
							</button>
						</div>
					</div>
					<span class="prpl-suggested-task-points">
						+${ taskPoints }
					</span>
				</div>
			</li>`;

			this.taskListeners();
		}

		/**
		 * Add listeners to the item.
		 */
		taskListeners = () => {
			const thisObj = this,
				item = thisObj.querySelector( 'li' );

			item.querySelectorAll( '.prpl-suggested-task-button' ).forEach(
				function ( button ) {
					button.addEventListener( 'click', function () {
						let action = button.getAttribute( 'data-action' );
						const target = button.getAttribute( 'data-target' ),
							tooltipActions =
								item.querySelector( '.tooltip-actions' );

						// If the tooltip was already open, close it.
						if (
							!! tooltipActions.querySelector(
								'.prpl-suggested-task-' +
									target +
									'[data-tooltip-visible]'
							)
						) {
							action = 'close-' + target;
						} else {
							// Close the any opened radio group.
							item.closest( '.prpl-suggested-tasks-list' )
								.querySelector( `[data-tooltip-visible]` )
								?.classList.remove(
									'prpl-toggle-radio-group-open'
								);
							// Remove any existing tooltip visible attribute, in the entire list.
							item.closest( '.prpl-suggested-tasks-list' )
								.querySelector( `[data-tooltip-visible]` )
								?.removeAttribute( 'data-tooltip-visible' );
						}

						switch ( action ) {
							case 'snooze':
								tooltipActions
									.querySelector(
										'.prpl-suggested-task-' + target
									)
									.setAttribute(
										'data-tooltip-visible',
										'true'
									);
								break;

							case 'close-snooze':
								// Close the radio group.
								tooltipActions
									.querySelector(
										'.prpl-suggested-task-' +
											target +
											'.prpl-toggle-radio-group-open'
									)
									?.classList.remove(
										'prpl-toggle-radio-group-open'
									);
								// Close the tooltip.
								tooltipActions
									.querySelector(
										'.prpl-suggested-task-' +
											target +
											'[data-tooltip-visible]'
									)
									?.removeAttribute( 'data-tooltip-visible' );
								break;

							case 'info':
								tooltipActions
									.querySelector(
										'.prpl-suggested-task-' + target
									)
									.setAttribute(
										'data-tooltip-visible',
										'true'
									);
								break;

							case 'close-info':
								tooltipActions
									.querySelector(
										'.prpl-suggested-task-' + target
									)
									.removeAttribute( 'data-tooltip-visible' );
								break;
						}
					} );
				}
			);

			// Toggle snooze duration radio group.
			item.querySelector( '.prpl-toggle-radio-group' ).addEventListener(
				'click',
				function () {
					this.closest(
						'.prpl-suggested-task-snooze'
					).classList.toggle( 'prpl-toggle-radio-group-open' );
				}
			);

			// Handle snooze duration radio group change.
			item.querySelectorAll(
				'.prpl-snooze-duration-radio-group input[type="radio"]'
			).forEach( ( radioElement ) => {
				radioElement.addEventListener( 'change', function () {
					thisObj.snoozeTask(
						item.getAttribute( 'data-task-id' ),
						this.value
					);
				} );
			} );
		};

		/**
		 * Snooze a task.
		 *
		 * @param {string} taskId   The task ID.
		 * @param {string} duration The duration to snooze the task for.
		 */
		snoozeTask = ( taskId, duration ) => {
			taskId = taskId.toString();
			// Save the todo list to the database
			const request = wp.ajax.post(
				'progress_planner_suggested_task_action',
				{
					task_id: taskId,
					nonce: progressPlannerSuggestedTask.nonce,
					action_type: 'snooze',
					duration,
				}
			);
			request.done( () => {
				const el = document.querySelector(
					`.prpl-suggested-task[data-task-id="${ taskId }"]`
				);

				if ( el ) {
					el.remove();
				}

				// Update the global var.
				if (
					window.progressPlannerSuggestedTasks.tasks.snoozed.indexOf(
						taskId
					) === -1
				) {
					window.progressPlannerSuggestedTasks.tasks.snoozed.push(
						taskId
					);
				}

				const event = new Event( 'prplMaybeInjectSuggestedTaskEvent' );
				document.dispatchEvent( event );
			} );
		};
	}
);
