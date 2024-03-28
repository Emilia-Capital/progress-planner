<?php
/**
 * Class Settings_Test
 *
 * @package FewerTags
 */

namespace ProgressPlanner\Tests;

use ProgressPlanner\Settings;

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
		\ProgressPlanner\Settings::set( $setting, $value );

		$saved = \get_option( Settings::OPTION_NAME );
		if ( \is_string( $setting ) ) {
			$this->assertEquals( $value, $saved[ $setting ] );
		} else {
			$this->assertEquals( $value, \_wp_array_get( $saved, $setting ) );
		}

		$this->assertEquals( $value, \ProgressPlanner\Settings::get( $setting ) );
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
