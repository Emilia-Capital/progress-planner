<?php
/**
 * Handler for posts activities.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Activities;

use ProgressPlanner\Activities\Content;
use ProgressPlanner\Date;

/**
 * Handler for posts activities.
 */
class Content_Helpers {

	/**
	 * Get an array of post-types names for the stats.
	 *
	 * @return string[]
	 */
	public static function get_post_types_names() {
		$post_types = \get_post_types( [ 'public' => true ] );
		unset( $post_types['attachment'] );

		return array_keys( $post_types );
	}

	/**
	 * Get words count from content.
	 *
	 * This method will render shortcodes, blocks,
	 * and strip HTML before counting the words.
	 *
	 * @param string $content The content.
	 *
	 * @return int
	 */
	public static function get_word_count( $content ) {
		// Parse blocks and shortcodes.
		$content = \do_blocks( \do_shortcode( $content ) );

		// Strip HTML.
		$content = \wp_strip_all_tags( $content, true );

		// Count words.
		return \str_word_count( $content );
	}

	/**
	 * Get Activity from WP_Post object.
	 *
	 * @param \WP_Post $post The post object.
	 *
	 * @return \ProgressPlanner\Activities\Content
	 */
	public static function get_activity_from_post( $post ) {
		$type = 'publish' === $post->post_status ? 'publish' : 'update';
		$date = 'publish' === $post->post_status ? $post->post_date : $post->post_modified;

		$activity = new Content();
		$activity->set_category( 'content' );
		$activity->set_type( $type );
		$activity->set_date( Date::get_datetime_from_mysql_date( $date ) );
		$activity->set_data_id( $post->ID );
		return $activity;
	}

	/**
	 * Get posts by dates.
	 *
	 * @param array $query_args The query arguments. See WP_Query for more details.
	 *
	 * @return array
	 */
	private static function get_posts_stats_by_query( $query_args ) {
		$key           = md5( wp_json_encode( $query_args ) );
		static $cached = [];
		if ( ! isset( $cached[ $key ] ) ) {
			$cached[ $key ] = get_posts(
				wp_parse_args(
					$query_args,
					[ 'posts_per_page' => -1 ]
				)
			);
		}

		$posts = $cached[ $key ];

		return [
			'count' => count( $posts ),
			'words' => array_sum( array_map( [ __CLASS__, 'get_word_count' ], wp_list_pluck( $posts, 'post_content' ) ) ),
		];
	}

	/**
	 * Get posts stats from an array of post-IDs.
	 *
	 * @param int[] $post_ids The post-IDs.
	 *
	 * @return array
	 */
	public static function get_posts_stats_by_ids( $post_ids ) {
		return self::get_posts_stats_by_query(
			[
				'post__in'       => $post_ids,
				'posts_per_page' => count( $post_ids ),
			]
		);
	}
}
