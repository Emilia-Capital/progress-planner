<?php
/**
 * Badge object.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Badges;

use ProgressPlanner\Base;
use ProgressPlanner\Settings;

/**
 * Badge class.
 */
class Content_Notorious_Novelist extends Badge {

	/**
	 * The badge ID.
	 *
	 * @var string
	 */
	protected $id = 'notorious-novelist';

	/**
	 * The badge category.
	 *
	 * @var string
	 */
	protected $category = 'content_writing';

	/**
	 * The badge name.
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'Notorious Novelist', 'progress-planner' );
	}

	/**
	 * The badge icons.
	 *
	 * @return array
	 */
	public function get_icons_svg() {
		return [
			'pending'  => [
				'path' => \PROGRESS_PLANNER_DIR . '/assets/images/badges/writing_badge3_gray.svg',
				'url'  => \PROGRESS_PLANNER_URL . '/assets/images/badges/writing_badge3_gray.svg',
			],
			'complete' => [
				'path' => \PROGRESS_PLANNER_DIR . '/assets/images/badges/writing_badge3.svg',
				'url'  => \PROGRESS_PLANNER_URL . '/assets/images/badges/writing_badge3.svg',
			],
		];
	}

	/**
	 * Progress callback.
	 */
	public function progress_callback() {
		$saved_progress = (int) Settings::get( [ 'badges', 'notorious-novelist' ], [] );

		// If the badge is already complete, return 100% progress.
		if ( isset( $saved_progress['progress'] ) && 100 === $saved_progress ) {
			return [
				'percent'   => 100,
				'remaining' => 0,
			];
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

		$percent   = min( 100, floor( 100 * $new_count / 50 ) );
		$remaining = 50 - min( 50, $new_count );

		// If the user has published 50 new posts, save the badge as complete and return.
		if ( 0 === $remaining ) {
			Settings::set(
				[ 'badges', 'notorious-novelist' ],
				[
					'progress' => 100,
					'date'     => ( new \DateTime() )->format( 'Y-m-d H:i:s' ),
				]
			);
		}

		return [
			'percent'   => $percent,
			'remaining' => $remaining,
		];
	}
}
