<?php
/**
 * Class Date_Test
 *
 * @package FewerTags
 */

namespace Progress_Planner\Tests;

use Progress_Planner\Date;

/**
 * Date test case.
 */
class Date_Test extends \WP_UnitTestCase {

	/**
	 * Test get_range.
	 */
	public function test_get_range() {
		$start_date = new \DateTime( '2020-01-01' );
		$end_date   = new \DateTime( '2020-01-31' );
		$range      = Date::get_range( $start_date, $end_date );
		$this->assertEquals( '2020-01-01', $range['start']->format( 'Y-m-d' ) );
		$this->assertEquals( '2020-01-30', $range['end']->format( 'Y-m-d' ) ); // Excludes end date.
	}

	/**
	 * Test get_periods.
	 *
	 * @dataProvider data_get_periods
	 *
	 * @param \DateTime $start     The start date.
	 * @param \DateTime $end       The end date.
	 * @param string    $frequency The frequency.
	 */
	public function test_get_periods( $start, $end, $frequency ) {
		$periods = Date::get_periods( $start, $end, $frequency );
		$this->assertEquals( $start, $periods[0]['start'] );
		$this->assertEquals( $end, end( $periods )['end']->modify( '+1 day' ) );
	}

	/**
	 * Data provider for test_get_periods.
	 */
	public function data_get_periods() {
		return [
			[ new \DateTime( '2020-01-01' ), new \DateTime( '2020-12-31' ), 'daily' ],
			[ new \DateTime( '2020-01-01' ), new \DateTime( '2020-12-31' ), 'weekly' ],
			[ new \DateTime( '2020-01-01' ), new \DateTime( '2020-12-31' ), 'monthly' ],
			[ new \DateTime( '1987-01-04' ), new \DateTime( '2147-01-27' ), 'daily' ],
			[ new \DateTime( '1987-05-16' ), new \DateTime( '2147-05-12' ), 'weekly' ],
			[ new \DateTime( '1987-02-06' ), new \DateTime( '2147-03-08' ), 'monthly' ],
		];
	}

	/**
	 * Test get_days_between_dates.
	 *
	 * @dataProvider data_get_days_between_dates
	 *
	 * @param \DateTime $start    The start date.
	 * @param \DateTime $end      The end date.
	 * @param int       $expected The expected number of days.
	 */
	public function test_get_days_between_dates( $start, $end, $expected ) {
		$this->assertEquals(
			$expected,
			Date::get_days_between_dates( $start, $end )
		);
	}

	/**
	 * Data provider for test_get_days_between_dates.
	 */
	public function data_get_days_between_dates() {
		return [
			[ new \DateTime( '2020-01-01' ), new \DateTime( '2020-01-31' ), 30 ],
			[ new \DateTime( '2020-01-01' ), new \DateTime( '2020-01-01' ), 0 ],
			[ new \DateTime( '2020-01-01' ), new \DateTime( '2020-01-02' ), 1 ],
			[ new \DateTime( '2020-01-01' ), new \DateTime( '2020-01-03' ), 2 ],
			[ new \DateTime( '2020-01-04' ), new \DateTime( '2020-01-01' ), -3 ],
		];
	}
}
