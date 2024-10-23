<?php
/**
 * Class Suggested_Tasks_Test
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Tests;

use Progress_Planner\Suggested_Tasks;
use Progress_Planner\Suggested_Tasks\API;

/**
 * Suggested_Tasks test case.
 */
class Suggested_Tasks_Test extends \WP_UnitTestCase {

	/**
	 * Suggested_Tasks object.
	 *
	 * @var Suggested_Tasks
	 */
	protected $suggested_tasks;

	/**
	 * Setup the test case.
	 *
	 * @return void
	 */
	public function set_up() {
		$this->suggested_tasks = new Suggested_Tasks();
	}

	/**
	 * Test the get_api method.
	 *
	 * @return void
	 */
	public function test_get_api() {
		$api = $this->suggested_tasks->get_api();
		$this->assertInstanceOf( API::class, $api );
	}

	/**
	 * Test the mark_task_as_completed method.
	 *
	 * @return void
	 */
	public function test_mark_task_as_completed() {
	}

	/**
	 * Test the get_pending_celebration method.
	 *
	 * @return void
	 */
	public function test_get_pending_celebration() {
	}

	/**
	 * Test the mark_task_as_pending_celebration method.
	 *
	 * @return void
	 */
	public function test_mark_task_as_pending_celebration() {
	}

	/**
	 * Test the mark_task_as_celebrated method.
	 *
	 * @return void
	 */
	public function test_mark_task_as_celebrated() {
	}

	/**
	 * Test the maybe_celebrate_tasks method.
	 *
	 * @return void
	 */
	public function test_maybe_celebrate_tasks() {
	}

	/**
	 * Test the mark_task_as_snoozed method.
	 *
	 * @return void
	 */
	public function test_mark_task_as_snoozed() {
	}

	/**
	 * Test the maybe_unsnooze_tasks method.
	 *
	 * @return void
	 */
	public function test_maybe_unsnooze_tasks() {
	}
}
