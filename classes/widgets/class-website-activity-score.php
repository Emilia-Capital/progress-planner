<?php
/**
 * Progress_Planner widget.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Widgets;

use Progress_Planner\Widgets\Widget;

/**
 * Website activity score widget.
 */
final class Website_Activity_Score extends Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'website-activity-score';

	/**
	 * Render the widget content.
	 */
	public function the_content() {
		?>
		<h2 class="prpl-widget-title">
			<?php \esc_html_e( 'Your website activity score', 'progress-planner' ); ?>
		</h2>
		<?php self::print_score_gauge(); ?>
		<?php
	}

	/**
	 * Print the score gauge.
	 *
	 * @param string $background_color The background color.
	 * @param string $description      The description.
	 *
	 * @return void
	 */
	public static function print_score_gauge( $background_color = 'var(--prpl-background-orange)', $description = '' ) {
		$score = self::get_score();
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
						--color:<?php echo \esc_attr( self::get_gauge_color( $score ) ); ?>"
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
				\esc_html_e( 'Your Website Activity Score is based on the amount of website maintenance work you\'ve done over the past 30 days.', 'progress-planner' );
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
		$activities = \progress_planner()->get_query()->query_activities(
			[
				// Use 31 days to take into account
				// the activities score decay from previous activities.
				'start_date' => new \DateTime( '-31 days' ),
			]
		);

		$score        = 0;
		$current_date = new \DateTime();
		foreach ( $activities as $activity ) {
			$score += $activity->get_points( $current_date );
		}
		$score = min( 100, max( 0, $score ) );

		// Get the number of pending updates.
		$pending_updates = \wp_get_update_data()['counts']['total'];

		// Reduce points for pending updates.
		$score -= min( min( $score / 2, 25 ), $pending_updates * 5 );
		return (int) floor( $score );
	}

	/**
	 * Get the gauge color.
	 *
	 * @param int $score The score.
	 *
	 * @return string The color.
	 */
	protected static function get_gauge_color( $score ) {
		if ( $score >= 75 ) {
			return 'var(--prpl-color-accent-green)';
		}
		if ( $score >= 50 ) {
			return 'var(--prpl-color-accent-orange)';
		}
		return 'var(--prpl-color-accent-red)';
	}
}
