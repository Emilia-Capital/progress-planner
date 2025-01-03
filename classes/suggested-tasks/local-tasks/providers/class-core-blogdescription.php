<?php
/**
 * Add tasks for Core blogdescription.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Suggested_Tasks\Local_Tasks\Providers;

/**
 * Add tasks for Core blogdescription.
 */
class Core_Blogdescription extends Local_Tasks_Abstract {

	/**
	 * The provider ID.
	 *
	 * @var string
	 */
	const TYPE = 'core-blogdescription';

	/**
	 * Get the provider ID.
	 *
	 * @return string
	 */
	public function get_provider_type() {
		return self::TYPE;
	}

	/**
	 * Evaluate a task.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return bool|string
	 */
	public function evaluate_task( $task_id ) {

		// Early bail if the user does not have the capability to manage options.
		if ( ! $this->capability_required() ) {
			return false;
		}

		if ( 0 === strpos( $task_id, self::TYPE ) && '' !== \get_bloginfo( 'description' ) ) {
			return $task_id;
		}
		return false;
	}

	/**
	 * Get an array of tasks to inject.
	 *
	 * @return array
	 */
	public function get_tasks_to_inject() {

		// Early bail if the user does not have the capability to manage options or if the task is snoozed.
		if ( true === $this->is_task_type_snoozed() || ! $this->capability_required() ) {
			return [];
		}

		// If tagline is set, do not add the task.
		if ( '' !== \get_bloginfo( 'description' ) ) {
			return [];
		}

		return [
			$this->get_task_details( self::TYPE ),
		];
	}

	/**
	 * Get the task details.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return array
	 */
	public function get_task_details( $task_id ) {

		return [
			'task_id'     => $task_id,
			'title'       => \esc_html__( 'Set Tagline', 'progress-planner' ),
			'parent'      => 0,
			'priority'    => 'high',
			'type'        => 'maintenance',
			'points'      => 1,
			'url'         => $this->capability_required() ? \esc_url( \admin_url( 'options-general.php' ) ) : '',
			'description' => '<p>' . \esc_html__( 'Set the tagline to make your website look more professional.', 'progress-planner' ) . '</p>',
		];
	}

	/**
	 * Get the data from a task-ID.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return array The data.
	 */
	public function get_data_from_task_id( $task_id ) {
		$data = [
			'type' => self::TYPE,
			'id'   => $task_id,
		];

		return $data;
	}

	/**
	 * Check if a task type is snoozed.
	 *
	 * @return bool
	 */
	public function is_task_type_snoozed() {
		$snoozed = \progress_planner()->get_suggested_tasks()->get_snoozed_tasks();
		if ( ! \is_array( $snoozed ) || empty( $snoozed ) ) {
			return false;
		}

		foreach ( $snoozed as $task ) {
			if ( self::TYPE === $task['id'] ) {
				return true;
			}
		}

		return false;
	}
}
