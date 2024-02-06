<?php
/**
 * View for the admin page.
 *
 * @package ProgressPlanner
 */

$progress_planner = \ProgressPlanner\Progress_Planner::get_instance();
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

		<p>
			<?php
			printf(
				esc_html__( 'All: %d', 'progress-planner' ),
				esc_html(
					$progress_planner
						->get_stats()
						->get_stat( 'posts' )
						->set_post_type( $post_type )
						->get_data( 'all' )['publish']
				)
			);
			?>
		</p>

		<?php
		$lines = [
			[
				'period'          => esc_html__( 'Day', 'progress-planner' ),
				'period'          => 'day',
				'post_type'       => $post_type,
				'post_type_label' => $post_type_object->label,
			],
			[
				'period'          => esc_html__( 'Week', 'progress-planner' ),
				'period'          => 'week',
				'post_type'       => $post_type,
				'post_type_label' => $post_type_object->label,
			],
			[
				'period'          => esc_html__( 'Month', 'progress-planner' ),
				'period'          => 'month',
				'post_type'       => $post_type,
				'post_type_label' => $post_type_object->label,
			],
			[
				'period'          => esc_html__( 'Year', 'progress-planner' ),
				'period'          => 'year',
				'post_type'       => $post_type,
				'post_type_label' => $post_type_object->label,
			],
		];

		foreach ( $lines as $line ) {
			include PROGRESS_PLANNER_DIR . '/views/stat-posts.php';
		}
		?>
	<?php endforeach; ?>
</div>
