<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

use Progress_Planner\Chart;
use Progress_Planner\Activities\Content_Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_types = Content_Helpers::get_post_types_names();
$stats      = $this->get_stats();
$sum_weekly = array_sum( $stats['weekly'] );
?>
<div class="prpl-counter-big-wrapper">
	<span class="counter-big-number">
		<?php echo \esc_html( \number_format_i18n( (int) array_sum( $stats['weekly'] ) ) ); ?>
	</span>
	<span class="counter-big-text">
		<?php echo \esc_html_e( 'content published', 'progress-planner' ); ?>
	</span>
</div>

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
<table>
	<thead>
		<tr>
			<th><?php \esc_html_e( 'Content type', 'progress-planner' ); ?></th>
			<th><?php \esc_html_e( 'Last week', 'progress-planner' ); ?></th>
			<th><?php \esc_html_e( 'Total', 'progress-planner' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $post_types as $post_type_item ) : ?>
			<tr>
				<td><?php echo \esc_html( \get_post_type_object( $post_type_item )->labels->name ); ?></td>
				<td><?php echo \esc_html( \number_format_i18n( $stats['weekly'][ $post_type_item ] ) ); ?></td>
				<td><?php echo \esc_html( \number_format_i18n( $stats['all'][ $post_type_item ] ) ); ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
