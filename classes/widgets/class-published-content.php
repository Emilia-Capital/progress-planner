<?php
/**
 * Progress_Planner widget.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Widgets;

use Progress_Planner\Widgets\Widget;
use Progress_Planner\Chart;
use Progress_Planner\Activities\Content_Helpers;

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
		$stats      = $this->get_stats();
		$sum_weekly = array_sum( $stats['weekly'] );
		?>
		<div class="two-col">
			<div class="prpl-top-counter-bottom-content">
				<?php $this->render_big_counter( (int) array_sum( $stats['weekly'] ), __( 'content published', 'progress-planner' ) ); ?>
				<div class="prpl-widget-content">
					<p>
						<?php if ( 0 === $sum_weekly ) : ?>
							<?php \esc_html_e( 'You didn\'t publish new content last week. You can do better!', 'progress-planner' ); ?>
						<?php else : ?>
							<?php
							printf(
								\esc_html(
									/* translators: %1$s: number of posts/pages published this week + "pieces". %2$s: Total number of posts. */
									\_n(
										'Nice! You published %1$s piece of new content last week. You now have %2$s in total. Keep up the good work!',
										'Nice! You published %1$s pieces of new content last week. You now have %2$s in total. Keep up the good work!',
										$sum_weekly,
										'progress-planner'
									)
								),
								\esc_html( \number_format_i18n( $sum_weekly ) ),
								\esc_html( \number_format_i18n( array_sum( $stats['all'] ) ) )
							);
							?>
						<?php endif; ?>
					</p>
				</div>
				<div class="prpl-graph-wrapper">
					<?php ( new Chart() )->the_chart( $this->get_chart_args() ); ?>
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
	public function get_stats() {
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
	public function get_chart_args() {
		return [
			'query_params'   => [
				'category' => 'content',
				'type'     => 'publish',
			],
			'dates_params'   => [
				'start'     => \DateTime::createFromFormat( 'Y-m-d', \gmdate( 'Y-m-01' ) )->modify( $this->get_range() ),
				'end'       => new \DateTime(),
				'frequency' => $this->get_frequency(),
				'format'    => 'M',
			],
			'chart_params'   => [
				'type' => 'line',
			],
			'compound'       => false,
			'filter_results' => [ $this, 'filter_activities' ],
		];
	}

	/**
	 * Callback to filter the activities.
	 *
	 * @param \Progress_Planner\Activities\Content[] $activities The activities array.
	 *
	 * @return \Progress_Planner\Activities\Content[]
	 */
	public function filter_activities( $activities ) {
		return array_filter(
			$activities,
			function ( $activity ) {
				$post = $activity->get_post();
				return is_object( $post )
					&& \in_array( $post->post_type, Content_Helpers::get_post_types_names(), true );
			}
		);
	}
}
