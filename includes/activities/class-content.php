<?php
/**
 * Handler for posts activities.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Activities;

use ProgressPlanner\Activity;
use ProgressPlanner\Date;
use ProgressPlanner\Activities\Content_Helpers;

/**
 * Handler for posts activities.
 */
class Content extends Activity {

	/**
	 * The points awarded for each activity.
	 *
	 * @var array
	 */
	const ACTIVITIES_POINTS = [
		'publish' => 50,
		'update'  => 10,
		'delete'  => 5,
		'comment' => 2,
	];

	/**
	 * Category of the activity.
	 *
	 * @var string
	 */
	protected $category = 'content';

	/**
	 * Get WP_Post from the activity.
	 *
	 * @return \WP_Post
	 */
	public function get_post() {
		return \get_post( $this->data_id );
	}
}
