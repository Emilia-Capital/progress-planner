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
final class Badge_Streak extends Popover {

	/**
	 * The popover ID.
	 *
	 * @var string
	 */
	protected $id = 'badge-streak';

	/**
	 * Render the widget content.
	 *
	 * @return void
	 */
	protected function the_content() {
		?>
		<h2><?php \esc_html_e( 'You are on the right track!', 'progress-planner' ); ?></h2>
		<p><?php \esc_html_e( 'Find out which badges to unlock next and become a Progress Planner Professional!', 'progress-planner' ); ?></p>

		<div class="prpl-widgets-container in-popover">
			<div class="prpl-widget-wrapper in-popover">
				<h3><?php \esc_html_e( 'Don’t break your streak and stay active every week!', 'progress-planner' ); ?></h3>
				<p><?php \esc_html_e( 'Execute at least one website maintenance task every week. That could be publishing content, adding content, updating a post, or updating a plugin.', 'progress-planner' ); ?></p>
				<p><?php \esc_html_e( 'Not able to work on your site for a week? Use your streak freeze!', 'progress-planner' ); ?></p>
				<div id="popover-badge-streak-content">
					<?php $this->print_badges( 'maintenance' ); ?>
				</div>
				<?php $this->print_progressbar( 'maintenance' ); ?>
			</div>

			<div class="prpl-widget-wrapper in-popover">
				<h3><?php \esc_html_e( 'Keep adding posts and pages', 'progress-planner' ); ?></h3>
				<p><?php \esc_html_e( 'The more you write, the sooner you unlock new badges. You can earn level 1 of this badge immediately after installing the plugin if you have written 20 or more blog posts.', 'progress-planner' ); ?></p>
				<div id="popover-badge-streak-maintenance">
					<?php $this->print_badges( 'content' ); ?>
				</div>
				<?php $this->print_progressbar( 'content' ); ?>
			</div>
		</div>
		<div class="footer">
			<div class="string-freeze-explain">
				<h2><?php \esc_html_e( 'Streak freeze', 'progress-planner' ); ?></h2>
				<p><?php \esc_html_e( 'Going on a holiday? Or don\'t have any time this week? You can skip your website maintenance for a maximum of one week. Your streak will continue afterward.', 'progress-planner' ); ?></p>
			</div>
		</div>
		<?php
	}

	/**
	 * Print badges by category.
	 *
	 * @param string $category The category of badges.
	 *
	 * @return void
	 */
	public static function print_badges( $category ) {
		?>
		<?php foreach ( \progress_planner()->get_badges()->get_badges( $category ) as $badge ) : ?>
			<?php
			$badge_progress  = $badge->get_progress();
			$badge_completed = 100 === (int) $badge_progress['progress'];
			?>
			<span
				class="prpl-badge"
				data-value="<?php echo \esc_attr( $badge_progress['progress'] ); ?>"
			>
				<div class="inner">
					<?php $badge->the_icon( $badge_completed ); ?>
					<?php echo \esc_html( $badge->get_name() ); ?>
				</div>
				<p><?php echo \esc_html( $badge->get_description() ); ?></p>
			</span>
		<?php endforeach; ?>
		<?php
	}

	/**
	 * Print progressbar for badges.
	 *
	 * @param string $context The context of the progressbar. Can be 'content' or 'maintenance'.
	 *
	 * @return void
	 */
	public static function print_progressbar( $context ) {
		$badges = \progress_planner()->get_badges()->get_badges( $context );
		?>
		<div class="progress-badges">
			<span class="badges-popover-progress-total">
				<span style="width: <?php echo (int) end( $badges )->get_progress()['progress']; ?>%"></span>
			</span>
			<div class="indicators">
				<?php foreach ( $badges as $badge ) : ?>
					<div class="indicator">
						<?php $badge_progress = $badge->get_progress(); ?>
						<span class="indicator-label">
							<?php if ( 0 === (int) $badge_progress['remaining'] ) : ?>
								✔️
							<?php else : ?>
								<?php
								printf(
									'content' === $context
										? \esc_html(
											/* translators: The number of weeks remaining to complete the badge. */
											_n(
												'%s post to go',
												'%s posts to go',
												(int) $badge_progress['remaining'],
												'progress-planner'
											)
										) : \esc_html(
											/* translators: The number of weeks remaining to complete the badge. */
											_n(
												'%s week to go',
												'%s weeks to go',
												(int) $badge_progress['remaining'],
												'progress-planner'
											)
										),
									'<span class="number">' . \esc_html( \number_format_i18n( $badge_progress['remaining'] ) ) . '</span>'
								)
								?>
							<?php endif; ?>
						</span>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
