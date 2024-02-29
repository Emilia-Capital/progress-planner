<?php
/**
 * Print the graph for words count progress.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

$prpl_last_week_pages = count(
	get_posts(
		[
			'post_type'   => 'page',
			'post_status' => 'publish',
			'date_query'  => [
				[
					'after' => '1 week ago',
				],
			],
		]
	)
);
$prpl_all_pages_count = wp_count_posts( 'page' );

?>
<div class="prpl-widget-wrapper">
	<div class="counter-big-wrapper">
		<span class="counter-big-number">
			<?php echo esc_html( $prpl_last_week_pages ); ?>
		</span>
		<span class="counter-big-text">
			<?php esc_html_e( 'pages published', 'progress-planner' ); ?>
		</span>
	</div>
	<div class="prpl-widget-content">
		<p>
			<?php if ( 0 === $prpl_last_week_pages ) : ?>
				<?php esc_html_e( 'No pages last week', 'progress-planner' ); ?>
			<?php else : ?>
				<?php
				printf(
					/* translators: %1$d: number of posts published this week. %2$d: Total number of pages. */
					esc_html__( 'Good job! You added %1$s pages in the past week. You now have %2$s pages in total.', 'progress-planner' ),
					esc_html( $prpl_last_week_pages ),
					esc_html( $prpl_all_pages_count->publish )
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
					'category' => 'content',
					'type'     => 'publish',
				],
				'filter_results' => function ( $activities ) {
					foreach ( $activities as $key => $activity ) {
						if ( 'page' !== $activity->get_post()->post_type ) {
							unset( $activities[ $key ] );
						}
					}
					return $activities;
				},
				'dates_params' => [
					'start'     => \DateTime::createFromFormat( 'Y-m-d', \gmdate( 'Y-m-01' ) )->modify( '-5 months' ),
					'end'       => new \DateTime(),
					'frequency' => 'monthly',
					'format'    => 'M',
				],
				'chart_params' => [
					'type' => 'line',
				],
			]
		);
		?>
	</div>
</div>
