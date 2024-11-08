<?php
/**
 * Template part.
 *
 * @package Progress_Planner
 */

$prpl_badges = \progress_planner()->get_badges()->get_badges( $prpl_context );
?>
<div class="progress-badges">
	<span class="badges-popover-progress-total">
		<span style="width: <?php echo (int) end( $prpl_badges )->get_progress()['progress']; ?>%"></span>
	</span>
	<div class="indicators">
		<?php foreach ( $prpl_badges as $prpl_badge ) : ?>
			<div class="indicator">
				<?php $prpl_badge_progress = $prpl_badge->get_progress(); ?>
				<span class="indicator-label">
					<?php if ( 0 === (int) $prpl_badge_progress['remaining'] ) : ?>
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
										(int) $prpl_badge_progress['remaining'],
										'progress-planner'
									)
								) : \esc_html(
									/* translators: The number of weeks remaining to complete the badge. */
									_n(
										'%s week to go',
										'%s weeks to go',
										(int) $prpl_badge_progress['remaining'],
										'progress-planner'
									)
								),
							'<span class="number">' . \esc_html( \number_format_i18n( $prpl_badge_progress['remaining'] ) ) . '</span>'
						)
						?>
					<?php endif; ?>
				</span>
			</div>
		<?php endforeach; ?>
	</div>
</div>
<?php
