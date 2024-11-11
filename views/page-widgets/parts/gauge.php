<?php
/**
 * Gauge view.
 *
 * @package Progress_Planner
 */

$prpl_gauge_details['classes'] = isset( $prpl_gauge_details['classes'] )
	? ' ' . trim( $prpl_gauge_details['classes'] )
	: '';
?>
<div class="prpl-gauge-container<?php echo \esc_attr( $prpl_gauge_details['classes'] ); ?>" style="background: <?php echo \esc_attr( $prpl_gauge_details['background'] ); ?>">
	<div
		class="prpl-gauge"
		style="
			--value:<?php echo (float) $prpl_gauge_details['value']; ?>;
			--background: <?php echo \esc_attr( $prpl_gauge_details['background'] ); ?>;
			--max: 180deg;
			--start: 270deg;
			--color:<?php echo \esc_attr( $prpl_gauge_details['color'] ); ?>"
	>
		<span class="prpl-gauge-0">0</span>
		<?php if ( isset( $prpl_gauge_details['badge'] ) ) : ?>
			<span class="prpl-gauge-badge">
				<?php $prpl_gauge_details['badge']->the_icon( $prpl_gauge_details['badge_completed'] ); ?>
			</span>
		<?php elseif ( isset( $prpl_gauge_details['number'] ) ) : ?>
			<span class="prpl-gauge-number"><?php echo \esc_html( $prpl_gauge_details['number'] ); ?></span>
		<?php endif; ?>
		<span class="prpl-gauge-100"><?php echo \esc_html( $prpl_gauge_details['max'] ); ?></span>
	</div>
</div>
