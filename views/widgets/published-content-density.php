<?php
/**
 * Print the graph for words count progress.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

use ProgressPlanner\Activities\Content_Helpers;

$prpl_query_args = [
	'category' => 'content',
	'type'     => 'publish',
];

$prpl_count_words_callback = function ( $activities ) {
	return Content_Helpers::get_posts_stats_by_ids(
		array_map(
			function ( $activity ) {
				return $activity->get_data_id();
			},
			$activities
		)
	)['words'];
};

$prpl_count_density_callback = function ( $activities ) use ( $prpl_count_words_callback ) {
	$words = $prpl_count_words_callback( $activities );
	$count = count( $activities );
	return round( $words / max( 1, $count ) );
};

$prpl_all_activities_density = $prpl_count_density_callback(
	\progress_planner()->get_query()->query_activities( $prpl_query_args )
);

$prpl_weekly_activities_density = $prpl_count_density_callback(
	\progress_planner()->get_query()->query_activities(
		array_merge(
			$prpl_query_args,
			[
				'start_date' => new \DateTime( '-7 days' ),
				'end_date'   => new \DateTime( 'tomorrow' ),
			]
		)
	)
);

?>
<div class="prpl-widget-wrapper">
	<div class="counter-big-wrapper">
		<span class="counter-big-number">
			<?php echo esc_html( $prpl_weekly_activities_density ); ?>
		</span>
		<span class="counter-big-text">
			<?php esc_html_e( 'content density', 'progress-planner' ); ?>
		</span>
	</div>
	<div class="prpl-widget-content">
		<p>
			<?php
			printf(
				/* translators: %d: number of words/post published this week. */
				esc_html__( 'You have written content with an average density of %1$d words/post in the past 7 days. Your all-time average is %2$d', 'progress-planner' ),
				esc_html( $prpl_weekly_activities_density ),
				esc_html( $prpl_all_activities_density )
			);
			?>
		</p>
	</div>
	<div class="prpl-graph-wrapper">
		<?php
		( new Chart() )->the_chart(
			[
				'query_params'   => [
					'category' => 'content',
					'type'     => 'publish',
				],
				'dates_params'   => [
					'start'     => \DateTime::createFromFormat( 'Y-m-d', \gmdate( 'Y-m-01', \strtotime( 'now' ) ) )->modify( '-12 months' ),
					'end'       => new \DateTime( 'now' ),
					'frequency' => 'weekly',
					'format'    => 'M',
				],
				'chart_params'   => [
					'type' => 'line',
				],
				'count_callback' => $prpl_count_density_callback,
				'additive'       => false,
			],
		);
		?>
	</div>
</div>
