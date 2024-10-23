<?php
/**
 * Class Todo_Test
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Tests;

use Progress_Planner\Todo;

/**
 * Todo test case.
 */
class Todo_Test extends \WP_UnitTestCase {

	/**
	 * Todo object.
	 *
	 * @var Todo
	 */
	protected $todo;

	/**
	 * Setup the test case.
	 *
	 * @return void
	 */
	public function set_up() {
		$this->todo = new Todo();
	}

	/**
	 * Test get_items method.
	 *
	 * @return void
	 */
	public function test_get_items() {
		$items = $this->todo->get_items();
		$this->assertIsArray( $items );
		$this->assertEmpty( $items );
	}
}
