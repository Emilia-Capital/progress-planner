<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

use Progress_Planner\Badges;

// Get the latest completed badge.
$latest_badge_id = Badges::get_latest_completed_badge();
$latest_badge    = $latest_badge_id ? Badges::get_badge( $latest_badge_id ) : false;
?>
<h2 class="prpl-widget-title">
	<?php \esc_html_e( 'Latest new badge!', 'progress-planner' ); ?>
</h2>
<?php if ( ! $latest_badge ) : ?>
	<p><?php \esc_html_e( 'You haven\'t unlocked any badges yet. Hang on, you\'ll get there!', 'progress-planner' ); ?></p>
<?php else : ?>
	<p>
		<?php
		printf(
			/* translators: %s: The badge name. */
			\esc_html__( 'Wooohoooo! Congratulations! You have earned a new badge. You are now a %s.', 'progress-planner' ),
			'<strong>' . \esc_html( $latest_badge['name'] ) . '</strong>'
		);
		?>
	</p>
	<img src="<?php echo \esc_url( $this->endpoint . $latest_badge_id ); ?>" alt="<?php echo \esc_attr( $latest_badge['name'] ); ?>" />
<?php endif; ?>
