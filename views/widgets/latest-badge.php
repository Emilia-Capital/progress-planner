<?php
/**
 * ProgressPlanner widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

use ProgressPlanner\Badges;

// Get the latest completed badge.
$prpl_latest_badge_id = Badges::get_latest_completed_badge();

if ( $prpl_latest_badge_id ) {
	$prpl_latest_badge = Badges::get_badge( $prpl_latest_badge_id );
}
?>
<h2 class="prpl-widget-title">
	<?php \esc_html_e( 'Latest badge', 'progress-planner' ); ?>
</h2>
<?php if ( ! $prpl_latest_badge ) : ?>
	<p><?php \esc_html_e( 'No badges earned yet.', 'progress-planner' ); ?></p>
<?php else : ?>
	<p>
		<?php
		printf(
			/* translators: %s: The badge name. */
			\esc_html__( 'Congratulations! You have earned a new badge. You are now a %s.', 'progress-planner' ),
			'<strong>' . \esc_html( $prpl_latest_badge['name'] ) . '</strong>'
		);
		?>
	</p>
	<div class="prpl-badge-wrapper prpl-badge-latest two-col narrow">
		<div class="badge-svg">
			<?php include $prpl_latest_badge['icons-svg']['complete']['path']; ?>
		</div>
		<div>
			<?php
			printf(
				/* translators: %s: The badge name. */
				\esc_html__( 'Yes! I have earned a new Progress Planner badge: I am a %s!', 'progress-planner' ),
				'<strong>' . \esc_html( $prpl_latest_badge['name'] ) . '</strong>'
			);
			?>
		</div>
	</div>
<?php endif; ?>
