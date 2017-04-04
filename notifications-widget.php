<?php
/**
 * Plugin Name: BuddyPress Notifications Widget
 * Author: Brajesh Singh
 * Version: 1.1.2
 * Plugin URI: http://buddydev.com/plugins/buddypress-notifications-widget/
 * Author URI: http://buddydev.com/members/sbrajesh/
 * Description: Allow site admins to show BuddyPress user notification in widget.
 * License: GPL
 */

/**
 * Helps loading the core files and translations
 */
class BuddyDev_BP_Notifications_Widget_Helper {

	/**
	 * Absolute path to this plugin directory
	 *
	 * @var string
	 */
	private $path;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->path = plugin_dir_path( __FILE__ );
		$this->setup();
	}

	/**
	 * Setup hooks.
	 */
	public function setup() {

		add_action( 'bp_loaded', array( $this, 'load' ) );
	}

	/**
	 * Load core files
	 */
	public function load() {

		$files = array(
			'core/bp-notification-widget-functions.php',
			'core/class-bp-notification-widget.php',
		);

		foreach ( $files as $file ) {
			require_once $this->path . $file;
		}
	}
}

new BuddyDev_BP_Notifications_Widget_Helper();


//localization
function bpdev_bpdnw_load_textdomain() {

	$locale = apply_filters( 'bpdnw_load_textdomain_get_locale', get_locale() );


	// if load .mo file
	if ( ! empty( $locale ) ) {
		$mofile_default = sprintf( '%slanguages/%s.mo', plugin_dir_path( __FILE__ ), $locale );

		$mofile = apply_filters( 'bpdnw_load_textdomain_mofile', $mofile_default );

		if ( file_exists( $mofile ) ) {
			// make sure file exists, and load it
			load_textdomain( 'bpdnw', $mofile );
		}
	}
}

add_action( 'bp_loaded', 'bpdev_bpdnw_load_textdomain', 2 );


//register widgets
function bpdev_notification_register_widget() {
	register_widget( 'BuddyDev_BPNotification_Widget' );

}

add_action( 'bp_widgets_init', 'bpdev_notification_register_widget' );

//load javascript
add_action( 'bp_enqueue_scripts', 'bpdev_notification_widget_load_js' );
function bpdev_notification_widget_load_js() {
	wp_enqueue_script( 'notification-clear-js', plugin_dir_url( __FILE__ ) . "notification.js", array( 'jquery' ) );
}

//ajaxed delete
add_action( 'wp_ajax_bpdev_notification_clear_notifications', 'bpdev_notification_clear_notifications' );
function bpdev_notification_clear_notifications() {
	//CHECK VALIDITY OF NONCE

	if ( ! is_user_logged_in() ) {
		return;
	}

	$user_id = bp_loggedin_user_id();

	check_ajax_referer( 'clear-all-notifications-for-' . $user_id );

	global $bp, $wpdb;

	$wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->core->table_name_notifications} WHERE user_id = %d ", $user_id ) );

	echo "1";
	exit( 0 );
}
    
