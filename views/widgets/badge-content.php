<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

$details = $this->get_badge_details();
?>
<div class="prpl-badges-columns-wrapper">
	<div class="prpl-badge-wrapper">
		<span
			class="prpl-badge"
			data-value="<?php echo \esc_attr( $details['progress']['progress'] ); ?>"
		>
			<div
				class="prpl-badge-gauge"
				style="
					--value:<?php echo (float) ( $details['progress']['progress'] / 100 ); ?>;
					--max: 360deg;
					--start: 180deg;
				">
				<?php require $details['badge']['icons-svg']['complete']['path']; ?>
			</div>
		</span>
		<span class="progress-percent"><?php echo \esc_attr( $details['progress']['progress'] ); ?>%</span>
	</div>
	<div class="prpl-badge-content-wrapper">
		<h2 class="prpl-widget-title">
			<?php echo \esc_html( $details['badge']['name'] ); ?>
		</h2>
		<p>
			<?php
			printf(
				\esc_html(
					/* translators: %s: The remaining number of posts or pages to write. */
					\_n(
						'Write %s new post or page and earn your next badge!',
						'Write %s new posts or pages and earn your next badge!',
						(int) $details['progress']['remaining'],
						'progress-planner'
					)
				),
				\esc_html( \number_format_i18n( $details['progress']['remaining'] ) )
			);
			?>
		</p>
	</div>
</div>
