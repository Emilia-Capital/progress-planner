<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$gauge_score = $this->get_score();

?>
<div class="prpl-activities-gauge-container">
	<div
		class="prpl-activities-gauge"
		style="
			--value:<?php echo (float) ( $gauge_score / 100 ); ?>;
			--background: var(--prpl-background-orange);
			--max: 180deg;
			--start: 270deg;
			--color:<?php echo \esc_attr( $this->get_gauge_color( $gauge_score ) ); ?>"
	>
		<span class="prpl-gauge-0">
			0
		</span>
		<span class="prpl-gauge-number">
			<?php echo (int) $gauge_score; ?>
		</span>
		<span class="prpl-gauge-100">
			100
		</span>
	</div>
</div>
