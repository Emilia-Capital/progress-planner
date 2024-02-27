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
	'data'     => [
		'post_type' => 'post',
	],
];
$prpl_last_week_posts = Admin\Page::get_posts_published_this_week();
$prpl_all_posts_count = count(
	Activities\Query::get_instance()->query_activities( $prpl_query_args )
);

?>
<div class="prpl-widget-wrapper">
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
					esc_html__( 'Good job! You added %1$s posts in the past week. You now have %2$s posts in total.', 'progress-planner' ),
					esc_html( $prpl_last_week_posts ),
					esc_html( $prpl_all_posts_count )
				);
				?>
			<?php endif; ?>
		</p>
	</div>
	<div class="graph-wrapper">
		<?php
		( new Charts\Posts() )->the_chart(
			[
				'category' => 'post',
				'type'     => 'publish',
				'data'     => [
					'post_type' => 'post',
				],
			],
			[
				'start'    => \DateTime::createFromFormat( 'Y-m-d', \gmdate( 'Y-m-01', \strtotime( 'now' ) ) )->modify( '-6 months' ),
				'end'      => new \DateTime( 'now' ),
				'interval' => 'months',
				'range'    => 6,
				'format'   => 'M',
			]
		);
		?>
	</div>
</div>
