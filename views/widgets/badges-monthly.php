<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

use Progress_Planner\Badges\Badge\Monthly;

$monthly = Monthly::get_instances();
?>
<h2 class="prpl-widget-title">
	<?php \esc_html_e( 'Your monthly badges', 'progress-planner' ); ?>
</h2>
<div class="prpl-widget-content">
	<?php \esc_html_e( 'Check out your progress! Which badge will you unlock next?', 'progress-planner' ); ?>
</div>
<div class="progress-wrapper badge-group-monthly">
	<?php foreach ( $monthly as $month => $badge ) : ?>
		<?php
		$badge_progress  = $badge->progress_callback();
		$badge_completed = 100 === (int) $badge_progress['progress'];
		$month_key       = str_replace( 'monthly-', '', $badge->get_id() );
		?>
		<span
			class="prpl-badge"
			data-value="<?php echo \esc_attr( $badge_progress['progress'] ); ?>"
		>
			<?php
			include $badge_completed // phpcs:ignore PEAR.Files.IncludingFile.UseRequire
				? $badge->get_icons_svg()['complete'][ $month_key ]['path']
				: $badge->get_icons_svg()['pending'][ $month_key ]['path'];
			?>
			<p><?php echo \esc_html( $badge->get_name() ); ?></p>
		</span>
	<?php endforeach; ?>
</div>
<div class="prpl-widget-content">
	<?php \esc_html_e( 'Stay tuned for more badges!', 'progress-planner' ); ?>
</div>
