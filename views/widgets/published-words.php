<?php
/**
 * ProgressPlanner widget.
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

$prpl_all_time_words = $prpl_count_words_callback(
	\progress_planner()->get_query()->query_activities( $prpl_query_args )
);

$prpl_this_week_words = $prpl_count_words_callback(
	\progress_planner()->get_query()->query_activities(
		array_merge(
			$prpl_query_args,
			[
				'start_date' => new \DateTime( '-7 days' ),
				'end_date'   => new \DateTime(),
			]
		)
	)
);

$prpl_color_callback = function () {
	return '#14b8a6';
};

?>
<div class="two-col">
	<div class="prpl-top-counter-bottom-content">
		<div class="counter-big-wrapper">
			<span class="counter-big-number">
				<?php echo esc_html( $prpl_this_week_words ); ?>
			</span>
			<span class="counter-big-text">
				<?php esc_html_e( 'words written', 'progress-planner' ); ?>
			</span>
		</div>
		<div class="prpl-widget-content">
			<p>
				<?php if ( 0 === $prpl_this_week_words ) : ?>
					<?php esc_html_e( 'No words written last week', 'progress-planner' ); ?>
				<?php else : ?>
					<?php
					printf(
						/* translators: %1$d: number of posts published this week. %2$d: Total number of posts. */
						esc_html__( 'Great! You have written %1$d words in the past 7 days. You now have %2$d words in total.', 'progress-planner' ),
						esc_html( $prpl_this_week_words ),
						esc_html( $prpl_all_time_words )
					);
					?>
				<?php endif; ?>
			</p>
		</div>
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
					'start'     => \DateTime::createFromFormat( 'Y-m-d', \gmdate( 'Y-m-01' ) )->modify( '-5 months' ),
					'end'       => new \DateTime(),
					'frequency' => 'monthly',
					'format'    => 'M',
				],
				'chart_params'   => [
					'type' => 'line',
				],
				'count_callback' => $prpl_count_words_callback,
				'additive'       => false,
				'colors'       => [
					'background' => $prpl_color_callback,
					'border'     => $prpl_color_callback,
				],
			],
		);
		?>
	</div>
</div>
