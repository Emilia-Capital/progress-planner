<?php
/**
 * Goals class.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

/**
 * Goals class.
 *
 * This is a collection of individual Goal objects.
 */
class Goals {

	/**
	 * The individual goals.
	 *
	 * @var array
	 */
	private $goals = [];

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->register_goals();
	}

	/**
	 * Add a goal to the collection.
	 *
	 * @param Goal $goal The goal object.
	 */
	public function add_goal( $goal ) {
		$this->goals[] = $goal;
	}

	/**
	 * Get all goals.
	 *
	 * @return array
	 */
	public function get_all_goals() {
		return $this->goals;
	}

	/**
	 * Get an individual goal.
	 *
	 * @param string $id The ID of the goal.
	 * @return Goal
	 */
	public function get_goal( $id ) {
		foreach ( $this->goals as $goal ) {
			if ( $id === $goal->get_details()['id'] ) {
				return $goal;
			}
		}
		return new Goals\Goal();
	}

	/**
	 * Register the individual goals.
	 */
	private function register_goals() {
		$this->add_goal(
			new Goals\Goal(
				[
					'id'          => 'weekly_post',
					'title'       => esc_html__( 'Write a weekly blog post', 'progress-planner' ),
					'description' => '',
					'type'        => 'post',
					'frequency'   => 'weekly',
					'priority'    => 'high',
				]
			)
		);
	}
}
