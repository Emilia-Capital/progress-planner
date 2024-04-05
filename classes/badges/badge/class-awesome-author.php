<?php
/**
 * Badge object.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Badges\Badge;

use ProgressPlanner\Base;
use ProgressPlanner\Badges\Badge_Content;

/**
 * Badge class.
 */
final class Awesome_Author extends Badge_Content {

	/**
	 * The badge ID.
	 *
	 * @var string
	 */
	protected $id = 'awesome-author';

	/**
	 * The badge name.
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'Awesome Author', 'progress-planner' );
	}

	/**
	 * The badge icons.
	 *
	 * @return array
	 */
	public function get_icons_svg() {
		return [
			'pending'  => [
				'path' => \PROGRESS_PLANNER_DIR . '/assets/images/badges/writing_badge2_gray.svg',
				'url'  => \PROGRESS_PLANNER_URL . '/assets/images/badges/writing_badge2_gray.svg',
			],
			'complete' => [
				'path' => \PROGRESS_PLANNER_DIR . '/assets/images/badges/writing_badge2.svg',
				'url'  => \PROGRESS_PLANNER_URL . '/assets/images/badges/writing_badge2.svg',
			],
		];
	}

	/**
	 * Progress callback.
	 *
	 * @return array
	 */
	public function progress_callback() {
		$saved_progress = $this->get_saved();

		// If we have a saved value, return it.
		if ( isset( $saved_progress['progress'] ) && isset( $saved_progress['remaining'] ) ) {
			return $saved_progress;
		}

		// Get the number of new posts published.
		$new_count = count(
			\progress_planner()->get_query()->query_activities(
				[
					'category'   => 'content',
					'type'       => 'publish',
					'start_date' => Base::get_activation_date(),
				],
			)
		);

		$percent   = min( 100, floor( 100 * $new_count / 30 ) );
		$remaining = 30 - min( 30, $new_count );

		$this->save_progress(
			[
				'progress'  => $percent,
				'remaining' => $remaining,
			]
		);

		return [
			'progress'  => $percent,
			'remaining' => $remaining,
		];
	}
}