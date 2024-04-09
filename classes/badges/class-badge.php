<?php
/**
 * Badge object.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Badges;

use ProgressPlanner\Badges;
use ProgressPlanner\Settings;

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
				'icons-svg'         => $this->get_icons_svg(),
				'progress_callback' => [ $this, 'progress_callback' ],
			]
		);
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
	 * Get the badge icons.
	 *
	 * @return array
	 */
	abstract public function get_icons_svg();

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
		return Settings::get( [ 'badges', $this->id ], [] );
	}

	/**
	 * Save the progress.
	 *
	 * @param array $progress The progress to save.
	 *
	 * @return void
	 */
	protected function save_progress( $progress ) {
		$progress['date'] = ( new \DateTime() )->format( 'Y-m-d H:i:s' );
		Settings::set( [ 'badges', $this->id ], $progress );
	}
}
