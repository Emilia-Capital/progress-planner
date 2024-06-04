<?php
/**
 * Handler for posts activities.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Activities;

use Progress_Planner\Base;
use Progress_Planner\Activity;
use Progress_Planner\Date;
use Progress_Planner\Activities\Content_Helpers;

/**
 * Handler for posts activities.
 */
class Todo extends Activity {

	/**
	 * Category of the activity.
	 *
	 * @var string
	 */
	public $category = 'todo';

	/**
	 * The data ID.
	 *
	 * Depending on the activity this is the post-ID, term-ID, comment-ID etc.
	 *
	 * @var int
	 */
	public $data_id = 0;

	/**
	 * Save the activity.
	 *
	 * @return void
	 */
	public function save() {
		$this->date    = new \DateTime();
		$this->user_id = get_current_user_id();

		\progress_planner()->get_query()->insert_activity( $this );
	}
}
