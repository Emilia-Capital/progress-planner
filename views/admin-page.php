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

	<?php $post_types = get_post_types( [], 'objects' ); ?>
	<?php foreach ( $post_types as $post_type => $post_type_object ) : ?>
		<?php if ( ! $post_type_object->public ) : ?>
			<?php continue; ?>
		<?php endif; ?>
		<h3><?php echo esc_html( $post_type_object->label ); ?></h3>
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
			] as $period => $period_label ) :
			?>
				<tr>
					<td>
						<?php echo esc_html( $period_label ); ?>
					</td>
					<td style="text-align:center;">
						<?php echo (int) \ProgressPlanner\Progress_Planner::get_instance()
							->get_stats()
							->get_stat( 'posts' )
							->set_post_type( $post_type )
							->get_data( $period )['count'];
						?>
					</td>
					<td style="text-align:center;">
						<?php echo (int) \ProgressPlanner\Progress_Planner::get_instance()
							->get_stats()
							->get_stat( 'posts' )
							->set_post_type( $post_type )
							->get_data( $period )['word_count'];
						?>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>

		<p>
			<?php
			printf(
				esc_html__( 'All: %d', 'progress-planner' ),
				esc_html(
					\ProgressPlanner\Progress_Planner::get_instance()
						->get_stats()
						->get_stat( 'posts' )
						->set_post_type( $post_type )
						->get_data( 'all' )['publish']
				)
			);
			?>
		</p>

	<?php endforeach; ?>
</div>
