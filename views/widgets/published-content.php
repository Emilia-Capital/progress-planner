<?php
/**
 * ProgressPlanner widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

use ProgressPlanner\Activities\Content_Helpers;

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$prpl_active_range = isset( $_GET['range'] ) ? \sanitize_text_field( \wp_unslash( $_GET['range'] ) ) : '-6 months';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$prpl_active_frequency = isset( $_GET['frequency'] ) ? \sanitize_text_field( \wp_unslash( $_GET['frequency'] ) ) : 'monthly';

$prpl_post_types        = Content_Helpers::get_post_types_names();
$prpl_last_week_content = [];
$prpl_all_content_count = [];
foreach ( $prpl_post_types as $prpl_post_type ) {
	// Get the content published this week.
	$prpl_last_week_content[ $prpl_post_type ] = count(
		\get_posts(
			[
				'post_status'    => 'publish',
				'post_type'      => $prpl_post_type,
				'date_query'     => [
					[
						'after' => '1 week ago',
					],
				],
				'posts_per_page' => 100,
			]
		)
	);
	// Get the total number of posts for this post-type.
	$prpl_all_content_count[ $prpl_post_type ] = \wp_count_posts( $prpl_post_type )->publish;
}
?>
<div class="two-col">
	<div class="prpl-top-counter-bottom-content">
		<div class="counter-big-wrapper">
			<span class="counter-big-number">
				<?php echo \esc_html( \number_format_i18n( array_sum( $prpl_last_week_content ) ) ); ?>
			</span>
			<span class="counter-big-text">
				<?php \esc_html_e( 'content published', 'progress-planner' ); ?>
			</span>
		</div>
		<div class="prpl-widget-content">
			<p>
				<?php if ( 0 === $prpl_last_week_content ) : ?>
					<?php \esc_html_e( 'No content published last week', 'progress-planner' ); ?>
				<?php else : ?>
					<?php
					printf(
						/* translators: %1$s: number of posts/pages published this week. %2$s: Total number of posts. */
						\esc_html__( 'Good job! You added %1$s pieces of content in the past week. You now have %2$s in total.', 'progress-planner' ),
						\esc_html( \number_format_i18n( array_sum( $prpl_last_week_content ) ) ),
						\esc_html( \number_format_i18n( array_sum( $prpl_all_content_count ) ) )
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
						'start'     => \DateTime::createFromFormat( 'Y-m-d', \gmdate( 'Y-m-01' ) )->modify( $prpl_active_range ),
						'end'       => new \DateTime(),
						'frequency' => $prpl_active_frequency,
						'format'    => 'M',
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
	<table>
		<thead>
			<tr>
				<th><?php \esc_html_e( 'Content type', 'progress-planner' ); ?></th>
				<th><?php \esc_html_e( 'Last week', 'progress-planner' ); ?></th>
				<th><?php \esc_html_e( 'Total', 'progress-planner' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $prpl_post_types as $prpl_post_type ) : ?>
				<tr>
					<td><?php echo \esc_html( \get_post_type_object( $prpl_post_type )->labels->name ); ?></td>
					<td><?php echo \esc_html( \number_format_i18n( $prpl_last_week_content[ $prpl_post_type ] ) ); ?></td>
					<td><?php echo \esc_html( \number_format_i18n( $prpl_all_content_count[ $prpl_post_type ] ) ); ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
