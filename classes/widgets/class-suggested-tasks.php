<?php
/**
 * Progress_Planner widget.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Widgets;

use Progress_Planner\Query;

/**
 * Published Content Widget.
 */
final class Suggested_Tasks extends Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'suggested-tasks';

	/**
	 * Print the score gauge.
	 *
	 * @param string $background_color The background color.
	 * @param string $description      The description.
	 *
	 * @return void
	 */
	public function print_score_gauge( $background_color = 'var(--prpl-background-orange)', $description = '' ) {
		$score = $this->get_score();
		?>
		<div class="prpl-activities-gauge-container-container">
			<div class="prpl-activities-gauge-container">
				<div
					class="prpl-activities-gauge"
					style="
						--value:<?php echo (float) ( $score / 100 ); ?>;
						--background: <?php echo \esc_attr( $background_color ); ?>;
						--max: 180deg;
						--start: 270deg;
						--color:var(--prpl-color-accent-orange)"
				>
					<span class="prpl-gauge-0">
						0
					</span>
					<span class="prpl-gauge-number">
						<?php echo (int) $score; ?>
					</span>
					<span class="prpl-gauge-100">
						100
					</span>
				</div>
			</div>
			<?php
			if ( empty( $description ) ) {
				\esc_html_e( 'Bla bla bla bla bla', 'progress-planner' );
			} else {
				echo \wp_kses_post( $description );
			}
			?>
		</div>
		<?php
	}

	/**
	 * Get the score.
	 *
	 * @return int The score.
	 */
	public static function get_score() {
		$activities = Query::get_instance()->query_activities(
			[
				'category'   => 'suggested_task',
				// Use 31 days to take into account
				// the activities score decay from previous activities.
				'start_date' => new \DateTime( '-31 days' ),
			]
		);

		$activities_count = count( $activities );

		/*
		If we need to get the pending activities count, we can use the following code:

		$pending_activities = \get_option( \Progress_Planner\Suggested_Tasks::OPTION_NAME, [] );
		$pending_activities_count = count( $pending_activities );
		$total_count = $activities_count + $pending_activities_count;
		 */

		$score = 10 * $activities_count;

		return (int) min( 100, max( 0, floor( $score ) ) );
	}
}
