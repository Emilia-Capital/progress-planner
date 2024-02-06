<?php
/**
 * View for the admin page.
 *
 * @package ProgressPlanner
 */

?>

<div class="wrap">
	<h1><?php esc_html_e( 'Progress Planner', 'progress-planner' ); ?></h1>

	<h2><?php esc_html_e( 'Post Types', 'progress-planner' ); ?></h2>

	<?php $progress_planner_post_types = get_post_types( [], 'objects' ); ?>
	<?php foreach ( $progress_planner_post_types as $progress_planner_post_type => $progress_planner_post_type_object ) : ?>
		<?php if ( ! $progress_planner_post_type_object->public ) : ?>
			<?php continue; ?>
		<?php endif; ?>
		<h3><?php echo esc_html( $progress_planner_post_type_object->label ); ?></h3>
		<table>
			<tr>
				<th style="padding: 0.25em 1em;">
					<?php esc_html_e( 'Period', 'progress-planner' ); ?>
				</th>
				<th style="padding: 0.25em 1em;">
					<?php esc_html_e( 'Number of posts', 'progress-planner' ); ?>
				</th>
				<th style="padding: 0.25em 1em;">
					<?php esc_html_e( 'Number of words', 'progress-planner' ); ?>
				</th>
			</tr>
			<?php
			foreach ( [
				'day'      => esc_html__( 'Day', 'progress-planner' ),
				'week'     => esc_html__( 'Week', 'progress-planner' ),
				'-2 weeks' => esc_html__( '2 Weeks', 'progress-planner' ),
				'-3 weeks' => esc_html__( '3 Weeks', 'progress-planner' ),
				'month'    => esc_html__( 'Month', 'progress-planner' ),
				'year'     => esc_html__( 'Year', 'progress-planner' ),
			] as $progress_planner_period => $progress_planner_period_label ) :
				?>
				<tr>
					<td>
						<?php echo esc_html( $progress_planner_period_label ); ?>
					</td>
					<td style="text-align:center;">
						<?php
						echo (int) \ProgressPlanner\Progress_Planner::get_instance()
							->get_stats()
							->get_stat( 'posts' )
							->set_post_type( $progress_planner_post_type )
							->get_data( $progress_planner_period )['count'];
						?>
					</td>
					<td style="text-align:center;">
						<?php
						echo (int) \ProgressPlanner\Progress_Planner::get_instance()
							->get_stats()
							->get_stat( 'posts' )
							->set_post_type( $progress_planner_post_type )
							->get_data( $progress_planner_period )['word_count'];
						?>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>

		<p>
			<?php
			printf(
				/* translators: %d: number of posts. */
				esc_html__( 'All: %d', 'progress-planner' ),
				esc_html(
					\ProgressPlanner\Progress_Planner::get_instance()
						->get_stats()
						->get_stat( 'posts' )
						->set_post_type( $progress_planner_post_type )
						->get_data( 'all' )['publish']
				)
			);
			?>
		</p>

	<?php endforeach; ?>
</div>
