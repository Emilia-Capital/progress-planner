<?php
/**
 * Monthly badges popover.
 *
 * @package Progress_Planner
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<h2><?php \esc_html_e( 'Your badges', 'progress-planner' ); ?></h2>
<div class="prpl-widgets-container in-popover">
	<div class="prpl-popover-column">
		<?php
		\progress_planner()->the_view(
			'page-widgets/parts/monthly-badge-2024.php',
			[
				'css_class' => 'prpl-widget-wrapper in-popover',
				'title_tag' => 'h3',
			]
		);
		?>

		<?php
		\progress_planner()->the_view(
			'page-widgets/parts/monthly-badges.php',
			[
				'css_class'  => 'in-popover',
				'title_year' => 2025,
			]
		);
		?>
	</div>

	<div class="prpl-popover-column">
		<?php
		$prpl_badges_groups = [
			'content'     => __( 'Writing badges', 'progress-planner' ),
			'maintenance' => __( 'Streak badges', 'progress-planner' ),
		];
		?>
		<?php foreach ( $prpl_badges_groups as $prpl_badge_group => $prpl_widget_title ) : ?>
			<div class="prpl-widget-wrapper prpl-widget-wrapper-<?php echo \esc_attr( $prpl_badge_group ); ?> in-popover  prpl-badge-streak">
				<h3 class="prpl-widget-title">
					<?php echo \esc_html( $prpl_widget_title ); ?>
				</h3>
				<div class="prpl-badges-container-achievements">
					<?php $prpl_group_badges = \progress_planner()->get_badges()->get_badges( $prpl_badge_group ); ?>
					<div class="progress-wrapper badge-group-<?php echo \esc_attr( $prpl_badge_group ); ?>">
						<?php foreach ( $prpl_group_badges as $prpl_badge ) : ?>
							<?php
							$prpl_badge_progress  = $prpl_badge->get_progress();
							$prpl_badge_completed = 100 === (int) $prpl_badge_progress['progress'];
							?>
							<span
								class="prpl-badge"
								data-value="<?php echo \esc_attr( $prpl_badge_progress['progress'] ); ?>"
							>
								<?php $prpl_badge->the_icon( $prpl_badge_completed ); ?>
								<p><?php echo \esc_html( $prpl_badge->get_name() ); ?></p>
							</span>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
