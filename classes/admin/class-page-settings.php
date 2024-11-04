<?php
/**
 * The admin pages and functionality for Progress Planner.
 *
 * @package Progress_Planner/Admin
 */

namespace Progress_Planner\Admin;

use Progress_Planner\Page_Types;

/**
 * Admin class.
 */
class Page_Settings {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Add the admin menu page.
		\add_action( 'admin_menu', [ $this, 'add_admin_menu_page' ] );

		// Add AJAX hooks to save options.
		\add_action( 'wp_ajax_prpl_settings_form', [ $this, 'store_settings_form_options' ] );
	}

	/**
	 * Add admin-menu page, as a submenu in the progress-planner menu.
	 *
	 * @return void
	 */
	public function add_admin_menu_page() {
		\add_submenu_page(
			'progress-planner',
			\esc_html__( 'Settings', 'progress-planner' ),
			\esc_html__( 'Settings', 'progress-planner' ),
			'manage_options',
			'progress-planner-settings',
			[ $this, 'add_admin_page_content' ]
		);
	}

	/**
	 * Add content to the admin page of the free plugin.
	 *
	 * @return void
	 */
	public function add_admin_page_content() {
		require_once PROGRESS_PLANNER_DIR . '/views/admin-page-settings.php';
	}

	/**
	 * Get an array of tabs and their settings.
	 *
	 * @return array
	 */
	public function get_tabs_settings() {
		$page_types = \progress_planner()->get_page_types()->get_page_types();

		foreach ( $page_types as $page_type ) {
			$type_pages     = \progress_planner()->get_page_types()->get_posts_by_type( 'any', $page_type['slug'] );
			$has_page_value = \progress_planner()->get_settings()->get( "has-page-{$page_type['slug']}", 'no' );

			$tabs[ "page-{$page_type['slug']}" ] = [
				'title'    => sprintf(
					/* translators: The page name. */
					esc_html__( 'Your pages: "%s" page', 'progress-planner' ),
					esc_html( $page_type['title'] )
				),
				'desc'     => $page_type['description'] ?? '',
				'intro'    => esc_html__( 'Let\'s determine your needs together.', 'progress-planner' ),
				'settings' => [
					"has-page-{$page_type['slug']}" => [
						'id'          => "has-page-{$page_type['slug']}",
						'title'       => $page_type['title'],
						'description' => sprintf(
							/* translators: The page name. */
							esc_html__( 'Do your site have the "%s" page?', 'progress-planner' ),
							esc_html( $page_type['title'] )
						),
						'type'        => 'radio',
						'options'     => [
							'yes'            => esc_html__( 'Yes', 'progress-planner' ),
							'no'             => esc_html__( 'No', 'progress-planner' ),
							'not-applicable' => esc_html__( 'I don\'t need this page', 'progress-planner' ),
						],
						'value'       => $has_page_value,
						'page'        => $page_type['slug'],
					],
					$page_type['slug']              => [
						'id'          => $page_type['slug'],
						'title'       => $page_type['title'],
						'description' => $page_type['description'] ?? '',
						'type'        => 'page-select',
						'value'       => empty( $type_pages ) ? 0 : $type_pages[0]->ID,
						'page'        => $page_type['slug'],
					],
				],
			];
		}

		$tabs = apply_filters( 'progress_planner_settings_page_tabs', $tabs );

		return $tabs;
	}

	/**
	 * Store the settings form options.
	 *
	 * @return void
	 */
	public function store_settings_form_options() {
		// Check the nonce.
		\check_admin_referer( 'prpl-settings' );

		if ( isset( $_POST['pages'] ) ) {
			foreach ( wp_unslash( $_POST['pages'] ) as $type => $page_args ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				// Remove the post-meta from the existing posts.
				$existing_posts = \progress_planner()->get_page_types()->get_posts_by_type( 'any', $type );
				foreach ( $existing_posts as $post ) {
					if ( $post->ID === (int) $page_args['id'] ) {
						continue;
					}

					// Get the term-ID for the type.
					$term = \get_term_by( 'slug', $type, Page_Types::TAXONOMY_NAME );
					if ( ! $term instanceof \WP_Term ) {
						continue;
					}

					// Remove the assigned terms from the `progress_planner_page_types` taxonomy.
					\wp_remove_object_terms( $post->ID, $term->term_id, Page_Types::TAXONOMY_NAME );
				}

				// Skip if the ID is not set.
				if ( 1 > (int) $page_args['id'] ) {
					continue;
				}

				// Add the term to the `progress_planner_page_types` taxonomy.
				\progress_planner()->get_page_types()->set_page_type_by_id( (int) $page_args['id'], $type );

				/**
				 * TODO: Handle the $page_args['assign-user'] and $page_args['plan-date'] values.
				 */
			}
		}

		// WIP: Save the page selection.
		$settings   = \progress_planner()->get_settings();
		$page_types = \progress_planner()->get_page_types()->get_page_types();
		foreach ( $page_types as $page_type ) {
			if ( isset( $_POST[ "has-page-{$page_type['slug']}" ] ) ) {
				$value = \sanitize_text_field( \wp_unslash( $_POST[ "has-page-{$page_type['slug']}" ] ) );
				$settings->set( "has-page-{$page_type['slug']}", $value );
			}
		}

		do_action( 'progress_planner_settings_form_options_stored' );

		\wp_send_json_success( \esc_html__( 'Options stored successfully', 'progress-planner' ) );
	}
}
