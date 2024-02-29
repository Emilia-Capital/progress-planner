<?php
/**
 * Print the graph for words count progress.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

$prpl_query_args = [
	'category' => 'content',
	'type'     => 'publish',
];

$prpl_last_week_content = Admin\Page::get_content_published_this_week();
$prpl_all_content_count = count(
	\progress_planner()->get_query()->query_activities( $prpl_query_args )
);

?>
<div class="prpl-widget-wrapper">
	<div class="counter-big-wrapper">
		<span class="counter-big-number">
			<?php echo esc_html( $prpl_last_week_content ); ?>
		</span>
		<span class="counter-big-text">
			<?php esc_html_e( 'content published', 'progress-planner' ); ?>
		</span>
	</div>
	<div class="prpl-widget-content">
		<p>
			<?php if ( 0 === $prpl_last_week_content ) : ?>
				<?php esc_html_e( 'No content published last week', 'progress-planner' ); ?>
			<?php else : ?>
				<?php
				printf(
					/* translators: %1$d: number of posts/pages published this week. %2$d: Total number of posts. */
					esc_html__( 'Good job! You added %1$d pieces of content in the past week. You now have %2$d in total.', 'progress-planner' ),
					esc_html( $prpl_last_week_content ),
					esc_html( $prpl_all_content_count )
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
				'dates_params' => [
					'start'     => \DateTime::createFromFormat( 'Y-m-d', \gmdate( 'Y-m-01' ) )->modify( '-5 months' ),
					'end'       => new \DateTime(),
					'frequency' => 'weekly',
					'format'    => 'M, d',
				],
				'chart_params' => [
					'type' => 'line',
				],
				'additive'     => false,
			],
		);
		?>
	</div>
</div>
