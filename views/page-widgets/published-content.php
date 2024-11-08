<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$prpl_widget = \progress_planner()->get_admin__page()->get_widget( 'published-content' );
$post_types  = \progress_planner()->get_activities__content_helpers()->get_post_types_names();
$stats       = $prpl_widget->get_stats();
$sum_weekly  = array_sum( $stats['weekly'] );
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
	<?php \progress_planner()->get_chart()->the_chart( $prpl_widget->get_chart_args_content_count() ); ?>
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

<hr>

<div class="prpl-counter-big-wrapper">
	<span class="counter-big-number">
		<?php echo \esc_html( \number_format_i18n( (int) $prpl_widget->get_weekly_activities_density() ) ); ?>
	</span>
	<span class="counter-big-text">
		<?php echo \esc_html_e( 'content density', 'progress-planner' ); ?>
	</span>
</div>
<p>
	<?php
	if ( 0 === $prpl_widget->get_weekly_activities_density() ) {
		printf(
			/* translators: %s: All-time average number. */
			\esc_html__( 'Your overall content density average is %1$s.', 'progress-planner' ),
			\esc_html( \number_format_i18n( $prpl_widget->get_all_activities_density() ) )
		);
	} else {
		printf(
			/* translators: %1$s: number of words/post published this week. %2$s: All-time average number. */
			\esc_html__( 'Your content has an average density of %1$s words per post in the last 7 days. Your overall content density average is %2$s.', 'progress-planner' ),
			\esc_html( \number_format_i18n( $prpl_widget->get_weekly_activities_density() ) ),
			\esc_html( \number_format_i18n( $prpl_widget->get_all_activities_density() ) )
		);
	}
	?>
</p>
<div class="prpl-graph-wrapper">
	<?php \progress_planner()->get_chart()->the_chart( $prpl_widget->get_chart_args_content_density() ); ?>
</div>

<hr>

<div class="prpl-counter-big-wrapper">
	<span class="counter-big-number">
		<?php echo \esc_html( \number_format_i18n( (int) $prpl_widget->get_weekly_words() ) ); ?>
	</span>
	<span class="counter-big-text">
		<?php echo \esc_html_e( 'words', 'progress-planner' ); ?>
	</span>
</div>
<p>
	<?php if ( 0 === $prpl_widget->get_weekly_words() ) : ?>
		<?php \esc_html_e( 'You didn\'t write last week. Let\'s get started!', 'progress-planner' ); ?>
	<?php else : ?>
		<?php
		printf(
			\esc_html(
				/* translators: %1$s: number of posts published this week. %2$s: Total number of posts. */
				\_n(
					'Great job! You have written %1$s word in the past 7 days.',
					'Great job! You have written %1$s words in the past 7 days.',
					$prpl_widget->get_weekly_words(),
					'progress-planner'
				)
			),
			\esc_html( \number_format_i18n( $prpl_widget->get_weekly_words() ) ),
		);
		?>
	<?php endif; ?>
</p>
