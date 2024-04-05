<?php
/**
 * Badge object.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Badges\Badge;

use ProgressPlanner\Base;
use ProgressPlanner\Badges\Badge_Content;
use ProgressPlanner\Activities\Content_Helpers;

/**
 * Badge class.
 */
final class Wonderful_Writer extends Badge_Content {

	/**
	 * The badge ID.
	 *
	 * @var string
	 */
	protected $id = 'wonderful-writer';

	/**
	 * The badge name.
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'Wonderful Writer', 'progress-planner' );
	}

	/**
	 * The badge icons.
	 *
	 * @return array
	 */
	public function get_icons_svg() {
		return [
			'pending'  => [
				'path' => \PROGRESS_PLANNER_DIR . '/assets/images/badges/writing_badge1_gray.svg',
				'url'  => \PROGRESS_PLANNER_URL . '/assets/images/badges/writing_badge1_gray.svg',
			],
			'complete' => [
				'path' => \PROGRESS_PLANNER_DIR . '/assets/images/badges/writing_badge1.svg',
				'url'  => \PROGRESS_PLANNER_URL . '/assets/images/badges/writing_badge1.svg',
			],
		];
	}

	/**
	 * Progress callback.
	 *
	 * @return array
	 */
	public function progress_callback() {
		// Get the saved progress.
		$saved_progress = $this->get_saved();

		// If we have a saved value, return it.
		if ( isset( $saved_progress['progress'] ) && isset( $saved_progress['remaining'] ) ) {
			return $saved_progress;
		}

		// Get the total number of posts.
		$total_posts_count = 0;
		foreach ( Content_Helpers::get_post_types_names() as $post_type ) {
			$total_posts_count += \wp_count_posts( $post_type )->publish;
		}

		$remaining = 200 - min( 200, $total_posts_count );

		// If there are 200 existing posts, save the badge as complete and return.
		if ( 0 === $remaining ) {
			$this->save_progress(
				[
					'progress'  => 100,
					'remaining' => 0,
				]
			);

			return [
				'progress'  => 100,
				'remaining' => 0,
			];
		}

		// Get the new posts count.
		$new_count = count(
			\progress_planner()->get_query()->query_activities(
				[
					'category'   => 'content',
					'type'       => 'publish',
					'start_date' => Base::get_activation_date(),
				]
			)
		);

		$remaining_new = 10 - min( 10, $new_count );

		$final_percent   = max(
			min( 100, floor( $total_posts_count / 2 ) ),
			min( 100, floor( $new_count * 10 ) )
		);
		$final_remaining = min( $remaining, $remaining_new );

		$this->save_progress(
			[
				'progress'  => $final_percent,
				'remaining' => $final_remaining,
			]
		);

		return [
			'progress'  => $final_percent,
			'remaining' => $final_remaining,
		];
	}
}
