<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$prpl_widget = \progress_planner()->get_admin()->page->get_widget( 'activity-scores' );

?>
<div class="prpl-activities-gauge-container activities-scores">
	<div
		class="prpl-activities-gauge"
		style="
			--value:<?php echo (float) ( $prpl_widget->get_score() / 100 ); ?>;
			--max: 180deg;
			--start: 270deg;
			--color:<?php echo \esc_attr( $prpl_widget->get_gauge_color( $prpl_widget->get_score() ) ); ?>"
	>
		<span class="prpl-gauge-0">
			0
		</span>
		<span class="prpl-gauge-number">
			<?php echo (int) $prpl_widget->get_score(); ?>
		</span>
		<span class="prpl-gauge-100">
			100
		</span>
	</div>
</div>
