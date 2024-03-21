<?php
/**
 * ProgressPlanner widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

use ProgressPlanner\Settings;
use ProgressPlanner\Badges;

// Get the settings for badges.
$prpl_badges_settings   = Settings::get( 'badges' );
$prpl_latest_badge_date = null;
$prpl_latest_badge      = null;
$prpl_latest_badge_id   = null;
foreach ( $prpl_badges_settings as $prpl_badge_id => $prpl_badge_settings ) {
	if ( isset( $prpl_badge_settings['progress'] ) && 100 === $prpl_badge_settings['progress'] ) {
		if ( null === $prpl_latest_badge_date ||
			\DateTime::createFromFormat( 'Y-m-d H:i:s', $prpl_badge_settings['date'] )->format( 'U' ) > \DateTime::createFromFormat( 'Y-m-d H:i:s', $prpl_latest_badge_date )->format( 'U' )
		) {
			$prpl_latest_badge_date = $prpl_badge_settings['date'];
			$prpl_latest_badge_id   = $prpl_badge_id;
		}
	}
}

if ( $prpl_latest_badge_id ) {
	$prpl_latest_badge = Badges::get_badge( $prpl_latest_badge_id );
}
?>
<h2 class="prpl-widget-title">
	<?php esc_html_e( 'Latest badge', 'progress-planner' ); ?>
</h2>
<?php if ( ! $prpl_latest_badge ) : ?>
	<p><?php esc_html_e( 'No badges earned yet.', 'progress-planner' ); ?></p>
<?php else : ?>
	<p>
		<?php
		printf(
			/* translators: %s: The badge name. */
			esc_html__( 'Congratulations! You have earned a new badge. You are now a %s.', 'progress-planner' ),
			'<strong>' . esc_html( $prpl_latest_badge['name'] ) . '</strong>'
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
				esc_html__( 'Yes! I have earned a new Progress Planner badge: I am a %s!', 'progress-planner' ),
				'<strong>' . esc_html( $prpl_latest_badge['name'] ) . '</strong>'
			);
			?>
		</div>
	</div>
<?php endif; ?>
