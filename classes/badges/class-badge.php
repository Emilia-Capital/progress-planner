<?php
/**
 * Badge object.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Badges;

/**
 * Badge class.
 */
abstract class Badge {

	/**
	 * The badge ID.
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * The icon URL.
	 *
	 * @var string
	 */
	protected $icon_url;

	/**
	 * Get the badge ID.
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get the badge name.
	 *
	 * @return string
	 */
	abstract public function get_name();

	/**
	 * Get the badge description.
	 *
	 * @return string
	 */
	abstract public function get_description();

	/**
	 * Progress callback.
	 *
	 * @return array
	 */
	abstract public function progress_callback();

	/**
	 * Get the saved progress.
	 *
	 * @return array
	 */
	protected function get_saved() {
		return \progress_planner()->get_settings()->get( [ 'badges', $this->id ], [] );
	}

	/**
	 * Get the badge progress.
	 *
	 * @return array
	 */
	public function get_progress() {
		return $this->progress_callback();
	}

	/**
	 * Save the progress.
	 *
	 * @param array $progress The progress to save.
	 *
	 * @return void
	 */
	protected function save_progress( $progress ) {
		$progress['date'] = ( new \DateTime() )->format( 'Y-m-d H:i:s' );
		\progress_planner()->get_settings()->set( [ 'badges', $this->id ], $progress );
	}

	/**
	 * Get the icon URL.
	 *
	 * @param bool $complete Whether the badge is complete.
	 *
	 * @return string
	 */
	protected function get_icon_url( $complete = true ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		return PROGRESS_PLANNER_URL . '/assets/images/badges/' . $this->get_id() . '.svg';
	}

	/**
	 * Print the icon.
	 *
	 * @param bool $complete Whether the badge is complete.
	 *
	 * @return void
	 */
	public function the_icon( $complete = false ) {
		?>
		<img
			class="prpl-badge-icon-image <?php echo $complete ? 'complete' : 'incomplete'; ?>"
			src="<?php echo esc_url( $this->get_icon_url( $complete ) ); ?>"
			alt=""
		>
		<?php
	}
}
