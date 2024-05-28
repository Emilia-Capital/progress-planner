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
			<?php \esc_html_e( 'Your Website Activity Score', 'progress-planner' ); ?>
		</h2>
		<div class="two-col">
			<?php self::print_score_gauge(); ?>
			<div>
				<?php \esc_html_e( 'Your activity this week:', 'progress-planner' ); ?>
				<?php $this->print_weekly_activities_checklist(); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Print the score gauge.
	 *
	 * @return void
	 */
	public static function print_score_gauge() {
		$score = self::get_score();
		?>
		<div>
			<div class="prpl-activities-gauge-container">
				<div
					class="prpl-activities-gauge"
					style="
						--value:<?php echo (float) ( $score / 100 ); ?>;
						--background: var(--prpl-background-orange);
						--max: 180deg;
						--start: 270deg;
						--color:<?php echo \esc_attr( self::get_gauge_color( $score ) ); ?>"
				>
					<span class="prpl-gauge-number">
						<?php echo (int) $score; ?>
					</span>
				</div>
			</div>
			<?php \esc_html_e( 'Your Website Activity Score is based on the amount of website maintenance work you\'ve done over the past 30 days.', 'progress-planner' ); ?>
		</div>
		<?php
	}

	/**
	 * Print the list of weekly activities checklist items.
	 *
	 * @return void
	 */
	public static function print_weekly_activities_checklist() {
		?>
		<ul>
			<?php foreach ( self::get_checklist_results() as $label => $value ) : ?>
				<li class="prpl-checklist-item">
					<?php echo $value ? '<span class="prpl-icon prpl-green">&#10003;</span>' : '<span class="prpl-icon prpl-red">&#9747;</span>'; ?>
					<?php echo wp_kses_post( $label ); ?>
				</li>
			<?php endforeach; ?>
		</ul>
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
			$score += $activity->get_points( $current_date ) / 2;
		}
		$score = min( 100, max( 0, $score / 2 ) );

		// Get the number of pending updates.
		$pending_updates = \wp_get_update_data()['counts']['total'];

		// Reduce points for pending updates.
		$score -= min( min( $score / 2, 25 ), $pending_updates * 5 );
		return (int) floor( $score );
	}

	/**
	 * Get the checklist results.
	 *
	 * @return array<string, bool> The checklist results.
	 */
	public static function get_checklist_results() {
		$items   = self::get_checklist();
		$results = [];
		foreach ( $items as $item ) {
			$results[ $item['label'] ] = $item['callback']();
		}
		return $results;
	}

	/**
	 * Get the checklist items.
	 *
	 * @return array The checklist items.
	 */
	public static function get_checklist() {
		return [
			[
				'label'    => \esc_html__( 'published content', 'progress-planner' ),
				'callback' => function () {
					$events = \progress_planner()->get_query()->query_activities(
						[
							'start_date' => new \DateTime( '-7 days' ),
							'category'   => 'content',
							'type'       => 'publish',
						]
					);
					return count( $events ) > 0;
				},
			],
			[
				'label'    => \esc_html__( 'updated content', 'progress-planner' ),
				'callback' => function () {
					$events = \progress_planner()->get_query()->query_activities(
						[
							'start_date' => new \DateTime( '-7 days' ),
							'category'   => 'content',
							'type'       => 'update',
						]
					);
					return count( $events ) > 0;
				},
			],
			[
				'label'    => 0 === \wp_get_update_data()['counts']['total']
					? \esc_html__( 'performed all updates', 'progress-planner' )
					: '<a href="' . \esc_url( \admin_url( 'update-core.php' ) ) . '">' . \esc_html__( 'Perform all updates', 'progress-planner' ) . '</a>',
				'callback' => function () {
					return ! \wp_get_update_data()['counts']['total'];
				},
			],
		];
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
