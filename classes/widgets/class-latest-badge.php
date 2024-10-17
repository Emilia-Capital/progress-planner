<?php
/**
 * Progress_Planner widget.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Widgets;

use Progress_Planner\Badges;

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
	 * The endpoint to get the badge image.
	 *
	 * @var string
	 */
	const ENDPOINT = 'https://progressplanner.com/wp-json/progress-planner-saas/v1/share-badge-image?badge=';

	/**
	 * Render the widget content.
	 *
	 * @return void
	 */
	protected function the_content() {

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
			<img src="<?php echo \esc_url( self::ENDPOINT . $latest_badge_id ); ?>" alt="<?php echo \esc_attr( $latest_badge['name'] ); ?>" />
		<?php endif; ?>
		<?php
	}
}
