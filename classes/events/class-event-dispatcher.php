<?php
/**
 * Event dispatcher class.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Events;

/**
 * Event dispatcher class.
 */
class Event_Dispatcher {

	/**
	 * The listeners.
	 *
	 * @var array<string, array<callable>>
	 */
	protected $listeners = [];

	/**
	 * Listen for an event.
	 *
	 * @param string   $event The event name.
	 * @param callable $listener The listener.
	 * @return void
	 */
	public function listen( $event, callable $listener ) {
		$this->listeners[ $event ][] = $listener;
	}

	/**
	 * Dispatch an event and notify all its listeners.
	 *
	 * @param object $event The event.
	 * @return void
	 */
	public function dispatch( $event ) {
		if ( ! empty( $this->listeners[ get_class( $event ) ] ) ) {
			foreach ( $this->listeners[ get_class( $event ) ] as $listener ) {
				call_user_func( $listener, $event );
			}
		}
	}
}
