<?php
/**
 * The Cronalytics plugin.
 *
 * TODO: Long description of the plugin here.
 *
 * @package Cronalytics
 */

/**
 * Plugin Name: Cronalytics
 * Plugin URI:  http://cronalytics.io/wordpress
 * Description: create and manage crons via cronalytics.io.
 * Author:      Kieran Yeates
 * Author URI:  http://cronalytics.io
 * Version:     1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Main Cronalytics class.
 */
final class Cronalytics {

	/** Magic *****************************************************************/

	/**
	 * Cronalytics uses many variables, several of which can be filtered to
	 * customize the way it operates. Most of these variables are stored in a
	 * private array that gets updated with the help of PHP magic methods.
	 *
	 * This is a precautionary measure, to avoid potential errors produced by
	 * unanticipated direct manipulation of Cronalytics's run-time data.
	 *
	 * @see Cronalytics::setup_globals()
	 * @var array
	 */
	private $data;

	/** Singleton *************************************************************/

	/**
	 * Main Cronalytics Instance
	 *
	 * Insures that only one instance of Cronalytics exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @staticvar object $instance
	 * @return The one true Cronalytics
	 */
	public static function instance() {

		// Store the instance locally to avoid private static replication.
		static $instance = null;

		// Only run these methods if they haven't been ran previously.
		if ( null === $instance ) {
            $instance = new Cronalytics();
			$instance->setup_globals();
			$instance->includes();
			$instance->setup_actions();

//            $instance->add_test_cron();
		}

        // Always return the instance.
		return $instance;
	}

	/** Magic Methods *********************************************************/

	/**
	 * A dummy constructor to prevent Cronalytics from being loaded more than once.
	 */
	private function __construct() { /* Do nothing here */ }

