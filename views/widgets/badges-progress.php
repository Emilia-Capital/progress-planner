<?php
/**
 * ProgressPlanner widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

use ProgressPlanner\Badges;

$prpl_badges = [
	'content'     => [ 'wonderful-writer', 'awesome-author', 'notorious-novelist' ],
	'maintenance' => [ 'progress-professional', 'maintenance-maniac', 'super-site-specialist' ],
];

/**
 * Callback to get the progress color.
 *
 * @param int $progress The progress.
 *
 * @return string The color.
 */
$prpl_get_progress_color = function ( $progress ) {
	$color = 'var(--prpl-color-accent-red)';
	if ( $progress > 50 ) {
		$color = 'var(--prpl-color-accent-orange)';
	}
	if ( $progress > 75 ) {
		$color = 'var(--prpl-color-accent-green)';
	}
	return $color;
};

?>
<h2 class="prpl-widget-title">
	<?php esc_html_e( 'Badges progress', 'progress-planner' ); ?>
</h2>

<?php foreach ( $prpl_badges as $prpl_badge_category => $prpl_category_badges ) : ?>
	<div class="progress-wrapper">
		<?php foreach ( $prpl_category_badges as $prpl_category_badge ) : ?>
			<?php
			$prpl_badge_progress  = Badges::get_badge_progress( $prpl_category_badge );
			$prpl_badge_completed = 100 === (int) $prpl_badge_progress['percent'];
			$prpl_badge_args      = Badges::get_badge( $prpl_category_badge );
			?>
			<span
				class="prpl-badge"
				data-value="<?php echo \esc_attr( $prpl_badge_progress['percent'] ); ?>"
			>
				<?php
				include $prpl_badge_completed
					? $prpl_badge_args['icons-svg']['complete']['path']
					: $prpl_badge_args['icons-svg']['pending']['path'];
				?>
				<p><?php echo \esc_html( Badges::get_badge( $prpl_category_badge )['name'] ); ?></p>
			</span>
		<?php endforeach; ?>
	</div>
<?php endforeach; ?>
