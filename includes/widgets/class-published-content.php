<?php
/**
 * ProgressPlanner widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Widgets;

use ProgressPlanner\Widgets\Widget;
use ProgressPlanner\Chart;
use ProgressPlanner\Activities\Content_Helpers;

/**
 * Published Content Widget.
 */
final class Published_Content extends Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'published-content';

	/**
	 * Render the widget content.
	 *
	 * @return void
	 */
	protected function the_content() {
		$post_types = Content_Helpers::get_post_types_names();
		$stats      = self::get_stats();
		?>
		<div class="two-col">
			<div class="prpl-top-counter-bottom-content">
				<div class="counter-big-wrapper">
					<span class="counter-big-number">
						<?php echo \esc_html( \number_format_i18n( array_sum( $stats['weekly'] ) ) ); ?>
					</span>
					<span class="counter-big-text">
						<?php \esc_html_e( 'content published', 'progress-planner' ); ?>
					</span>
				</div>
				<div class="prpl-widget-content">
					<p>
						<?php if ( 0 === $stats['weekly'] ) : ?>
							<?php \esc_html_e( 'No content published last week', 'progress-planner' ); ?>
						<?php else : ?>
							<?php
							printf(
								/* translators: %1$s: number of posts/pages published this week. %2$s: Total number of posts. */
								\esc_html__( 'Good job! You added %1$s pieces of content in the past week. You now have %2$s in total.', 'progress-planner' ),
								\esc_html( \number_format_i18n( array_sum( $stats['weekly'] ) ) ),
								\esc_html( \number_format_i18n( array_sum( $stats['all'] ) ) )
							);
							?>
						<?php endif; ?>
					</p>
				</div>
				<div class="prpl-graph-wrapper">
					<?php ( new Chart() )->the_chart( self::get_chart_args() ); ?>
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
					<?php foreach ( $post_types as $post_type ) : ?>
						<tr>
							<td><?php echo \esc_html( \get_post_type_object( $post_type )->labels->name ); ?></td>
							<td><?php echo \esc_html( \number_format_i18n( $stats['weekly'][ $post_type ] ) ); ?></td>
							<td><?php echo \esc_html( \number_format_i18n( $stats['all'][ $post_type ] ) ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Get stats for posts, by post-type.
	 *
	 * @return array The stats.
	 */
	public static function get_stats() {
		$post_types = Content_Helpers::get_post_types_names();
		$weekly     = [];
		$all        = [];
		foreach ( $post_types as $post_type ) {
			// Get the content published this week.
			$weekly[ $post_type ] = count(
				\get_posts(
					[
						'post_status'    => 'publish',
						'post_type'      => $post_type,
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
			$all[ $post_type ] = \wp_count_posts( $post_type )->publish;
		}

		return [
			'weekly' => $weekly,
			'all'    => $all,
		];
	}

	/**
	 * Get the chart args.
	 *
	 * @return array The chart args.
	 */
	public static function get_chart_args() {
		return [
			'query_params' => [
				'category' => 'content',
				'type'     => 'publish',
			],
			'dates_params' => [
				'start'     => \DateTime::createFromFormat( 'Y-m-d', \gmdate( 'Y-m-01' ) )->modify( self::get_range() ),
				'end'       => new \DateTime(),
				'frequency' => self::get_frequency(),
				'format'    => 'M',
			],
			'chart_params' => [
				'type' => 'line',
			],
			'compound'     => false,
		];
	}
}
