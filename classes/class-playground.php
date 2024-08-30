<?php
/**
 * File that creates a better playground environment for the Progress Planner plugin.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner;

/**
 * Class Playground
 */
class Playground {

	/**
	 * Constructor.
	 */
	public function __construct() {
		\add_action( 'init', [ $this, 'register_hooks' ] );
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		if ( ! \get_option( 'progress_planner_license_key', false ) ) {
			$this->generate_data();
			\update_option( 'progress_planner_license_key', str_replace( ' ', '-', $this->create_random_string( 20 ) ) );
			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize -- This is a demo.
			\update_option( 'progress_planner_todo', unserialize( 'a:2:{i:0;a:2:{s:7:"content";s:48:"Update a post to see that the plugin tracks that";s:4:"done";b:0;}i:1;a:2:{s:7:"content";s:24:"Try out Progress Planner";s:4:"done";b:0;}}' ) );
		}
		\add_action( 'admin_notices', [ $this, 'admin_notices' ] );
	}

	/**
	 * Print an admin notice helping people.
	 *
	 * @return void
	 */
	public function admin_notices() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- We're not processing any data.
		if ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'progress-planner' ) {
			return;
		}

		echo '<div id="progress-planner-playground-notice" class="notice notice-success" style="margin-bottom:40px">';
		echo '<p style="max-width:600px;"><strong>' . esc_html__( 'Progress Planner demo', 'progress-planner' ) . '</strong><br>';
		\esc_html_e( 'This is a demo of Progress Planner. We\'ve prefilled this site with some content to show you what the reports in Progress Planner look like. We\'ve also added a few to-do\'s for you, you can see these here and on your dashoard.', 'progress-planner' );
		echo '</p>';
		echo '</div>';
	}

	/**
	 * Generate random posts & terms.
	 *
	 * @return void
	 */
	public function generate_data() {
		for ( $i = 0; $i < 50; $i++ ) {
			$this->create_random_post();
		}
		// One post for today.
		$this->create_random_post( false );
	}

	/**
	 * Create a random post.
	 *
	 * @param bool $random_date Whether to use a random date or not.
	 *
	 * @return int Post ID.
	 */
	private function create_random_post( $random_date = true ) {
		$postarr = [
			'post_title'   => str_replace( '.', '', $this->create_random_string( 5 ) ),
			'post_content' => $this->create_random_string( wp_rand( 200, 500 ) ),
			'post_status'  => 'publish',
			'post_type'    => 'post',
			'post_date'    => $this->get_random_date_last_12_months(),
		];

		if ( ! $random_date ) {
			unset( $postarr['post_date'] );
		}
		return \wp_insert_post( $postarr );
	}

	/**
	 * Generate a random date within the last 12 months.
	 *
	 * @return string Random date in 'Y-m-d H:i:s' format.
	 */
	private function get_random_date_last_12_months() {
		// Current time.
		// phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested -- This works fine for these purposes.
		$now = \current_time( 'timestamp' );

		// Timestamp for 12 months ago.
		$last_year = strtotime( '-12 months', $now );

		// Generate a random timestamp between last year and now.
		$random_timestamp = wp_rand( $last_year, $now );

		// Format the random timestamp as a MySQL datetime string.
		return gmdate( 'Y-m-d H:i:s', $random_timestamp );
	}

	/**
	 * Create a random string of content consisting of sentences.
	 *
	 * @param int $length Number of words in total to create across all sentences.
	 *
	 * @return string Random string of sentences.
	 */
	private function create_random_string( $length ) {
		$words = [ 'the', 'and', 'have', 'that', 'for', 'you', 'with', 'say', 'this', 'they', 'but', 'his', 'from', 'not', 'she', 'as', 'what', 'their', 'can', 'who', 'get', 'would', 'her', 'all', 'make', 'about', 'know', 'will', 'one', 'time', 'there', 'year', 'think', 'when', 'which', 'them', 'some', 'people', 'take', 'out', 'into', 'just', 'see', 'him', 'your', 'come', 'could', 'now', 'than', 'like', 'other', 'how', 'then', 'its', 'our', 'two', 'more', 'these', 'want', 'way', 'look', 'first', 'also', 'new', 'because', 'day', 'use', 'man', 'find', 'here', 'thing', 'give', 'many', 'well', 'only', 'those', 'tell', 'very', 'even', 'back', 'any', 'good', 'woman', 'through', 'life', 'child', 'work', 'down', 'may', 'after', 'should', 'call', 'world', 'over', 'school', 'still', 'try', 'last', 'ask', 'need' ];

		$sentences       = '';
		$words_remaining = $length;

		while ( $words_remaining > 0 ) {
			// Randomly decide the length of the current sentence (between 8 and 12 words).
			$sentence_length  = min( wp_rand( 8, 12 ), $words_remaining );
			$words_remaining -= $sentence_length;

			// Select random words for the sentence.
			$word_keys = array_rand( $words, $sentence_length );
			$sentence  = '';

			foreach ( (array) $word_keys as $key ) {
					$sentence .= $words[ $key ] . ' ';
			}

			// Capitalize the first word and add a period at the end.
			$sentences .= ucfirst( trim( $sentence ) ) . '. ';
		}

		return trim( $sentences );
	}
}
