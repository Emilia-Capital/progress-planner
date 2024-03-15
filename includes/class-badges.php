<?php
/**
 * Handle user badges.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

use ProgressPlanner\Goals\Goal_Recurring;
use ProgressPlanner\Goals\Goal_Posts;
use ProgressPlanner\Base;
use ProgressPlanner\Settings;

/**
 * Badges class.
 */
class Badges {

	/**
	 * Registered badges.
	 *
	 * @var array
	 */
	private $badges = [];

	/**
	 * Badges progress.
	 *
	 * @var array
	 */
	private $badges_progress = [];

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->badges_progress = Settings::get( 'badges', [] );
		$this->register_badges();
	}

	/**
	 * Register a badge.
	 *
	 * @param string $badge_id The badge ID.
	 * @param array  $args     The badge arguments.
	 *
	 * @return void
	 */
	public function register_badge( $badge_id, $args ) {
		$args['id']                = $badge_id;
		$this->badges[ $badge_id ] = $args;
	}

	/**
	 * Get a badge by ID.
	 *
	 * @param string $badge_id The badge ID.
	 *
	 * @return array
	 */
	public function get_badge( $badge_id ) {
		return isset( $this->badges[ $badge_id ] ) ? $this->badges[ $badge_id ] : [];
	}

	/**
	 * Get all badges.
	 *
	 * @return array
	 */
	public function get_badges() {
		return $this->badges;
	}

	/**
	 * Get the progress for a badge.
	 *
	 * @param string $badge_id The badge ID.
	 *
	 * @return int
	 */
	public function get_badge_progress( $badge_id ) {
		$badge = $this->get_badge( $badge_id );
		if ( empty( $badge ) ) {
			return 0;
		}

		$progress = [];

		if ( ! isset( $badge['steps'] ) ) {
			return $badge['progress_callback']();
		}

		foreach ( $badge['steps'] as $step ) {
			$progress[] = [
				'name'     => $step['name'],
				'icons'    => $step['icons-svg'],
				'progress' => $badge['progress_callback']( $step['target'] ),
			];
		}

		return $progress;
	}

	/**
	 * Register Core badges.
	 *
	 * @return void
	 */
	private function register_badges() {
		// Badges for number of posts.
		$this->register_badge(
			'content_writing',
			[
				'public'            => true,
				'steps'             => [
					[
						'target'    => 'wonderful-writer',
						'name'      => __( 'Wonderful Writer', 'progress-planner' ),
						'icons-svg' => [
							'pending'  => [
								'path' => \PROGRESS_PLANNER_DIR . '/assets/images/badges/writing_badge1_gray.svg',
								'url'  => \PROGRESS_PLANNER_URL . '/assets/images/badges/writing_badge1_gray.svg',
							],
							'complete' => [
								'path' => \PROGRESS_PLANNER_DIR . '/assets/images/badges/writing_badge1.svg',
								'url'  => \PROGRESS_PLANNER_URL . '/assets/images/badges/writing_badge1.svg',
							],
						],
					],
					[
						'target'    => 'awesome-author',
						'name'      => __( 'Awesome Author', 'progress-planner' ),
						'icons-svg' => [
							'pending'  => [
								'path' => \PROGRESS_PLANNER_DIR . '/assets/images/badges/writing_badge2_gray.svg',
								'url'  => \PROGRESS_PLANNER_URL . '/assets/images/badges/writing_badge2_gray.svg',
							],
							'complete' => [
								'path' => \PROGRESS_PLANNER_DIR . '/assets/images/badges/writing_badge2.svg',
								'url'  => \PROGRESS_PLANNER_URL . '/assets/images/badges/writing_badge2.svg',
							],
						],
					],
					[
						'target'    => 'notorious-novelist',
						'name'      => __( 'Notorious Novelist', 'progress-planner' ),
						'icons-svg' => [
							'pending'  => [
								'path' => \PROGRESS_PLANNER_DIR . '/assets/images/badges/writing_badge3_gray.svg',
								'url'  => \PROGRESS_PLANNER_URL . '/assets/images/badges/writing_badge3_gray.svg',
							],
							'complete' => [
								'path' => \PROGRESS_PLANNER_DIR . '/assets/images/badges/writing_badge3.svg',
								'url'  => \PROGRESS_PLANNER_URL . '/assets/images/badges/writing_badge3.svg',
							],
						],
					],
				],
				'progress_callback' => function ( $target ) {
					$saved_progress = (int) Settings::get( [ 'badges', 'content_writing', $target, 'progress' ], 0 );
					if ( 100 === $saved_progress ) {
						return 100;
					}

					// Evaluation for the "Wonderful writer" badge.
					if ( 'wonderful-writer' === $target ) {
						$existing_count = count(
							\progress_planner()->get_query()->query_activities(
								[
									'category' => 'content',
									'type'     => 'publish',
								]
							)
						);
						// Targeting 200 existing posts.
						$existing_progress = max( 100, floor( $existing_count / 2 ) );
						if ( 100 <= $existing_progress ) {
							if ( $saved_progress !== $existing_progress ) {
								Settings::set(
									[ 'badges', 'content_writing', $target ],
									[
										'progress' => 100,
										'date'     => ( new \DateTime() )->format( 'Y-m-d H:i:s' ),
									]
								);
							}
							return 100;
						}
						$new_count = count(
							\progress_planner()->get_query()->query_activities(
								[
									'category'   => 'content',
									'type'       => 'publish',
									'start_date' => Base::get_activation_date(),
								],
							)
						);
						// Targeting 10 new posts.
						$new_progress = max( 100, floor( $new_count * 10 ) );
						$final        = max( $existing_progress, $new_progress );
						if ( $saved_progress !== $final ) {
							Settings::set(
								[ 'badges', 'content_writing', $target ],
								[
									'progress' => $final,
									'date'     => ( new \DateTime() )->format( 'Y-m-d H:i:s' ),
								]
							);
						}
						return $final;
					}

					// Evaluation for the "Awesome author" badge.
					if ( 'awesome-author' === $target ) {
						$new_count = count(
							\progress_planner()->get_query()->query_activities(
								[
									'category'   => 'content',
									'type'       => 'publish',
									'start_date' => Base::get_activation_date(),
								],
							)
						);

						// Targeting 30 new posts.
						$final = min( 100, floor( 100 * $new_count / 30 ) );

						if ( $saved_progress !== $final ) {
							Settings::set(
								[ 'badges', 'content_writing', $target ],
								[
									'progress' => $final,
									'date'     => ( new \DateTime() )->format( 'Y-m-d H:i:s' ),
								]
							);
						}
						return $final;
					}

					// Evaluation for the "Notorious novelist" badge.
					if ( 'notorious-novelist' === $target ) {
						$new_count = count(
							\progress_planner()->get_query()->query_activities(
								[
									'category'   => 'content',
									'type'       => 'publish',
									'start_date' => Base::get_activation_date(),
								],
							)
						);
						// Targeting 50 new posts.
						$final = min( 100, floor( 50 * $new_count / 100 ) );
						if ( $saved_progress !== $final ) {
							Settings::set(
								[ 'badges', 'content_writing', $target ],
								[
									'progress' => $final,
									'date'     => ( new \DateTime() )->format( 'Y-m-d H:i:s' ),
								]
							);
						}
						return $final;
					}
				},
			]
		);

		// Write a post for 10 consecutive weeks.
		$this->register_badge(
			'streak_any_task',
			[
				'public'            => true,
				'steps'             => [
					[
						'target'    => 6,
						'name'      => __( 'Progress Professional', 'progress-planner' ),
						'icons-svg' => [
							'pending'  => [
								'path' => \PROGRESS_PLANNER_DIR . '/assets/images/badges/streak_badge1_gray.svg',
								'url'  => \PROGRESS_PLANNER_URL . '/assets/images/badges/streak_badge1_gray.svg',
							],
							'complete' => [
								'path' => \PROGRESS_PLANNER_DIR . '/assets/images/badges/streak_badge1.svg',
								'url'  => \PROGRESS_PLANNER_URL . '/assets/images/badges/streak_badge1.svg',
							],
						],
					],
					[
						'target'    => 26,
						'name'      => __( 'Maintenance Maniac', 'progress-planner' ),
						'icons-svg' => [
							'pending'  => [
								'path' => \PROGRESS_PLANNER_DIR . '/assets/images/badges/streak_badge2_gray.svg',
								'url'  => \PROGRESS_PLANNER_URL . '/assets/images/badges/streak_badge2_gray.svg',
							],
							'complete' => [
								'path' => \PROGRESS_PLANNER_DIR . '/assets/images/badges/streak_badge2.svg',
								'url'  => \PROGRESS_PLANNER_URL . '/assets/images/badges/streak_badge2.svg',
							],
						],
					],
					[
						'target'    => 52,
						'name'      => __( 'Super Site Specialist', 'progress-planner' ),
						'icons-svg' => [
							'pending'  => [
								'path' => \PROGRESS_PLANNER_DIR . '/assets/images/badges/streak_badge3_gray.svg',
								'url'  => \PROGRESS_PLANNER_URL . '/assets/images/badges/streak_badge3_gray.svg',
							],
							'complete' => [
								'path' => \PROGRESS_PLANNER_DIR . '/assets/images/badges/streak_badge3.svg',
								'url'  => \PROGRESS_PLANNER_URL . '/assets/images/badges/streak_badge3.svg',
							],
						],
					],
				],
				'progress_callback' => function ( $target ) {
					$goal = new Goal_Recurring(
						new Goal_Posts(
							[
								'id'          => 'weekly_post',
								'title'       => \esc_html__( 'Write a weekly blog post', 'progress-planner' ),
								'description' => \esc_html__( 'Streak: The number of weeks this goal has been accomplished consistently.', 'progress-planner' ),
								'status'      => 'active',
								'priority'    => 'low',
								'evaluate'    => function ( $goal_object ) {
									return (bool) count(
										\progress_planner()->get_query()->query_activities(
											[
												'category' => 'content',
												'type'     => 'publish',
												'start_date' => $goal_object->get_details()['start_date'],
												'end_date' => $goal_object->get_details()['end_date'],
											]
										)
									);
								},
							]
						),
						'weekly',
						Base::get_activation_date(),
						new \DateTime(), // Today.
						1 // Allow break in the streak for 1 week.
					);

					$saved_progress = (int) Settings::get( [ 'badges', 'streak_any_task', $target, 'progress' ], 0 );
					if ( 100 === $saved_progress ) {
						return 100;
					}

					$final = min( floor( 100 * $goal->get_streak()['max_streak'] / $target ), 100 );

					if ( $saved_progress !== $final ) {
						Settings::set(
							[ 'badges', 'streak_any_task', $target ],
							[
								'progress' => $final,
								'date'     => ( new \DateTime() )->format( 'Y-m-d H:i:s' ),
							]
						);
					}
					return $final;
				},
			]
		);

		// Write a post for 10 consecutive weeks.
		$this->register_badge(
			'personal_record_content',
			[
				'public'            => false,
				'progress_callback' => function () {
					$goal = new Goal_Recurring(
						new Goal_Posts(
							[
								'id'          => 'weekly_post',
								'title'       => \esc_html__( 'Write a weekly blog post', 'progress-planner' ),
								'description' => \esc_html__( 'Streak: The number of weeks this goal has been accomplished consistently.', 'progress-planner' ),
								'status'      => 'active',
								'priority'    => 'low',
								'evaluate'    => function ( $goal_object ) {
									return (bool) count(
										\progress_planner()->get_query()->query_activities(
											[
												'category' => 'content',
												'type'     => 'publish',
												'start_date' => $goal_object->get_details()['start_date'],
												'end_date' => $goal_object->get_details()['end_date'],
											]
										)
									);
								},
							]
						),
						'weekly',
						new \DateTime( '-2 years' ), // 2 years ago.
						new \DateTime(), // Today.
						0 // Do not allow breaks in the streak.
					);

					$saved_progress = Settings::get( [ 'badges', 'personal_record_content' ], false );
					// If the date is set and shorter than 2 days, return it without querying.
					if ( $saved_progress && is_array( $saved_progress['progress'] ) && ( new \DateTime() )->diff( new \DateTime( $saved_progress['date'] ) )->days < 2 ) {
						return $saved_progress['progress'];
					}

					$final = $goal->get_streak();
					Settings::set(
						[ 'badges', 'personal_record_content' ],
						[
							'progress' => $final,
							'date'     => ( new \DateTime() )->format( 'Y-m-d H:i:s' ),
						]
					);

					return $goal->get_streak();
				},
			]
		);
	}
}
