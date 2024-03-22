<?php // phpcs:disable

$numbers = \progress_planner()->get_dev_config();
?>
<h2>DEV - Weights, scores, multipliers</h2>
<form id="prpl-dev-stats-numbers">
	<?php foreach ( $numbers as $key => $value ) : ?>
		<label
			for="prpl-dev-stats-numbers-<?php echo esc_attr( $key ); ?>"
			style="display:flex;font-size:12px;justify-content:space-between;"
		>
			<?php echo esc_html( $key ); ?>
			<input
				type="number"
				name="<?php echo esc_attr( $key ); ?>"
				value="<?php echo esc_attr( $value ); ?>"
				style="width: 5em;"
				step="0.01"
			>
		</label>
		<hr>
	<?php endforeach; ?>
	<button type="submit" class="button button-primary">Try it</button>
</form>
