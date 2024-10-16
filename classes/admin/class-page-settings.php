<?php
/**
 * The admin pages and functionality for Progress Planner.
 *
 * @package Progress_Planner/Admin
 */

namespace Progress_Planner\Admin;

use Progress_Planner\Settings;
use Progress_Planner\API\Site_Types;

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
		$settings = new Settings();

		// WIP.
		$site_types = [];

		$tabs = [
			'intake' => [
				'title'    => esc_html__( 'Intake form', 'progress-planner' ),
				'desc'     => esc_html__( 'Let\'s get to know you and your site.', 'progress-planner' ),
				'intro'    => '<p>' . esc_html__( 'Tell us a bit about you & your site!', 'progress-planner' ) . '</p><p>' . esc_html__( 'This will allow us to give you good advice.', 'progress-planner' ) . '</p>',
				'settings' => [
					'site_age'        => [
						'title'   => esc_html__( 'For how long has your site been around?', 'progress-planner' ),
						'label'   => esc_html__( 'For how long has your site been around?', 'progress-planner' ),
						'id'      => 'site_age',
						'type'    => 'select',
						'options' => [
							'less-than-week'    => __( 'Less than a week', 'progress-planner' ),
							'less-than-month'   => __( 'Less than a month', 'progress-planner' ),
							'less-than-year'    => __( 'Less than a year', 'progress-planner' ),
							'more-than-year'    => __( 'More than a year', 'progress-planner' ),
							'more-than-2-years' => __( 'More than two years', 'progress-planner' ),
						],
						'value'   => $settings->get( [ 'settings', 'site_age' ], 'less-than-week' ),
					],
					'site_type'       => [
						'title'   => esc_html__( 'Your site', 'progress-planner' ),
						'label'   => esc_html__( 'What type of site is it?', 'progress-planner' ),
						'id'      => 'site_type',
						'type'    => 'select',
						'options' => $site_types,
						'value'   => $settings->get( [ 'settings', 'site_type' ], 'personal' ),
					],
					'time_allocation' => [
						'title'   => esc_html__( 'Your time', 'progress-planner' ),
						'label'   => esc_html__( 'How much time do you want to spend on your site every week?', 'progress-planner' ),
						'id'      => 'time_allocation',
						'type'    => 'radio',
						'options' => [
							/* translators: %s: number of minutes. */
							'15' => sprintf( esc_html__( '%s minutes', 'progress-planner' ), '<span class="number">15</span>' ),
							/* translators: %s: number of minutes. */
							'30' => sprintf( esc_html__( '%s minutes', 'progress-planner' ), '<span class="number">30</span>' ),
							/* translators: %s: number of minutes. */
							'60' => sprintf( esc_html__( '%s minutes', 'progress-planner' ), '<span class="number">60</span>' ),
							/* translators: %s: number of minutes. */
							'90' => sprintf( esc_html__( '%s minutes', 'progress-planner' ), '<span class="number">90</span>' ),
						],
						'value'   => $settings->get( [ 'settings', 'time_allocation' ], '60' ),
					],
				],
			],
		];

		$tabs['up2date'] = [
			'title'    => esc_html__( 'Keep your site up to date!', 'progress-planner' ),
			'desc'     => esc_html__( 'We\'ll tell you what to do.', 'progress-planner' ),
			'intro'    => '',
			'settings' => [],
		];

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

		$settings_to_save = [];
		$settings         = new Settings();

		// Store the options.
		foreach ( [ 'site_age', 'site_type', 'time_allocation' ] as $option ) {
			if ( ! isset( $_POST[ $option ] ) ) {
				continue;
			}
			$settings_to_save[ $option ] = \sanitize_text_field( \wp_unslash( $_POST[ $option ] ) );
		}

		if ( ! empty( $settings_to_save ) ) {
			$settings->set( 'settings', $settings_to_save );
		}

		\wp_send_json_success( \esc_html__( 'Options stored successfully', 'progress-planner' ) );
	}
}
