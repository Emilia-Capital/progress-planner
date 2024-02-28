<?php
/**
 * Print the graph for words count progress.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

$prpl_query_args = [
	'category' => 'post',
	'type'     => 'publish',
];

$prpl_count_words_callback = function ( $activities ) {
	$words = 0;
	foreach ( $activities as $activity ) {
		$words += $activity->get_data( 'word_count' );
	}
	return $words;
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
				'end_date'   => new \DateTime( 'tomorrow' ),
			]
		)
	)
);

?>
<div class="prpl-widget-wrapper">
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
	<div class="prpl-graph-wrapper">
		<?php
		( new Chart() )->the_chart(
			[
				'query_params' => [
					'category' => 'post',
					'type'     => 'publish',
					'data'     => [
						'post_type' => 'post',
					],
				],
				'dates_params' => [
					'start'     => \DateTime::createFromFormat( 'Y-m-d', \gmdate( 'Y-m-01', \strtotime( 'now' ) ) )->modify( '-5 months' ),
					'end'       => new \DateTime( 'now' ),
					'frequency' => 'monthly',
					'format'    => 'M',
				],
				'chart_params' => [
					'type' => 'line',
				],
				'count_callback' => $prpl_count_words_callback,
				'additive'	   => false,
			],
		);
		?>
	</div>
</div>
