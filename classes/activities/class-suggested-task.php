<?php
/**
 * Handler for posts activities.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Activities;

use Progress_Planner\Query;
use Progress_Planner\Activity;

/**
 * Handler for posts activities.
 */
class Suggested_Task extends Activity {

	/**
	 * Category of the activity.
	 *
	 * @var string
	 */
	public $category = 'suggested_task';

	/**
	 * Save the activity.
	 *
	 * @return void
	 */
	public function save() {
		$this->date    = new \DateTime();
		$this->user_id = \get_current_user_id();

		Query::get_instance()->insert_activity( $this );
		\do_action( 'progress_planner_activity_saved', $this );
	}
}
