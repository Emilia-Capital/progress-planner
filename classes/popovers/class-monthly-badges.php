<?php
/**
 * Badges popover.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Popovers;

/**
 * Badges popover.
 */
final class Monthly_Badges extends Popover {

	/**
	 * The popover ID.
	 *
	 * @var string
	 */
	protected $id = 'monthly-badges';

	/**
	 * Render the widget content.
	 *
	 * @return void
	 */
	protected function the_content() {
		?>
		<h2><?php \esc_html_e( 'Your badges', 'progress-planner' ); ?></h2>
		<div class="prpl-widgets-container in-popover">

			<div class="prpl-popover-column">
				<div class="prpl-widget-wrapper in-popover">
					<h3 class="prpl-widget-title"><?php esc_html_e( 'Monthly badges 2024', 'progress-planner' ); ?></h3>
					<div class="prpl-ravi-reward-container">
						<span class="prpl-ravi-reward-icon">
							<?php \progress_planner()->the_asset( 'images/badges/monthly-badge-default.svg' ); ?>
						</span>
						<p><?php esc_html_e( 'Ravi\'s remarkable Reward', 'progress-planner' ); ?></p>
					</div>
				</div>

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
				$badges_groups = [
					'content'     => __( 'Writing badges', 'progress-planner' ),
					'maintenance' => __( 'Streak badges', 'progress-planner' ),
				];
				foreach ( $badges_groups as $badge_group => $widget_title ) :
					?>
					<div class="prpl-widget-wrapper prpl-widget-wrapper-<?php echo \esc_attr( $badge_group ); ?> in-popover  prpl-badge-streak">
						<h3 class="prpl-widget-title">
							<?php echo \esc_html( $widget_title ); ?>
						</h3>
						<div class="prpl-badges-container-achievements">
						<?php
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
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
