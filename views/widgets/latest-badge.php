<?php
/**
 * ProgressPlanner widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

use ProgressPlanner\Settings;

// Get the settings for badges.
$prpl_badges_settings      = Settings::get( 'badges' );
$prpl_latest_badge_date    = null;
$prpl_latest_badge_details = null;
foreach ( $prpl_badges_settings as $prpl_badge_id => $prpl_badge_settings ) {
	foreach ( $prpl_badge_settings as $prpl_badge_target => $prpl_badge_progress ) {
		if ( isset( $prpl_badge_progress['progress'] ) && 100 === $prpl_badge_progress['progress'] ) {
			if ( null === $prpl_latest_badge_date ||
				\DateTime::createFromFormat( 'Y-m-d H:i:s', $prpl_badge_progress['date'] )->format( 'U' ) > \DateTime::createFromFormat( 'Y-m-d H:i:s', $prpl_latest_badge_date )->format( 'U' )
			) {
				$prpl_latest_badge_date    = $prpl_badge_progress['date'];
				$prpl_latest_badge_details = [ $prpl_badge_id, $prpl_badge_target ];
			}
		}
	}
}

if ( $prpl_latest_badge_details ) {
	$prpl_latest_badge = \progress_planner()->get_badges()->get_badge( $prpl_latest_badge_details[0] );
	foreach ( $prpl_latest_badge['steps'] as $prpl_badge_step ) {
		if ( $prpl_badge_step['target'] === $prpl_latest_badge_details[1] ) {
			$prpl_latest_badge = $prpl_badge_step;
			break;
		}
	}
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
