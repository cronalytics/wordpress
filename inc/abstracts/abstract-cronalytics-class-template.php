<?php
/**
 * Abstract class used to provide common functionality to all extending plugin classes.
 *
 * @package Cronalytics\Cronalytics_Class_Template
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Abstract class used to provide common functionality to all extending plugin classes.
 *
 * Also provides access to the main class variables.
 */
abstract class Cronalytics_Class_Template {

	/**
	 * Local instance of main plugin class.
	 *
	 * Provides easy access to main plugin variables and methods.
	 *
	 * @var  object  Global instance of main plugin class.
	 */
	protected $main;

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->main = cronalytics();
	}
}
