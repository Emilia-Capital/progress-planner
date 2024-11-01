<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$prpl_widget = \progress_planner()->get_admin()->page->get_widget( 'badge-streak' );

$prpl_widget_context_details = [];
if ( $prpl_widget->get_details( 'content' ) ) {
	$prpl_widget_context_details['content'] = [
		'background' => 'var(--prpl-background-blue)',
		'text'       => sprintf(
			\esc_html(
				/* translators: %s: The remaining number of posts or pages to write. */
				\_n(
					'Write %s new post or page and earn your next badge!',
					'Write %s new posts or pages and earn your next badge!',
					(int) $prpl_widget->get_details( 'content' )->get_progress()['remaining'],
					'progress-planner'
				)
			),
			\esc_html( \number_format_i18n( $prpl_widget->get_details( 'content' )->get_progress()['remaining'] ) )
		),
	];
}
if ( $prpl_widget->get_details( 'maintenance' ) ) {
	$prpl_widget_context_details['maintenance'] = [
		'background' => 'var(--prpl-background-red)',
		'text'       => sprintf(
			\esc_html(
				/* translators: %s: The remaining number of weeks. */
				\_n(
					'%s week to go to complete this streak!',
					'%s weeks to go to complete this streak!',
					(int) $prpl_widget->get_details( 'maintenance' )->get_progress()['remaining'],
					'progress-planner'
				)
			),
			\esc_html( \number_format_i18n( $prpl_widget->get_details( 'maintenance' )->get_progress()['remaining'] ) )
		),
	];
}

?>

<h2 class="prpl-widget-title">
	<?php
	\esc_html_e( 'Your streak badges', 'progress-planner' );
	\progress_planner()->get_popovers()->badges->render_button(
		'info-outline',
		'<span class="screen-reader-text">' . \esc_html__( 'More info', 'progress-planner' ) . '</span>'
	);
	\progress_planner()->get_popovers()->badges->render();
	?>
</h2>

<div class="prpl-latest-badges-wrapper">
	<?php foreach ( $prpl_widget_context_details as $context => $details ) : ?>
		<div class="prpl-badges-columns-wrapper">
			<div class="prpl-badge-wrapper" style="--background: <?php echo \esc_attr( $details['background'] ); ?>">
				<span
					class="prpl-badge"
					data-value="<?php echo \esc_attr( $prpl_widget->get_details( $context )->get_progress()['progress'] ); ?>"
				>
					<div
						class="prpl-badge-gauge"
						style="
							--value:<?php echo (float) ( $prpl_widget->get_details( $context )->get_progress()['progress'] / 100 ); ?>;
							--max: 360deg;
							--start: 180deg;
						">
						<?php $prpl_widget->get_details( $context )->the_icon( true ); ?>
					</div>
				</span>
				<span class="progress-percent"><?php echo \esc_attr( $prpl_widget->get_details( $context )->get_progress()['progress'] ); ?>%</span>
			</div>
			<div class="prpl-badge-content-wrapper">
				<h3><?php echo \esc_html( $prpl_widget->get_details( $context )->get_name() ); ?></h3>
				<p><?php echo \esc_html( $details['text'] ); ?></p>
			</div>
		</div>

		<hr>

	<?php endforeach; ?>
</div>

<h3><?php esc_html_e( 'Your achievements', 'progress-planner' ); ?></h3>
<div class="prpl-badges-container-achievements">
	<?php
	foreach ( [ 'content', 'maintenance' ] as $badge_group ) :
		$group_badges = \progress_planner()->get_badges()->get_badges( $badge_group );
		?>
		<div class="progress-wrapper badge-group-<?php echo \esc_attr( $badge_group ); ?>">
			<?php foreach ( $group_badges as $badge ) : ?>
				<?php
				$badge_progress  = $badge->get_progress();
				$badge_completed = 100 === (int) $badge_progress['progress'];
				?>
				<span
					class="prpl-badge"
					data-value="<?php echo \esc_attr( $badge_progress['progress'] ); ?>"
				>
					<?php $badge->the_icon( $badge_completed ); ?>
					<p><?php echo \esc_html( $badge->get_name() ); ?></p>
				</span>
			<?php endforeach; ?>
		</div>
	<?php endforeach; ?>
</div>