	/**
	 * A dummy magic method to prevent Cronalytics from being cloned
	 */
	public function __clone() { _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'cronalytics' ), '1.0.0' ); }

	/**
	 * A dummy magic method to prevent Cronalytics from being unserialized
	 */
	public function __wakeup() { _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'cronalytics' ), '1.0.0' ); }

	/**
	 * Magic method for checking the existence of a certain custom field
	 */
	public function __isset( $key ) { return isset( $this->data[$key] ); }

	/**
	 * Magic method for getting Cronalytics variables
	 */
	public function __get( $key ) { return isset( $this->data[$key] ) ? $this->data[$key] : null; }

	/**
	 * Magic method for setting Cronalytics variables
	 */
	public function __set( $key, $value ) { $this->data[$key] = $value; }

	/**
	 * Magic method for unsetting Cronalytics variables
	 */
	public function __unset( $key ) { if ( isset( $this->data[$key] ) ) unset( $this->data[$key] ); }

	/**
	 * Magic method to prevent notices and errors from invalid method calls
	 */
	public function __call( $name = '', $args = array() ) { unset( $name, $args ); return null; }

	/** Private Methods *******************************************************/

    public function test_schedule_hook($args) {
        echo "Test schedule hook!";
        return true;
    }

	/**
	 * Set some smart defaults to class variables.
	 */
	private function setup_globals() {

		$this->version    = '1.0.0';

        $this->cron_store_option = 'cronalytics_crons';

		// Setup some base path and URL information.
		$this->file       = __FILE__;
		$this->basename   = plugin_basename( $this->file );
		$this->plugin_dir = plugin_dir_path( $this->file );
		$this->plugin_url = plugin_dir_url( $this->file );

		// Includes.
		$this->inc_dir    = $this->plugin_dir . 'inc/';

		// Abstracts.
		$this->abstracts  = $this->inc_dir    . 'abstracts/';

        // Vendor files
        $this->vendor     = $this->inc_dir    . 'vendor/';

		// Classes.
		$this->classes    = $this->inc_dir    . 'classes/';

		// Assets.
		$this->assets_dir = $this->plugin_dir . 'assets/';
		$this->assets_url = $this->plugin_url . 'assets/';

		// CSS folder.
		$this->css_dir    = $this->assets_dir  . 'css/';
		$this->css_url    = $this->assets_url  . 'css/';

		// Images folder.
		$this->image_dir  = $this->assets_dir  . 'img/';
		$this->image_url  = $this->assets_url  . 'img/';

		// JS folder.
		$this->js_dir     = $this->assets_dir  . 'js/';
		$this->js_url     = $this->assets_url  . 'js/';

        // HTML Templates
        $this->template_dir = $this->inc_dir   . 'templates/';
	}

	/**
	 * Include required files.
	 */
	private function includes() {

		// Require the abstract class used to provide common functionality to all extending plugin classes.
		require $this->abstracts . 'abstract-cronalytics-class-template.php';

        //vendor files
        require $this->vendor .  'tcdent/php-restclient/restclient.php';

        require $this->classes . 'cronalytics-api.php';

		// Require the additional classes directly into a main class variable.
		$this->class_hooks = require $this->classes . 'cronalytics-hooks.php';

        $cronalyticsOptions = array('x' => true, 'vvv' => false);
//        if (DEBUG) { $cronalyticsOptions['x'] == true; }
        $this->api = new CronalyticsAPI($cronalyticsOptions);
		// Admin specific includes.
		if ( is_admin() ) {
		}
	}

    /**
     * enqueue any style or js needed
     */
    public function enqueue() {
        if ( is_admin() ) {
            wp_enqueue_style( 'bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css' );
            wp_enqueue_script( 'bootstrapjs', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js' );
        }
    }
	/**
	 * Setup the default hooks and actions.
	 */
	private function setup_actions() {

		// Attach functions to the activate / deactive hooks.
		add_action( 'activate_'   . $this->basename, array( $this, 'activate' ) );
		add_action( 'deactivate_' . $this->basename, array( $this, 'deactivate' ) );

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );

        add_action( 'init',  array( $this, 'handle_post' ) );
        add_action( 'admin_menu',  array( $this, 'add_management_page' ) );

        $this->class_hooks->register_cron_hooks();


        add_filter( 'cron_schedules', array($this, 'add_test_schedule') );
//        wp_schedule_event(time(), 'secondly', 'my_schedule_hook', array(1,2,3,4,5));

        // FOR TESTING
        add_action( 'my_schedule_hook', array($this, 'test_schedule_hook'));
	}

    public function add_management_page() {
        add_management_page(
            esc_html__( 'Cronalytics - Cron management', 'wp-cronalytics' ),
            esc_html__( 'Crons', 'wp-cronalytics' ),
            'manage_options',
            'cronalytics_admin_manage_page',
            array( $this, 'admin_manage_page' )
        );
    }

	/** Public Methods *******************************************************/

	/**
	 * Plugin activation.
	 */
	public function activate() {
        add_option( $this->cron_store_option, serialize( array() ) );

	}

    public function add_test_schedule( $schedules ) {
        $schedules['secondly'] = array(
            'interval' => 1,
            'display' => __('Once A Second')
        );
        return $schedules;
    }
    /**
	 * Plugin deactivation.
	 */
	public function deactivate() {

	}

    /**
     * Create a management page that lists crons and allows you to monitor via cronalytics
     */
    public function admin_manage_page() {
        $crons = $this->get_crons();
        $action_response = $this->get_flash();
        include $this->template_dir . 'manage_page.tpl.php';
    }

    /**
     * @return array
     */
    private function get_crons() {
        $cronalytics = get_option( $this->cron_store_option );
        $cronalytics = unserialize( $cronalytics );

        $wp_crons = $this->get_wp_crons();

        for( $i = 0; $i < sizeof( $wp_crons ); $i++ ) {
            $cron = $wp_crons[ $i ];

            foreach( $cronalytics as $hook => $cron_data ) {
                if ( $cron[ 'hook' ] == $hook ) {
                    $cron[ 'cronalytics' ] = $cron_data;
                }
            }

            $wp_crons[ $i ] = $cron;
        }
        return $wp_crons;

    }

    /**
     * Get a list of crons and add in any cronalytics info we have
     * @return array
     */
    private function get_wp_crons() {

        $wp_crons = _get_cron_array();

        $all_crons = array();
        foreach( $wp_crons as $timestamp => $cron_list ) {
            foreach( $cron_list as $hook => $cron ) {
                $id = array_keys($cron)[0];
                $data = $cron[$id];

                if ( ! $data['schedule'] ) {
                    continue;
                }

                $add_cron = array(
                    'hook' => $hook,
                    'id' => $id,
                    'schedule' => $data['schedule'],
                    'args' => $data['args'],
                    'interval' => $data['interval'],
                    'next_activation' => $timestamp,
                );


                $all_crons[] = $add_cron;
            }
        }

        return $all_crons;
    }

    /**
     * @param $wp_cron
     * @return mixed|WP_Error
     */
    public function add_cron_to_cronalytics( $wp_cron ) {

        $email = 'admin@wordpress.com';
        $added_by = 'cronalytics wordpress plugin';
        $interval = $wp_cron['interval'];
        $interval_start = $wp_cron['next_activation'];

        $response = $this->api->addInterval($wp_cron['hook'], $email, $added_by, $interval, $interval_start, 'moving');

        $result = json_decode($response, true);
        if ( $result['success'] == false ) {
            return new WP_Error( 'cronalytics_fail', 'Cron failed to add: ' . $result['error'] );
        }

        $cron = $result['data']['cron'];

        $cronalytics = get_option( $this->cron_store_option );
        $cronalytics = unserialize( $cronalytics );
        $cronalytics[$wp_cron['hook']] = array(
            'public_hash' => $cron['public_hash'],
            'private_hash' => $cron['private_hash']
        );
        $update_result = update_option( $this->cron_store_option, serialize( $cronalytics ) );
        if ( ! $update_result ) {
            return new WP_Error('fail', 'Failed to save Cronalytics information to wordpress database: ' . print_r(json_encode( $cronalytics ), true));
        }

        return $result;
    }

    public function remove_cron_from_cronalytics( $hook, $delete_from_cronalytics = true ) {

//TELL CRONALYTICS WE ARE DELETING!!!! maybe
//        $response = $this->api->addInterval($wp_cron['hook'], $email, $added_by, $interval, $interval_start, 'moving');

//        $result = json_decode($response, true);
//        if ( $result['success'] == false ) {
//            return new WP_Error( 'cronalytics_fail', 'Cron failed to add: ' . $result['error'] );
//        }

//        $cron = $result['data']['cron'];

        $cronalytics = get_option( $this->cron_store_option );
        $cronalytics = unserialize( $cronalytics );
        unset($cronalytics[$hook]);

        $update_result = update_option( $this->cron_store_option, serialize( $cronalytics ) );
        if ( ! $update_result ) {
            return new WP_Error('fail', 'Failed to save Cronalytics information to wordpress database: ' . print_r(json_encode( $cronalytics ), true));
        }

        return true;
    }

    private function update_flash( $message = null, $type = null, $action = null ) {
        if ( ! is_array( $this->action_response ) ) {
            $this->action_response = array(
                'action' => 'New action!!',
                'type' => null,
                'message' => 'New Message!',
            );
        }

        $action_response = $this->action_response;

        if ( ! empty( $message ) ) { $action_response['message'] = $message; }
        if ( ! empty( $type ) ) { $action_response['type'] = $type; }
        if ( ! empty( $action ) ) { $action_response['action'] = $action; }

        $this->action_response = $action_response;
    }

    private function get_flash() {
        if ( ! is_array( $this->action_response ) ) {
            $this->action_response = array(
                'action' => '',
                'type' => null,
                'message' => '',
            );
        }

        return $this->action_response;
    }

    /**
     *
     */
    public function handle_post() {
        if (empty($_REQUEST['page']) && $_REQUEST['page'] !== 'cronalytics_admin_manage_page') {
           return;
        }


        switch ( $_REQUEST['action'] ) {
            case  'ca_add':
                $hook = $_REQUEST['hook'];
                $all_crons = $this->get_crons();

                $found_in_cronalytics = false;
                $cron = array();
                foreach($all_crons as $check_cron) {
                    if ($check_cron['hook'] == $hook ) {
                        $cron = $check_cron;
                        if (isset($cron['cronalytics'])) {
                            $found_in_cronalytics = true;
                        }
                        continue;
                    }
                }

                if ($found_in_cronalytics) {
                    $this->update_flash( "'{$hook}' already added to cronalytics.io", 'error' );
                    return;
                }

                $result = $this->add_cron_to_cronalytics($cron);

                if ( is_wp_error( $result ) ) {
                    $this->update_flash( $result->get_error_message(), 'error' );
                    return;
                }

                $this->update_flash( "'{$hook}' added to cronalytics.io", 'success' );
                break;
            case 'ca_remove':
                $hook = $_REQUEST['hook'];

                $result = $this->remove_cron_from_cronalytics( $hook );

                $this->update_flash($result);
                break;
        }

    }

}

/**
 * The main function responsible for returning the one true Cronalytics Instance
 * to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $cronalytics = cronalytics(); ?>
 *
 * @return The one true Cronalytics Instance
 */
function cronalytics() {
	return Cronalytics::instance();
}

// Spin up an instance.
$GLOBALS['cronalytics'] = cronalytics();
