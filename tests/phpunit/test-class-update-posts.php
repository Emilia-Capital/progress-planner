<?php
/**
 * Class Update_Posts_Test
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Tests;

use Progress_Planner\Suggested_Tasks\Local_Tasks\Update_Posts;

/**
 * Update posts test case.
 */
class Update_Posts_Test extends \WP_UnitTestCase {

	/**
	 * Update posts object.
	 *
	 * @var Update_Posts
	 */
	protected $update_posts;

	/**
	 * Setup the test case.
	 *
	 * @return void
	 */
	public function set_up() {
		parent::set_up();

		$this->update_posts = new Update_Posts();
	}

	/**
	 * Test transforming task data to task id and back.
	 *
	 * @return void
	 */
	public function test_task_id_data() {

		$task_data = [
			[
				'type'    => 'create-post',
				'date'    => \gmdate( 'YW' ),
				'post_id' => 1,
				'long'    => false,
			],
			[
				'type'    => 'create-post',
				'date'    => \gmdate( 'YW' ),
				'post_id' => 2,
				'long'    => true,
			],
		];

		foreach ( $task_data as $data ) {
			$task_id           = $this->update_posts->get_task_id( $data );
			$task_data_from_id = $this->update_posts::get_data_from_task_id( $task_id );

			$this->assertEquals( $data, $task_data_from_id );
		}
	}
}
