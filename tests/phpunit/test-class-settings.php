<?php
/**
 * Class Settings_Test
 *
 * @package Progress_Planner\Tests
 */

namespace Progress_Planner\Tests;

/**
 * Settings test case.
 */
class Settings_Test extends \WP_UnitTestCase {

	/**
	 * Test get & set methods.
	 *
	 * @dataProvider data_get
	 *
	 * @param string|array $setting Setting to get.
	 * @param mixed        $value   Expected value.
	 */
	public function test_set_get( $setting, $value ) {
		\progress_planner()->get_settings()->set( $setting, $value );

		$saved = \get_option( \Progress_Planner\Settings::OPTION_NAME );
		if ( \is_string( $setting ) ) {
			$this->assertEquals( $value, $saved[ $setting ] );
		} else {
			$this->assertEquals( $value, \_wp_array_get( $saved, $setting ) );
		}

		$this->assertEquals( $value, \progress_planner()->get_settings()->get( $setting ) );
	}

	/**
	 * Data provider for test_get.
	 *
	 * @return array
	 */
	public function data_get() {
		return [
			[ 'setting', 'expected' ],
			[ [ 'setting' ], 'expected' ],
			[ [ 'setting', 'subsetting' ], 'expected' ],
			[ [ 'setting', 'subsetting', 'subsubsetting' ], 'expected' ],
		];
	}
}
