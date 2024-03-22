<?php
/**
 * Badge object.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Badges\Badge;

use ProgressPlanner\Settings;
use ProgressPlanner\Badges\Badge_Maintenance;


/**
 * Badge class.
 */
final class Maintenance_Maniac extends Badge_Maintenance {

	/**
	 * The badge ID.
	 *
	 * @var string
	 */
	protected $id = 'maintenance-maniac';

	/**
	 * The badge name.
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'Maintenance Maniac', 'progress-planner' );
	}

	/**
	 * The badge icons.
	 *
	 * @return array
	 */
	public function get_icons_svg() {
		return [
			'pending'  => [
				'path' => \PROGRESS_PLANNER_DIR . '/assets/images/badges/streak_badge2_gray.svg',
				'url'  => \PROGRESS_PLANNER_URL . '/assets/images/badges/streak_badge2_gray.svg',
			],
			'complete' => [
				'path' => \PROGRESS_PLANNER_DIR . '/assets/images/badges/streak_badge2.svg',
				'url'  => \PROGRESS_PLANNER_URL . '/assets/images/badges/streak_badge2.svg',
			],
		];
	}

	/**
	 * Progress callback.
	 */
	public function progress_callback() {
		$saved_progress = (int) Settings::get( [ 'badges', 'maintenance-maniac' ], [] );

		// If the badge is already complete, return 100% progress.
		if ( isset( $saved_progress['progress'] ) && 100 === $saved_progress ) {
			return [
				'percent'   => 100,
				'remaining' => 0,
			];
		}

		// In order to avoid querying the database every time, we save the progress and date.
		// This works as a cache for the progress, and will get updated every 2 days.
		$use_saved = false;
		if ( isset( $saved_progress['date'] ) ) {
			$last_date = new \DateTime( $saved_progress['date'] );
			$diff      = $last_date->diff( new \DateTime() );
			if ( $diff->days <= 2 ) {
				$use_saved = true;
			}
		}

		// If we're using the saved value, return it.
		if ( $use_saved ) {
			return [
				'percent'   => $saved_progress['progress'],
				'remaining' => $saved_progress['remaining'],
			];
		}

		$max_streak = $this->get_goal()->get_streak()['max_streak'];
		$percent    = min( 100, floor( 100 * $max_streak / 26 ) );
		$remaining  = 26 - min( 26, $max_streak );

		// Update the saved value.
		Settings::set(
			[ 'badges', 'maintenance-maniac' ],
			[
				'progress'  => $percent,
				'remaining' => $remaining,
				'date'      => ( new \DateTime() )->format( 'Y-m-d H:i:s' ),
			]
		);

		return [
			'percent'   => $percent,
			'remaining' => $remaining,
		];
	}
}
