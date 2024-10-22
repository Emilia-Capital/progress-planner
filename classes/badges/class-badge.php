<?php
/**
 * Badge object.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Badges;

use Progress_Planner\Badges;

/**
 * Badge class.
 */
abstract class Badge {

	/**
	 * The badge ID.
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->register_badge();
	}

	/**
	 * Register the badge.
	 *
	 * @return void
	 */
	public function register_badge() {
		Badges::register_badge(
			$this->id,
			[
				'name'              => $this->get_name(),
				'description'       => $this->get_description(),
				'progress_callback' => [ $this, 'progress_callback' ],
			]
		);
	}

	/**
	 * Get the badge ID.
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get the badge name.
	 *
	 * @return string
	 */
	abstract public function get_name();

	/**
	 * Get the badge description.
	 *
	 * @return string
	 */
	abstract public function get_description();

	/**
	 * Progress callback.
	 *
	 * @return array
	 */
	abstract public function progress_callback();

	/**
	 * Get the saved progress.
	 *
	 * @return array
	 */
	protected function get_saved() {
		global $progress_planner;
		return $progress_planner->get_settings()->get( [ 'badges', $this->id ], [] );
	}

	/**
	 * Save the progress.
	 *
	 * @param array $progress The progress to save.
	 *
	 * @return void
	 */
	protected function save_progress( $progress ) {
		global $progress_planner;
		$progress['date'] = ( new \DateTime() )->format( 'Y-m-d H:i:s' );
		$progress_planner->get_settings()->set( [ 'badges', $this->id ], $progress );
	}
}
