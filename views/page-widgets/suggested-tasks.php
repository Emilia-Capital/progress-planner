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
			/**
			 * TODO: Add badges icons by month. Files should have a month suffix.
			 */
			\progress_planner()->the_asset( 'images/badges/bold-blogger.svg' );
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

<hr>

<h2 class="prpl-widget-title">
	<?php \esc_html_e( 'Your monthly badges', 'progress-planner' ); ?>
</h2>
<div class="prpl-widget-content">
	<?php \esc_html_e( 'Check out your progress! Which badge will you unlock next?', 'progress-planner' ); ?>
</div>
<div class="progress-wrapper badge-group-monthly">
	<?php foreach ( \progress_planner()->get_badges()->get_badges( 'monthly' ) as $badge ) : ?>
		<span
			class="prpl-badge prpl-badge-<?php echo \esc_attr( $badge->get_id() ); ?>"
			data-value="<?php echo \esc_attr( $badge->progress_callback()['progress'] ); ?>"
		>
			<?php
			/**
			 * TODO: Add badges icons by month. Files should have a month suffix.
			 */
			\progress_planner()->the_asset(
				100 === (int) $badge->progress_callback()['progress']
					? 'images/badges/bold-blogger.svg'
					: 'images/badges/bold-blogger-bw.svg'
			);
			?>
			<p><?php echo \esc_html( $badge->get_name() ); ?></p>
		</span>
	<?php endforeach; ?>
</div>
<div class="prpl-widget-content">
	<?php \esc_html_e( 'Stay tuned for more badges!', 'progress-planner' ); ?>
</div>
