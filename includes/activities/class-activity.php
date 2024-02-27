<?php
/**
 * Activity class.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Activities;

/**
 * Activity class.
 */
class Activity {

	/**
	 * Category of the activity.
	 *
	 * @var string
	 */
	protected $category;

	/**
	 * Type of the activity.
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * The date of the activity.
	 *
	 * @var \DateTime
	 */
	protected $date;

	/**
	 * The data ID.
	 *
	 * Depending on the activity this is the post-ID, term-ID, comment-ID etc.
	 *
	 * @var int
	 */
	protected $data_id;

	/**
	 * The data of the activity.
	 *
	 * @var array
	 */
	protected $data = [];

	/**
	 * ID of the activity.
	 *
	 * @var int
	 */
	protected $id;

	/**
	 * Set the ID of the activity.
	 *
	 * @param int $id The ID of the activity.
	 *
	 * @return void
	 */
	public function set_id( int $id ) {
		$this->id = $id;
	}

	/**
	 * Get the ID of the activity.
	 *
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Set the date.
	 *
	 * @param \DateTime $date The date of the activity.
	 */
	public function set_date( \DateTime $date ) {
		$this->date = $date;
	}

	/**
	 * Get the date of the activity.
	 *
	 * @return \DateTime
	 */
	public function get_date() {
		return $this->date;
	}

	/**
	 * Set the category.
	 *
	 * @param string $category The category of the activity.
	 */
	public function set_category( string $category ) {
		$this->category = $category;
	}

	/**
	 * Get the category of the activity.
	 *
	 * @return string
	 */
	public function get_category() {
		return $this->category;
	}

	/**
	 * Set the type.
	 *
	 * @param string $type The type of the activity.
	 */
	public function set_type( string $type ) {
		$this->type = $type;
	}

	/**
	 * Get the type of the activity.
	 *
	 * @return string
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Set the data ID.
	 *
	 * @param int $data_id The data ID.
	 */
	public function set_data_id( int $data_id ) {
		$this->data_id = $data_id;
	}

	/**
	 * Get the data ID.
	 *
	 * @return int
	 */
	public function get_data_id() {
		return $this->data_id;
	}

	/**
	 * Set the data of the activity.
	 *
	 * @param array $data The data of the activity.
	 */
	public function set_data( array $data ) {
		$this->data = $data;
	}

	/**
	 * Get the data of the activity.
	 *
	 * @return array
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Save the activity.
	 *
	 * @return void
	 */
	public function save() {
		$existing = Query::get_instance()->query_activities(
			[
				'category' => $this->category,
				'type'     => $this->type,
				'data_id'  => $this->data_id,
			]
		);
		if ( ! empty( $existing ) ) {
			Query::get_instance()->update_activity( $existing[0]->id, $this );
		} else {
			Query::get_instance()->insert_activity( $this );
		}
	}

	/**
	 * Delete the activity.
	 *
	 * @return void
	 */
	public function delete() {
		Query::get_instance()->delete_activity( $this );
	}
}
