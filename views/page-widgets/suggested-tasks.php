<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

use Progress_Planner\Suggested_Tasks;
use Progress_Planner\Badges\Badge\Monthly;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$monthly = Monthly::get_instances();

?>
<h2 class="prpl-widget-title">
	<?php \esc_html_e( 'Your monthly badge', 'progress-planner' ); ?>
</h2>

<div class="prpl-activities-gauge-container suggested-tasks">
	<div
		class="prpl-activities-gauge"
		style="
			--value:<?php echo (float) ( $this->get_score() / 100 ); ?>;
			--background: var(--prpl-background-orange);
			--max: 180deg;
			--start: 270deg;
			--color:var(--prpl-color-accent-orange)"
	>
		<span class="prpl-gauge-0">
			0
		</span>
		<span class="prpl-gauge-number">
			<?php echo (int) $this->get_score(); ?>
		</span>
		<span class="prpl-gauge-100">
			100
		</span>
	</div>
</div>

<p>
	<?php \esc_html_e( 'Bla bla bla', 'progress-planner' ); ?>
</p>

<hr>

<h2 class="prpl-widget-title">
	<?php \esc_html_e( 'Ravi\'s recomendations', 'progress-planner' ); ?>
</h2>

<ul style="display:none">
	<?php
	/**
	 * Allow filtering the template for suggested tasks items.
	 *
	 * @param string $template_file The template file path.
	 */
	$template_file = \apply_filters( 'progress_planner_suggested_todo_item_template', PROGRESS_PLANNER_DIR . '/views/suggested-tasks-item.php' );
	include $template_file; // phpcs:ignore PEAR.Files.IncludingFile.UseRequire
	?>
</ul>
<ul class="prpl-suggested-tasks-list"></ul>
<?php if ( Suggested_Tasks::maybe_celebrate_tasks() ) : ?>
	<script>
		alert( '<?php echo \esc_js( \esc_html__( 'Congratulations! You have completed all suggested tasks for this week.', 'progress-planner' ) ); ?>' );
	</script>
<?php endif; ?>

<hr>

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
			/**
			 * TODO: Add badges icons by month. Files should have a month suffix so we can include them directly.
			 */
			include $badge_completed // phpcs:ignore PEAR.Files.IncludingFile.UseRequire
				? \PROGRESS_PLANNER_DIR . '/assets/images/badges/bold-blogger.svg'
				: \PROGRESS_PLANNER_DIR . '/assets/images/badges/bold-blogger-bw.svg';
			?>
			<p><?php echo \esc_html( $badge->get_name() ); ?></p>
		</span>
	<?php endforeach; ?>
</div>
<div class="prpl-widget-content">
	<?php \esc_html_e( 'Stay tuned for more badges!', 'progress-planner' ); ?>
</div>
