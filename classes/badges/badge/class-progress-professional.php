<?php
/**
 * Badge object.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Badges\Badge;

use ProgressPlanner\Badges\Badge_Maintenance;

/**
 * Badge class.
 */
final class Progress_Professional extends Badge_Maintenance {

	/**
	 * The badge ID.
	 *
	 * @var string
	 */
	protected $id = 'progress-professional';

	/**
	 * The badge name.
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'Progress Professional', 'progress-planner' );
	}

	/**
	 * The badge icons.
	 *
	 * @return array
	 */
	public function get_icons_svg() {
		return [
			'pending'  => [
				'path' => \PROGRESS_PLANNER_DIR . '/assets/images/badges/streak_badge1_gray.svg',
				'url'  => \PROGRESS_PLANNER_URL . '/assets/images/badges/streak_badge1_gray.svg',
			],
			'complete' => [
				'path' => \PROGRESS_PLANNER_DIR . '/assets/images/badges/streak_badge1.svg',
				'url'  => \PROGRESS_PLANNER_URL . '/assets/images/badges/streak_badge1.svg',
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

		$max_streak = $this->get_goal()->get_streak()['max_streak'];
		$percent    = min( 100, floor( 100 * $max_streak / 6 ) );
		$remaining  = 6 - min( 6, $max_streak );

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