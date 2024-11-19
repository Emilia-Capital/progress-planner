/* global customElements, HTMLElement,  */

/**
 * Suggested task.
 */
customElements.define(
	'prpl-suggested-task',
	class extends HTMLElement {
		constructor( taskId, taskTitle, taskDescription, taskPoints ) {
			// Get parent class properties
			super();

			this.innerHTML = `
			<li class="prpl-suggested-task" data-task-id="${ taskId }">
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
		}
	}
);
