<?php
/**
 * Progress_Planner widget.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Widgets;

use Progress_Planner\Base;
use Progress_Planner\Goals\Goal_Recurring;
use Progress_Planner\Goals\Goal;
use Progress_Planner\Settings;
use Progress_Planner\Widgets\Widget;

/**
 * Personal record content widget.
 */
final class Personal_Record_Content extends Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'personal-record-content';

	/**
	 * Render the widget content.
	 *
	 * @return void
	 */
	protected function the_content() {
		$record = $this->personal_record_callback();
		?>
		<div class="two-col narrow">
			<?php $this->render_big_counter( (int) $record['max_streak'], __( 'Personal record', 'progress-planner' ) ); ?>
			<div class="prpl-widget-content">
				<?php if ( (int) $record['max_streak'] === 0 ) : ?>
					<?php \esc_html_e( 'This is the start of your first streak! Add content to your site every week and set a personal record!', 'progress-planner' ); ?>
				<?php elseif ( (int) $record['max_streak'] <= (int) $record['current_streak'] ) : ?>
					<?php
					printf(
						/* translators: %s: number of weeks. */
						\esc_html__( 'Congratulations! You\'re on a streak! You\'ve consistently maintained your website for the past %s! 🎉', 'progress-planner' ),
						Base::weeks( $record['current_streak'] ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					);
					?>
				<?php elseif ( 1 <= $record['current_streak'] ) : ?>
					<?php
					printf(
						/* translators: %1$s: number of weeks for the current streak. %2$s: number of weeks for the maximum streak. %3$s: The number of weeks to go in order to break the record. */
						\esc_html__( 'Keep it up! You\'ve consistently maintained your website for the past %1$s. Your longest streak was %2$s, you have %3$s to go to break your record!', 'progress-planner' ),
						Base::weeks( $record['current_streak'] ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						Base::weeks( $record['max_streak'] ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						Base::weeks( $record['max_streak'] - $record['current_streak'] ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					);
					?>
				<?php else : ?>
					<?php
					printf(
						/* translators: %1$s: number of weeks for the maximum streak. */
						\esc_html__( 'Get back to your streak! Your longest streak was %1$s. Keep working on those website maintenance tasks every week and break your record!', 'progress-planner' ),
						Base::weeks( $record['max_streak'] ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					);
					?>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Get the personal record goal.
	 *
	 * @return array
	 */
	public function personal_record_callback() {
		$goal = Goal_Recurring::get_instance(
			'weekly_post_record',
			[
				'class_name'  => Goal::class,
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

		$saved_progress = Settings::get( [ 'badges', 'record' ], false );
		// If the date is set and shorter than 2 days, return it without querying.
		if ( $saved_progress && is_array( $saved_progress['progress'] ) && ( new \DateTime() )->diff( new \DateTime( $saved_progress['date'] ) )->days < 2 ) {
			return $saved_progress['progress'];
		}

		$final = $goal->get_streak();
		Settings::set(
			[ 'badges', 'record' ],
			[
				'progress' => $final,
				'date'     => ( new \DateTime() )->format( 'Y-m-d H:i:s' ),
			]
		);

		return $goal->get_streak();
	}
}
