<?php
/**
 * Shortcode helper class for plugin.
 *
 * @package buddypress-notification-widget
 */

// Exit if access directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class BP_Notification_Shortcode_Helper
 */
class BP_Notification_Shortcode_Helper {

	/**
	 * Boot class
	 */
	public static function boot() {
		$self = new self();
		$self->setup();
	}

	/**
	 * Add shortcode
	 */
	private function setup() {
		add_shortcode( 'buddydev_bp_notification', array( $this, 'render' ) );
	}

	/**
	 * Render shortcode data
	 */
	public function render( $atts ) {
		$atts = shortcode_atts( array(
			'title'                   => __( 'Notifications', 'buddypress-notification-widget' ),
			'link_title'              => 1,
			'show_count'              => 1,
			'show_count_in_title'     => 1,
			'show_list'               => 1,
			'show_clear_notification' => 1,
			'show_empty'              => 1,
		), $atts, 'buddydev_bp_notification' );

		if ( ! is_user_logged_in() ) {
			return;
		}

		$user_id = get_current_user_id();

		// let us get the notifications for the user.
		if ( function_exists( 'bp_notifications_get_notifications_for_user' ) ) {
			$notifications = bp_notifications_get_notifications_for_user( $user_id, 'string' );
		} else {
			$notifications = bp_core_get_notifications_for_user( $user_id, 'string' );
		}

		// will be set to false if there are no notifications.
		$count = empty( $notifications ) ? 0 : count( $notifications );

		$notification_link = bp_loggedin_user_domain() . bp_get_notifications_slug();
		$title_link        = sprintf( '<a href="%s">%s</a>', $notification_link, $atts['title'] );

		ob_start();

		$title = ( ! empty( $atts['link_title'] ) ) ? $title_link : $atts['title'];

		echo $title;

		if ( $atts['show_count_in_title'] ) {
			printf( "<span class='notification-count-in-title'>(%d)</span>", $count );
		}

		$template = 'notifications.php';
		if ( buddypress_notification_widget()->active_buddyboss ) {
			$template = 'bb-notifications.php';
		}

		bpnw_load_template(
			$template,
			true,
			array_merge(
				$atts,
				array(
					'count'             => $count,
					'notification_link' => $notification_link,
					'notifications'     => $notifications,
				)
			)
		);

		return ob_get_clean();
	}
}