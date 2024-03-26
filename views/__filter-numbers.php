<?php // phpcs:disable

Use ProgressPlanner\Base;

$numbers = [
	'content-publish'              => Base::$points_config['content']['publish'],
	'content-update'               => Base::$points_config['content']['update'],
	'content-delete'               => Base::$points_config['content']['delete'],
	'content-word-multiplier-100'  => Base::$points_config['content']['word-multipliers'][100],
	'content-word-multiplier-350'  => Base::$points_config['content']['word-multipliers'][350],
	'content-word-multiplier-1000' => Base::$points_config['content']['word-multipliers'][1000],
	'score-target'                 => Base::$points_config['score-target'],
	'maintenance'                  => Base::$points_config['maintenance'],
];

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
