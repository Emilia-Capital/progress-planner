<?php
/**
 * ProgressPlanner widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

use ProgressPlanner\Activities\Content_Helpers;

$prpl_last_week_content = count(
	get_posts(
		[
			'post_status'    => 'publish',
			'post_type'      => Content_Helpers::get_post_types_names(),
			'date_query'     => [
				[
					'after' => '1 week ago',
				],
			],
			'posts_per_page' => 100,
		]
	)
);
$prpl_all_content_count = 0;
foreach ( Content_Helpers::get_post_types_names() as $prpl_post_type ) {
	$prpl_all_content_count += wp_count_posts( $prpl_post_type )->publish;
}

?>
<div class="prpl-top-counter-bottom-content">
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
