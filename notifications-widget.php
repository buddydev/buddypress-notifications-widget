<?php
/**
 * Plugin Name: BuddyPress Notifications Widget
 * Author: BuddyDev
 * Version: 1.3.2
 * Plugin URI: https://buddydev.com/plugins/buddypress-notifications-widget/
 * Author URI: https://buddydev.com/
 * Description: Allow site admins to show BuddyPress user notification in widget.
 * License: GPL
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

/**
 * BuddyDev_BP_Notifications_Widget_Helper
 */
class BuddyDev_BP_Notifications_Widget_Helper {

	/**
	 * Absolute path to this plugin directory
	 *
	 * @var string
	 */
	private $path;

	/**
	 * Plugin directory url.
	 *
	 * @var string
	 */
	private $url;

	/**
	 * BuddyDev_BP_Notifications_Widget_Helper Constructor
	 */
	public function __construct() {
		$this->path = plugin_dir_path( __FILE__ );
		$this->url  = plugin_dir_url( __FILE__ );

		add_action( 'bp_loaded', array( $this, 'setup' ) );
	}

	/**
	 * Setup hooks.
	 */
	public function setup() {

		// Only if the notifications component is enabled, we will load and do other stuff.
		if ( ! bp_is_active( 'notifications' ) ) {
			return ;
		}

		$this->load();

		add_action( 'bp_init', array( $this, 'load_textdomain' ) );
		add_action( 'bp_widgets_init', array( $this, 'register_widget' ) );
		add_action( 'bp_enqueue_scripts', array( $this, 'load_js' ) );
		add_action( 'wp_ajax_bpdev_notification_clear_notifications', array( $this, 'clear_notifications' ) );
	}

	/**
	 * Load file
	 */
	public function load() {
		require_once $this->path . 'core/class-bp-notification-widget.php';
		require_once $this->path . 'core/class-bp-notification-shortcode-helper.php';

		BP_Notification_Shortcode_Helper::boot();
	}

	/**
	 * Load translation.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'buddypress-notifications-widget', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Register the widget.
	 */
	public function register_widget() {
		register_widget( 'BuddyDev_BPNotification_Widget' );
	}

	/**
	 * Ajax clear all notifications for the current user.
	 */
	public function clear_notifications() {

		if ( ! is_user_logged_in() ) {
			return;
		}

		$user_id = bp_loggedin_user_id();

		check_ajax_referer( 'bp-notifications-widget-clear-all-' . $user_id );

		global $wpdb;

		$bp = buddypress();

		$wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->core->table_name_notifications} WHERE user_id = %d ", $user_id ) );

		wp_send_json_success( __( 'Cleared', 'buddypress-notifications-widget' ) );
	}

	/**
	 * Load js
	 */
	public function load_js() {
		wp_enqueue_script( 'bp-notification-widget-clear-js', $this->url . 'notification.js', array( 'jquery' ) );
	}
}

new BuddyDev_BP_Notifications_Widget_Helper();
