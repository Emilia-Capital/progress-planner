<?php
/**
 * Class Date_Test
 *
 * @package FewerTags
 */

namespace ProgressPlanner\Tests;

use ProgressPlanner\Activity;
use ProgressPlanner\Query;

/**
 * Date test case.
 */
class Activity_Test extends \WP_UnitTestCase {

	/**
	 * Activity object.
	 *
	 * @var Activity
	 */
	protected $activity;

	/**
	 * Setup the test case.
	 *
	 * @return void
	 */
	public function set_up() {
		parent::set_up();

		$this->activity = new Activity();
		$this->activity->set_category( 'test_category' );
		$this->activity->set_type( 'test_type' );
		$this->activity->set_date( new \DateTime() );
		$this->activity->set_data_id( 100 );
		$this->activity->set_user_id( 1 );
	}

	/**
	 * Test the get_category method.
	 *
	 * @return void
	 */
	public function test_get_category() {
		$this->assertEquals( 'test_category', $this->activity->get_category() );
	}

	/**
	 * Test the get_type method.
	 *
	 * @return void
	 */
	public function test_get_type() {
		$this->assertEquals( 'test_type', $this->activity->get_type() );
	}

	/**
	 * Test the get_date method.
	 *
	 * @return void
	 */
	public function test_get_date() {
		$this->assertInstanceOf( \DateTime::class, $this->activity->get_date() );
	}

	/**
	 * Test the get_data_id method.
	 *
	 * @return void
	 */
	public function test_get_data_id() {
		$this->assertEquals( 100, $this->activity->get_data_id() );
	}

	/**
	 * Test the get_user_id method.
	 *
	 * @return void
	 */
	public function test_get_user_id() {
		$this->assertEquals( 1, $this->activity->get_user_id() );
	}

	/**
	 * Test saving the activity.
	 *
	 * @return void
	 */
	public function test_save() {
		$this->activity->save();

		$activity = \progress_planner()->get_query()->query_activities(
			[
				'category' => $this->activity->get_category(),
				'type'     => $this->activity->get_type(),
				'user_id'  => 1,
			]
		)[0];

		$this->assertEquals( $this->activity->get_category(), $activity->get_category() );
		$this->assertEquals( $this->activity->get_type(), $activity->get_type() );
		$this->assertEquals( $this->activity->get_date()->format( 'Y-m-d' ), $activity->get_date()->format( 'Y-m-d' ) );
		$this->assertEquals( $this->activity->get_data_id(), $activity->get_data_id() );
		$this->assertEquals( $this->activity->get_user_id(), $activity->get_user_id() );
	}
}
