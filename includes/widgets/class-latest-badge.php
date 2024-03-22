<?php
/**
 * ProgressPlanner widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Widgets;

use ProgressPlanner\Badges;

/**
 * Latest badge widget.
 */
final class Latest_Badge extends Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'latest-badge';

	/**
	 * Render the widget content.
	 *
	 * @return void
	 */
	protected function the_content() {

		// Get the latest completed badge.
		$latest_badge_id = Badges::get_latest_completed_badge();

		if ( $latest_badge_id ) {
			$latest_badge = Badges::get_badge( $latest_badge_id );
		}
		?>
		<h2 class="prpl-widget-title">
			<?php \esc_html_e( 'Latest badge', 'progress-planner' ); ?>
		</h2>
		<?php if ( ! $latest_badge ) : ?>
			<p><?php \esc_html_e( 'No badges earned yet.', 'progress-planner' ); ?></p>
		<?php else : ?>
			<p>
				<?php
				printf(
					/* translators: %s: The badge name. */
					\esc_html__( 'Congratulations! You have earned a new badge. You are now a %s.', 'progress-planner' ),
					'<strong>' . \esc_html( $latest_badge['name'] ) . '</strong>'
				);
				?>
			</p>
			<div class="prpl-badge-wrapper prpl-badge-latest two-col narrow">
				<div class="badge-svg">
					<?php include $latest_badge['icons-svg']['complete']['path']; ?>
				</div>
				<div>
					<?php
					printf(
						/* translators: %s: The badge name. */
						\esc_html__( 'Yes! I have earned a new Progress Planner badge: I am a %s!', 'progress-planner' ),
						'<strong>' . \esc_html( $latest_badge['name'] ) . '</strong>'
					);
					?>
				</div>
			</div>
		<?php endif; ?>
		<?php
	}
}
