<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$prpl_widget     = \progress_planner()->get_admin__page()->get_widget( 'suggested-tasks' );
$prpl_percentage = $prpl_widget->get_score() / \Progress_Planner\Badges\Monthly::TARGET_POINTS;
?>
<h2 class="prpl-widget-title">
	<?php \esc_html_e( 'Your monthly badge', 'progress-planner' ); ?>
</h2>

<div class="prpl-gauge-container suggested-tasks">
	<div
		class="prpl-gauge"
		style="
			--value:<?php echo (float) $prpl_percentage; ?>;
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
			$prpl_badge->the_icon( \Progress_Planner\Badges\Monthly::TARGET_POINTS === (int) $prpl_widget->get_score() );
		}
		?>
		</span>
		<span class="prpl-gauge-100">
			<?php echo \esc_html( (string) \Progress_Planner\Badges\Monthly::TARGET_POINTS ); ?>
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

<div class="prpl-widget-content">
	<?php if ( 2024 === (int) gmdate( 'Y' ) ) : ?>
		<?php
		\progress_planner()->the_view(
			'page-widgets/parts/monthly-badge-2024.php',
			[
				'title_tag' => 'h2',
			]
		);
		?>
	<?php else : ?>

		<?php
		\progress_planner()->the_view(
			'page-widgets/parts/monthly-badges.php',
			[
				'title_year' => 2025,
			]
		);
		?>
	<?php endif; ?>
	<?php
	\progress_planner()->get_popover()->the_popover( 'monthly-badges' )->render_button(
		'',
		\esc_html__( 'Show all my badges!', 'progress-planner' )
	);
	\progress_planner()->get_popover()->the_popover( 'monthly-badges' )->render();
	?>
</div>
