<?php
/**
 * Assets class.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Admin;

/**
 * Assets class.
 */
class Scripts {

	/**
	 * Register scripts.
	 *
	 * @return void
	 */
	public function register_scripts() {
		// Register vendor scripts.
		$vendor_scripts = [
			'driver'             => [ 'driver.js.iife.js', '1.3.1' ],
			'particles-confetti' => [ 'tsparticles.confetti.bundle.min.js', '2.11.0' ],
		];
		foreach ( $vendor_scripts as $handle => $file ) {
			\wp_register_script( $handle, PROGRESS_PLANNER_URL . "/assets/js/vendor/{$file[0]}", [], (string) $file[1], true );
		}

		// Register web components.
		foreach ( $this->get_files_in_directory( 'assets/js/web-components' ) as $file ) {
			$handle = 'progress-planner-web-components-' . $file;
			\wp_register_script(
				$handle,
				PROGRESS_PLANNER_URL . "/assets/js/web-components/{$file}.js",
				$this->get_dependencies( 'web-components/' . $file ),
				(string) filemtime( PROGRESS_PLANNER_DIR . "/assets/js/web-components/{$file}.js" ),
				true
			);
			$this->localize_script( $handle );
		}

		// Register main scripts.
		foreach ( $this->get_files_in_directory( 'assets/js' ) as $file ) {
			$handle = 'progress-planner-' . $file;
			\wp_register_script(
				$handle,
				PROGRESS_PLANNER_URL . '/assets/js/' . $file . '.js',
				$this->get_dependencies( $file ),
				(string) filemtime( PROGRESS_PLANNER_DIR . '/assets/js/' . $file . '.js' ),
				true
			);
			$this->localize_script( $handle );
		}
	}

	/**
	 * Get dependencies for a script.
	 *
	 * @param string $file The file name.
	 * @return array
	 */
	public function get_dependencies( $file ) {
		switch ( $file ) {
			case 'web-components/prpl-gauge':
				return [ 'progress-planner-web-components-prpl-badge' ];

			case 'tour':
				return [ 'driver' ];

			case 'grid-masonry':
				return [ 'progress-planner-document-ready' ];

			case 'scan-posts':
				return [ 'progress-planner-ajax-request' ];

			case 'onboard':
				return [ 'progress-planner-ajax-request', 'progress-planner-scan-posts' ];

			case 'settings':
				return [ 'progress-planner-ajax-request', 'wp-util' ];

			case 'settings-page':
				return [ 'wp-util', 'progress-planner-document-ready' ];

			case 'todo':
				return [
					'wp-util',
					'wp-a11y',
					'progress-planner-ajax-request',
					'progress-planner-grid-masonry',
					'progress-planner-web-components-prpl-todo-item',
					'progress-planner-document-ready',
				];

			case 'suggested-tasks':
				$pending_celebration = \progress_planner()->get_suggested_tasks()->get_pending_celebration();
				$deps                = [
					'progress-planner-todo',
					'progress-planner-grid-masonry',
					'progress-planner-web-components-prpl-suggested-task',
					'progress-planner-document-ready',
				];

				// Check if need to load confetti.
				if ( ! empty( $pending_celebration ) ) {
					$deps[] = 'particles-confetti';
				} else {
					// Check if there are remote tasks to inject, checking here as it might involve an API call.
					$remote_tasks = \progress_planner()->get_suggested_tasks()->get_remote_tasks();
					if ( ! empty( $remote_tasks ) ) {
						$deps[] = 'particles-confetti';
					}
				}
				return $deps;

			default:
				return [];
		}
	}

