<?php
/**
 * ProgressPlanner widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

use ProgressPlanner\Goals\Goal_Recurring;
use ProgressPlanner\Goals\Goal_Posts;
use ProgressPlanner\Settings;

$prpl_personal_record_callback = function () {
	$goal = Goal_Recurring::get_instance(
		'weekly_post_record',
		[
			'class_name'  => Goal_Posts::class,
			'id'          => 'weekly_post',
			'title'       => \esc_html__( 'Write a weekly blog post', 'progress-planner' ),
			'description' => \esc_html__( 'Streak: The number of weeks this goal has been accomplished consistently.', 'progress-planner' ),
			'status'      => 'active',
			'priority'    => 'low',
			'evaluate'    => function ( $goal_object ) {
				return (bool) count(
					\progress_planner()->get_query()->query_activities(
						[
							'category'   => 'content',
							'type'       => 'publish',
							'start_date' => $goal_object->get_details()['start_date'],
							'end_date'   => $goal_object->get_details()['end_date'],
						]
					)
				);
			},
		],
		[
			'frequency'     => 'weekly',
			'start'         => new \DateTime( '-2 years' ),
			'end'           => new \DateTime(), // Today.
			'allowed_break' => 0, // Do not allow breaks in the streak.
		]
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
};

$prpl_personal_record_content = $prpl_personal_record_callback();

?>
<div class="two-col narrow">
	<div class="counter-big-wrapper">
		<span class="counter-big-number">
			<?php echo \esc_html( \number_format_i18n( $prpl_personal_record_content['max_streak'] ) ); ?>
		</span>
		<span class="counter-big-text">
			<?php \esc_html_e( 'personal record', 'progress-planner' ); ?>
		</span>
	</div>
	<div class="prpl-widget-content">
		<p>
			<?php if ( (int) $prpl_personal_record_content['max_streak'] <= (int) $prpl_personal_record_content['current_streak'] ) : ?>
				<?php
				printf(
					/* translators: %s: number of weeks. */
					\esc_html__( 'Congratulations, you\'re on a streak! You have been adding new content to your site consistently every week for the past %s weeks! 🎉', 'progress-planner' ),
					\esc_html( \number_format_i18n( $prpl_personal_record_content['current_streak'] ) )
				);
				?>
			<?php elseif ( 1 < $prpl_personal_record_content['current_streak'] ) : ?>
				<?php
				printf(
					/* translators: %1$s: number of weeks for the current streak. %2$s: number of weeks for the maximum streak. %3$s: The number of weeks to go in order to break the record. */
					\esc_html__( 'Keep it up! You have been adding content to your site consistently every week for the past %1$s weeks! Your longest streak was %2$s weeks, you have %3$s weeks to go to break your record!', 'progress-planner' ),
					\esc_html( \number_format_i18n( $prpl_personal_record_content['current_streak'] ) ),
					\esc_html( \number_format_i18n( $prpl_personal_record_content['max_streak'] ) ),
					\esc_html( \number_format_i18n( $prpl_personal_record_content['max_streak'] - $prpl_personal_record_content['current_streak'] ) )
				);
				?>
			<?php else : ?>
				<?php
				printf(
					/* translators: %s: number of weeks for the maximum streak. */
					\esc_html__( 'Your longest streak was %s weeks. Keep adding content to your site every week and break your record!', 'progress-planner' ),
					\esc_html( \number_format_i18n( $prpl_personal_record_content['max_streak'] ) ),
				);
				?>
			<?php endif; ?>
		</p>
	</div>
</div>
