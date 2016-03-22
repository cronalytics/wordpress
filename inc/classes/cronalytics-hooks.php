<?php
/**
 * Example class.
 *
 * @package Cronalytics\Cronalytics_Hooks
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Example class.
 */
final class Cronalytics_Hooks extends Cronalytics_Class_Template {

    private $results = array();

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Required to set up the parent abstract class variables.
		parent::__construct();

		// Actions / filters to be added here if required.
	}

	private function add_result($hook, $result) {
        $this->results[$hook] = $result;
    }

    /**
	 * Get a list of all crons that we want to monitor
	 *
	 * @return mixed
	 */
	private function get_crons_to_monitor() {
		$crons = get_option($this->main->cron_store_option);
		return unserialize($crons);
	}

	public function register_cron_hooks() {
		$crons = $this->get_crons_to_monitor();

		foreach( $crons as $name => $data ) {
			add_action( $name, array( $this, 'cron_started'), 1);
			add_action( $name, array( $this, 'cron_ended'), PHP_INT_MAX);
		}

	}

	private function get_current_cron_data() {
		$current = current_filter();
		foreach( $this->get_crons_to_monitor() as $name => $data ) {
			if ( $name == $current ) {
				return $data;
			}
		}
	}
	public function cron_started() {
		$cron = $this->get_current_cron_data();

		$now = new DateTime();
		$result = $this->main->api->startTrigger( $cron['private_hash'], $now );

        $this->add_result( current_filter(), $result );
	}

	public function cron_ended() {

		$cron = $this->get_current_cron_data();

        $now  = new DateTime();
        $trigger_hash = $this->results[ current_filter() ]['data']['trigger']['_id'];
		$this->main->api->endTrigger( $trigger_hash, $now );
    }
}

return new Cronalytics_Hooks();