	/**
	 * Localize a script
	 *
	 * @param string $handle The script handle.
	 * @return void
	 */
	public function localize_script( $handle ) {
		switch ( $handle ) {
			case 'progress-planner-web-components-prpl-suggested-task':
				\wp_localize_script(
					$handle,
					'progressPlannerSuggestedTask',
					[
						'nonce' => \wp_create_nonce( 'progress_planner' ),
						'i18n'  => [
							'info'           => \esc_html__( 'Info', 'progress-planner' ),
							'snooze'         => \esc_html__( 'Snooze', 'progress-planner' ),
							'snoozeThisTask' => \esc_html__( 'Snooze this task?', 'progress-planner' ),
							'howLong'        => \esc_html__( 'How long?', 'progress-planner' ),
							'snoozeDuration' => [
								'oneWeek'     => \esc_html__( '1 week', 'progress-planner' ),
								'oneMonth'    => \esc_html__( '1 month', 'progress-planner' ),
								'threeMonths' => \esc_html__( '3 months', 'progress-planner' ),
								'sixMonths'   => \esc_html__( '6 months', 'progress-planner' ),
								'oneYear'     => \esc_html__( '1 year', 'progress-planner' ),
								'forever'     => \esc_html__( 'forever', 'progress-planner' ),
							],
							'close'          => \esc_html__( 'Close', 'progress-planner' ),
							'markAsComplete' => \esc_html__( 'Mark as completed', 'progress-planner' ),
						],
					]
				);
				break;

			case 'progress-planner-web-components-prpl-todo-item':
				\wp_localize_script(
					$handle,
					'progressPlannerTodoItem',
					[
						'i18n' => [
							/* translators: %s: The task content. */
							'taskDelete'       => \esc_html__( "Delete task '%s'", 'progress-planner' ),
							/* translators: %s: The task content. */
							'taskMoveUp'       => \esc_html__( "Move task '%s' up", 'progress-planner' ),
							/* translators: %s: The task content. */
							'taskMoveDown'     => \esc_html__( "Move task '%s' down", 'progress-planner' ),
							'taskMovedUp'      => \esc_html__( 'Task moved up', 'progress-planner' ),
							'taskMovedDown'    => \esc_html__( 'Task moved down', 'progress-planner' ),
							/* translators: %s: The task content. */
							'taskCompleted'    => \esc_html__( "Task '%s' completed and moved to the bottom", 'progress-planner' ),
							/* translators: %s: The task content. */
							'taskNotCompleted' => \esc_html__( "Task '%s' marked as not completed and moved to the top", 'progress-planner' ),
						],
					]
				);
				break;

			case 'progress-planner-tour':
				\wp_localize_script(
					$handle,
					'progressPlannerTour',
					[
						'steps'        => \progress_planner()->get_admin__tour()->get_steps(),
						'progressText' => sprintf(
							/* translators: %1$s: The current step number. %2$s: The total number of steps. */
							\esc_html__( 'Step %1$s of %2$s', 'progress-planner' ),
							'{{current}}',
							'{{total}}'
						),
						'nextBtnText'  => \esc_html__( 'Next &rarr;', 'progress-planner' ),
						'prevBtnText'  => \esc_html__( '&larr; Previous', 'progress-planner' ),
						'doneBtnText'  => \esc_html__( 'Finish', 'progress-planner' ),
					]
				);
				break;

			case 'progress-planner-onboard':
			case 'progress-planner-header-filters':
			case 'progress-planner-settings':
				$data = [
					'onboardNonceURL' => \progress_planner()->get_onboard()->get_remote_nonce_url(),
					'onboardAPIUrl'   => \progress_planner()->get_onboard()->get_remote_url(),
					'ajaxUrl'         => \admin_url( 'admin-ajax.php' ),
					'nonce'           => \wp_create_nonce( 'progress_planner' ),
				];
				if ( 'progress-planner-settings' === $handle ) {
					$data['l10n'] = [
						'saving'      => \esc_html__( 'Saving...', 'progress-planner' ),
						'subscribing' => \esc_html__( 'Subscribing...', 'progress-planner' ),
						'subscribed'  => \esc_html__( 'Subscribed...', 'progress-planner' ),
					];
				}
				\wp_localize_script( $handle, 'progressPlanner', $data );
				break;

			case 'progress-planner-todo':
				\wp_localize_script(
					$handle,
					'progressPlannerTodo',
					[
						'ajaxUrl'   => \admin_url( 'admin-ajax.php' ),
						'nonce'     => \wp_create_nonce( 'progress_planner_todo' ),
						'listItems' => \progress_planner()->get_todo()->get_items(),
					]
				);
				break;

			case 'progress-planner-settings-page':
				\wp_localize_script(
					$handle,
					'progressPlannerSettingsPage',
					[
						'siteUrl'    => \get_site_url(),
						'savingText' => \esc_html__( 'Saving...', 'progress-planner' ),
					]
				);
				break;

			case 'progress-planner-suggested-tasks':
				// Get all saved tasks (completed, pending celebration, snoozed).
				$tasks = \progress_planner()->get_suggested_tasks()->get_saved_tasks();

				// Get pending tasks.
				$tasks['details'] = \progress_planner()->get_suggested_tasks()->get_tasks();

				// Insert the pending celebration tasks as high priority tasks, so they are shown always.
				foreach ( $tasks['pending_celebration'] as $task_id ) {

					$task_details = \progress_planner()->get_suggested_tasks()->get_local()->get_task_details( $task_id );

					if ( $task_details ) {
						$task_details['priority'] = 'high'; // Celebrate tasks are always on top.
						$task_details['action']   = 'celebrate';
						$tasks['details'][]       = $task_details;
					}

					// Mark the pending celebration tasks as completed.
					\progress_planner()->get_suggested_tasks()->transition_task_status( $task_id, 'pending_celebration', 'completed' );
				}

				\wp_localize_script(
					$handle,
					'progressPlannerSuggestedTasks',
					[
						'ajaxUrl' => \admin_url( 'admin-ajax.php' ),
						'nonce'   => \wp_create_nonce( 'progress_planner' ),
						'tasks'   => $tasks,
					]
				);
				break;

			default:
				return;
		}
	}

	/**
	 * Get files in the assets directory.
	 *
	 * @param string $directory The directory to get files from.
	 * @param string $trim The extension to trim from the files.
	 *
	 * @return array
	 */
	public function get_files_in_directory( $directory, $trim = '.js' ) {
		$files = \glob( PROGRESS_PLANNER_DIR . '/' . $directory . '/*.js' );
		foreach ( $files as $index => $file ) { // @phpstan-ignore-line foreach.nonIterable
			$files[ $index ] = \str_replace( $trim, '', \basename( $file ) ); // @phpstan-ignore-line offsetAccess.nonOffsetAccessible
		}

		return $files;
	}
}
