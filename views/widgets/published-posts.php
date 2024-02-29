<?php
/**
 * ProgressPlanner widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

$prpl_last_week_posts = count(
	get_posts(
		[
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'date_query'     => [
				[
					'after' => '1 week ago',
				],
			],
			'posts_per_page' => 100,
		]
	)
);
$prpl_all_posts_count = wp_count_posts();

?>
<div class="counter-big-wrapper">
	<span class="counter-big-number">
		<?php echo esc_html( $prpl_last_week_posts ); ?>
	</span>
	<span class="counter-big-text">
		<?php esc_html_e( 'posts published', 'progress-planner' ); ?>
	</span>
</div>
<div class="prpl-widget-content">
	<p>
		<?php if ( 0 === $prpl_last_week_posts ) : ?>
			<?php esc_html_e( 'No posts last week', 'progress-planner' ); ?>
		<?php else : ?>
			<?php
			printf(
				/* translators: %1$d: number of posts published this week. %2$d: Total number of posts. */
				esc_html__( 'Good job! You added %1$d posts in the past week. You now have %2$d posts in total.', 'progress-planner' ),
				esc_html( $prpl_last_week_posts ),
				esc_html( $prpl_all_posts_count->publish )
			);
			?>
		<?php endif; ?>
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
			'filter_results' => function ( $activities ) {
				foreach ( $activities as $key => $activity ) {
					if ( 'post' !== $activity->get_post()->post_type ) {
						unset( $activities[ $key ] );
					}
				}
				return $activities;
			},
			'dates_params'   => [
				'start'     => \DateTime::createFromFormat( 'Y-m-d', \gmdate( 'Y-m-01' ) )->modify( '-5 months' ),
				'end'       => new \DateTime(),
				'frequency' => 'monthly',
				'format'    => 'M',
			],
			'chart_params'   => [
				'type' => 'line',
			],
		],
	);
	?>
</div>
