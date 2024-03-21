<?php
/**
 * The "Wonderful Writer" badge.
 *
 * This badge is awarded when the user has published 10 new posts,
 * or has a total of 200 published posts.
 *
 * @package ProgressPlanner
 */

use ProgressPlanner\Base;
use ProgressPlanner\Settings;
use ProgressPlanner\Badges;
use ProgressPlanner\Activities\Content_Helpers;

Badges::register_badge(
	'wonderful-writer',
	[
		'category'          => 'content_writing',
		'name'              => __( 'Wonderful Writer', 'progress-planner' ),
		'icons-svg'         => [
			'pending'  => [
				'path' => \PROGRESS_PLANNER_DIR . '/assets/images/badges/writing_badge1_gray.svg',
				'url'  => \PROGRESS_PLANNER_URL . '/assets/images/badges/writing_badge1_gray.svg',
			],
			'complete' => [
				'path' => \PROGRESS_PLANNER_DIR . '/assets/images/badges/writing_badge1.svg',
				'url'  => \PROGRESS_PLANNER_URL . '/assets/images/badges/writing_badge1.svg',
			],
		],
		'progress_callback' => function () {
			// Get the saved progress.
			$saved_progress = Settings::get( [ 'badges', 'wonderful-writer' ], [] );

			// If the badge is already complete, return 100% progress.
			if ( isset( $saved_progress['progress'] ) && 100 === (int) $saved_progress['progress'] ) {
				return [
					'percent'   => 100,
					'remaining' => 0,
				];
			}

			// Get the total number of posts.
			$total_posts_count = 0;
			foreach ( Content_Helpers::get_post_types_names() as $post_type ) {
				$total_posts_count += \wp_count_posts( $post_type )->publish;
			}

			$remaining = 200 - min( 200, $total_posts_count );

			// If there are 200 existing posts, save the badge as complete and return.
			if ( 0 === $remaining ) {
				Settings::set(
					[ 'badges', 'wonderful-writer' ],
					[
						'progress' => 100,
						'date'     => ( new \DateTime() )->format( 'Y-m-d H:i:s' ),
					]
				);

				return [
					'percent'   => 100,
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

			// If there are 10 new posts, save the badge as complete and return.
			$remaining_new = 10 - min( 10, $new_count );
			if ( 0 === $remaining_new ) {
				Settings::set(
					[ 'badges', 'content_writing', 'wonderful-writer' ],
					[
						'progress' => 100,
						'date'     => ( new \DateTime() )->format( 'Y-m-d H:i:s' ),
					]
				);
				return [
					'percent'   => 100,
					'remaining' => 0,
				];
			}

			return [
				'percent'   => max(
					max( 100, floor( $total_posts_count / 2 ) ),
					max( 100, floor( $new_count * 10 ) )
				),
				'remaining' => min( $remaining, $remaining_new ),
			];
		},
	]
);
