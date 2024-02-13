<?php
/**
 * An object containing info about an individual goal.
 *
 * This object is meant to be extended by individual goal classes.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Goals;

/**
 * An object containing info about an individual goal.
 */
class Goal {

	/**
	 * The goal ID.
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * The goal title.
	 *
	 * @var string
	 */
	protected $title;

	/**
	 * The goal description.
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * The goal type.
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * The goal frequency.
	 *
	 * @var string
	 */
	protected $frequency;

	/**
	 * The goal start date.
	 *
	 * @var string
	 */
	protected $start_date;

	/**
	 * The goal end date.
	 *
	 * @var string
	 */
	protected $end_date;

	/**
	 * The goal status.
	 *
	 * @var string
	 */
	protected $status;

	/**
	 * The goal priority.
	 *
	 * @var string
	 */
	protected $priority;

	/**
	 * The goal progress.
	 *
	 * @var string
	 */
	protected $progress;

	/**
	 * Constructor.
	 *
	 * @param array $args The goal arguments.
	 */
	public function __construct( $args = [] ) {
		$args              = wp_parse_args(
			$args,
			[
				'id'          => '',
				'title'       => '',
				'description' => '',
				'type'        => '',
				'frequency'   => '',
				'start_date'  => '',
				'end_date'    => '',
				'status'      => '',
				'priority'    => '',
				'progress'    => '',
			]
		);
		$this->id          = $args['id'];
		$this->title       = $args['title'];
		$this->description = $args['description'];
		$this->type        = $args['type'];
		$this->frequency   = $args['frequency'];
		$this->start_date  = $args['start_date'];
		$this->end_date    = $args['end_date'];
		$this->status      = $args['status'];
		$this->priority    = $args['priority'];
		$this->progress    = $args['progress'];
	}

	/**
	 * Get the goal ID.
	 *
	 * @return string
	 */
	public function get_details() {
		return [
			'id'          => $this->id,
			'title'       => $this->title,
			'description' => $this->description,
			'type'        => $this->type,
			'frequency'   => $this->frequency,
			'start_date'  => $this->start_date,
			'end_date'    => $this->end_date,
			'status'      => $this->status,
			'priority'    => $this->priority,
			'progress'    => $this->progress,
		];
	}
}
