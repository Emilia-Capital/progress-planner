<?php
/**
 * View for the admin page.
 *
 * @package ProgressPlanner
 */

// TODO: This pre-populates the option with previous weeks and months values.
// This should be moved to a separate function that can be called from the admin page.
foreach ( [ 1, 2, 3, 4, 5 ] as $prpl_i ) {
	\ProgressPlanner\Progress_Planner::get_instance()
		->get_settings()
		->update_value_previous_unsaved_interval( 'weeks', $prpl_i );
	\ProgressPlanner\Progress_Planner::get_instance()
		->get_settings()
		->update_value_previous_unsaved_interval( 'months', $prpl_i );
}

?>

<div class="wrap">
	<h1><?php esc_html_e( 'Progress Planner', 'progress-planner' ); ?></h1>

	<h2><?php esc_html_e( 'Post Types', 'progress-planner' ); ?></h2>

	<?php $prpl_post_types = get_post_types( [], 'objects' ); ?>
	<?php foreach ( $prpl_post_types as $prpl_post_type => $prpl_post_type_object ) : ?>
		<?php if ( ! $prpl_post_type_object->public ) : ?>
			<?php continue; ?>
		<?php endif; ?>
		<h3><?php echo esc_html( $prpl_post_type_object->label ); ?></h3>
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
			<?php foreach ( [ 'weeks', 'months' ] as $prpl_interval_type ) : ?>
				<?php foreach ( [ 0, 1, 2, 3 ] as $prpl_interval_value ) : ?>
					<tr>
						<td><?php echo esc_html( '-' . $prpl_interval_value . ' ' . $prpl_interval_type ); ?></td>
						<td style="text-align:center;">
							<?php
							echo esc_html(
								\ProgressPlanner\Progress_Planner::get_instance()->get_settings()->get_value(
									'stats',
									(int) gmdate(
										'Y',
										strtotime( '-' . $prpl_interval_value . ' ' . $prpl_interval_type )
									),
									$prpl_interval_type,
									(int) gmdate(
										( 'weeks' === $prpl_interval_type ) ? 'W' : 'n',
										strtotime( '-' . $prpl_interval_value . ' ' . $prpl_interval_type )
									),
									'posts',
									$prpl_post_type
								)
							);
							?>
						</td>
						<td style="text-align:center;">
							<?php
							echo esc_html(
								\ProgressPlanner\Progress_Planner::get_instance()->get_settings()->get_value(
									'stats',
									gmdate( 'Y', strtotime( '-' . $prpl_interval_value . ' ' . $prpl_interval_type ) ),
									$prpl_interval_type,
									gmdate( 'n', strtotime( '-' . $prpl_interval_value . ' ' . $prpl_interval_type ) ),
									'words',
									$prpl_post_type
								)
							);
							?>
						</td>
					</tr>
				<?php endforeach; ?>
				<tr><td colspan="3"><hr></td></tr>
			<?php endforeach; ?>
		</table>
	<?php endforeach; ?>
</div>
