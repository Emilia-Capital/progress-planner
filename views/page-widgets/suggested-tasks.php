<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

use Progress_Planner\Badges\Monthly;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$prpl_widget = \progress_planner()->get_admin()->page->get_widget( 'suggested-tasks' );
$percentage  = $prpl_widget->get_score() / Monthly::TARGET_POINTS;
?>
<h2 class="prpl-widget-title">
	<?php \esc_html_e( 'Your monthly badge', 'progress-planner' ); ?>
</h2>

<div class="prpl-activities-gauge-container suggested-tasks">
	<div
		class="prpl-activities-gauge"
		style="
			--value:<?php echo (float) $percentage; ?>;
			--background: var(--prpl-background-orange);
			--max: 180deg;
			--start: 270deg;
			--color:var(--prpl-color-accent-orange)"
	>
		<span class="prpl-gauge-0">
			0
		</span>
		<span class="prpl-gauge-badge">
		<?php
		$prpl_badge = \progress_planner()
			->get_badges()
			->get_badge( 'monthly-' . gmdate( 'Y' ) . '-m' . (int) gmdate( 'm' ) );

		if ( $prpl_badge ) {
			$prpl_badge->the_icon( Monthly::TARGET_POINTS === (int) $prpl_widget->get_score() );
		}
		?>
		</span>
		<span class="prpl-gauge-100">
			<?php echo \esc_html( (string) Monthly::TARGET_POINTS ); ?>
		</span>
	</div>
</div>

<div class="prpl-widget-content-points">
	<span><?php \esc_html_e( 'Progress monthly badge', 'progress-planner' ); ?></span>
	<span class="prpl-widget-content-points-number">
		<?php echo (int) $prpl_widget->get_score(); ?>pt
	</span>
</div>

<hr>

<h2 class="prpl-widget-title">
	<?php \esc_html_e( 'Ravi\'s recommendations', 'progress-planner' ); ?>
</h2>

<ul style="display:none">
	<?php \progress_planner()->the_view( 'views/suggested-tasks-item.php' ); ?>
</ul>
<ul class="prpl-suggested-tasks-list"></ul>

<?php $badges = \progress_planner()->get_badges()->get_badges( 'monthly' ); ?>
<?php if ( $badges ) : ?>
	<?php
		$badges_per_row = 3;
		$badges_count   = count( $badges );
		$total_rows     = (int) ceil( $badges_count / $badges_per_row );

		// We need to know current month badge position.
		$current_month_id       = 'monthly-' . gmdate( 'Y' ) . '-m' . (int) gmdate( 'm' );
		$current_month_position = 0;
		$current_month_found    = false;

		// Get badges html.
		ob_start();
	foreach ( $badges as $badge ) :
		?>
			<?php
			if ( ! $current_month_found ) {
				++$current_month_position;

				if ( $current_month_id === $badge->get_id() ) {
					$current_month_found = true;
				}
			}
			?>
			<span
				class="prpl-badge prpl-badge-<?php echo \esc_attr( $badge->get_id() ); ?>"
				data-value="<?php echo \esc_attr( $badge->progress_callback()['progress'] ); ?>"
			>
			<?php $badge->the_icon( 100 === (int) $badge->progress_callback()['progress'] ); ?>
				<p><?php echo \esc_html( $badge->get_name() ); ?></p>
			</span>
		<?php
		endforeach;
		$badges_html = ob_get_clean();

		$scroll_to_row = (int) ceil( $current_month_position / $badges_per_row );

		// Always display the previous row, so user can see already completed badges.
	if ( 1 < $scroll_to_row ) {
		--$scroll_to_row;
	}

		// If we're in the first row, the top arrow should be disabled.
		$top_arrow_disabled = 1 === $scroll_to_row;

		// If we're in the row before last, the bottom arrow should be disabled (since we have 2 rows visible at a time).
		$bottom_arrow_disabled = ( $total_rows - 1 ) === $scroll_to_row;
	?>
	<hr>

	<h2 class="prpl-widget-title">
		<?php \esc_html_e( 'Your monthly badges', 'progress-planner' ); ?>
	</h2>
	<div class="prpl-widget-content">
		<?php \esc_html_e( 'Check out your progress! Which badge will you unlock next?', 'progress-planner' ); ?>
	</div>

	<div class="progress-wrapper badge-group-monthly">
		<?php if ( 2 * $badges_per_row < $badges_count ) : ?>
			<div class="prpl-badge-row-button-wrapper <?php echo $top_arrow_disabled ? 'prpl-badge-row-button-disabled' : ''; ?>">
				<button class="prpl-badge-row-button prpl-badge-row-button-up">
					<span class="dashicons dashicons-arrow-up-alt2"></span>
				</button>
			</div>
		<?php endif; ?>

		<div class="prpl-badge-row-wrapper">
			<div class="prpl-badge-row-wrapper-inner" style="--prpl-current-row: <?php echo \esc_attr( (string) $scroll_to_row ); ?>">
				<?php echo $badges_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
		<?php if ( 2 * $badges_per_row < $badges_count ) : ?>
			<div class="prpl-badge-row-button-wrapper <?php echo $bottom_arrow_disabled ? 'prpl-badge-row-button-disabled' : ''; ?>">
				<button class="prpl-badge-row-button prpl-badge-row-button-down">
					<span class="dashicons dashicons-arrow-down-alt2"></span>
				</button>
			</div>
		<?php endif; ?>
	</div>
	<div class="prpl-widget-content">
		<?php \esc_html_e( 'Stay tuned for more badges!', 'progress-planner' ); ?>
	</div>
<?php endif; ?>
