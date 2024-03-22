<?php
/**
 * Badge object.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Badges;

use ProgressPlanner\Badges;

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
	 * The badge category.
	 *
	 * @var string
	 */
	protected $category;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->register_badge();
	}

	/**
	 * Register the badge.
	 */
	public function register_badge() {
		Badges::register_badge(
			$this->id,
			[
				'category'          => $this->category,
				'name'              => $this->get_name(),
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
	 * Get the badge icons.
	 *
	 * @return array
	 */
	abstract public function get_icons_svg();

	/**
	 * Progress callback.
	 */
	abstract public function progress_callback();
}
