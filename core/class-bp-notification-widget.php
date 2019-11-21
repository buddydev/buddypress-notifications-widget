<?php
/**
 * Notification widget class
 */

// Do not allow direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

/**
 * BuddyPress Notifications widget class
 */
class BuddyDev_BPNotification_Widget extends WP_Widget {

	/**
	 * BuddyDev_BPNotification_Widget Constructor
	 */
	public function __construct() {
		$name = __( '(BuddyDev) BuddPress Notifications', 'buddypress-notifications-widget' );
		parent::__construct( false, $name );
	}

	/**
	 * Display widget output.
	 *
	 * @param array $args widget args.
	 * @param array $instance current instance settings.
	 */
	public function widget( $args, $instance ) {

		// do not show anything if user is not logged in.
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
		if ( empty( $notifications ) ) {
			$count = 0;
		} else {
			$count = count( $notifications );
		}

		// do not show this widget.
		if ( $count <= 0 && empty( $instance['show_empty'] ) ) {
			return;
		}

		echo $args['before_widget'];
		echo $args['before_title'];

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		$notification_link = trailingslashit( bp_loggedin_user_domain() . bp_get_notifications_slug() );
		$title_link        = sprintf( '<a href="%s">%s</a>', $notification_link, $instance['title'] );

		$widget_title = ( ! empty( $instance['link_title'] ) ) ? $title_link : $title;

		echo $widget_title;

		if ( $instance['show_count_in_title'] ) {
			printf( "<span class='notification-count-in-title'>(%d)</span>", $count );
		}

		echo $args['after_title'];

		echo "<div class='bpnw-notification-list bp-notification-widget-notifications-list'>";

		if ( ! empty( $instance['show_count'] ) && ( $count > 0 || empty( $instance['show_list'] ) ) ) {
			printf( '<a href="%s">%s</a>', $notification_link, sprintf( __( 'You have %d new notifications', 'buddypress-notifications-widget' ), $count ) );
		}

		if ( $instance['show_list'] ) {
			self::print_list( $notifications, $count );
		}

		if ( $count > 0 && $instance['show_clear_notification'] ) {
			$clear_text = __( 'clearing...', 'buddypress-notifications-widget' );
			echo '<br /><a data-clear-text="' . $clear_text . '" class="bp-notifications-widget-clear-link" href="' . bp_loggedin_user_domain() . '?clear-all=true' . '&_wpnonce=' . wp_create_nonce( 'bp-notifications-widget-clear-all-' . bp_loggedin_user_id() ) . '">' . __( '[x] Clear All Notifications', 'buddypress-notifications-widget' ) . '</a>';
		}

		echo '</div>';

		echo $args['after_widget'];
	}

	/**
	 * Update widget instance settings.
	 *
	 * @param array $new_instance new settings.
	 * @param array $old_instance ols settings.
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                            = $old_instance;
		$instance['title']                   = strip_tags( $new_instance['title'] );
		$instance['link_title']              = intval( $new_instance['link_title'] );
		$instance['show_count_in_title']     = intval( $new_instance['show_count_in_title'] );
		$instance['show_count']              = intval( $new_instance['show_count'] );
		$instance['show_list']               = intval( $new_instance['show_list'] );
		$instance['show_clear_notification'] = intval( $new_instance['show_clear_notification'] );
		$instance['show_empty']              = intval( $new_instance['show_empty'] );

		return $instance;
	}

	/**
	 * Display widget form.
	 *
	 * @param array $instance cuerrent instance.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args(
			$instance,
			array(
				'title'                   => __( 'Notifications', 'buddypress-notifications-widget' ),
				'link_title'              => 1,
				'show_count'              => 1,
				'show_count_in_title'     => 0,
				'show_list'               => 1,
				'show_clear_notification' => 1,
				'show_empty'              => 0,
			)
		);

		$title                   = strip_tags( $instance['title'] );
		$link_title              = absint( $instance['link_title'] ); // Link title to the user notifications tab.
		$show_count_in_title     = absint( $instance['show_count_in_title'] ); // show notification count.
		$show_count              = absint( $instance['show_count'] ); // show notification count.
		$show_list               = absint( $instance['show_list'] ); // show notification list.
		$show_clear_notification = absint( $instance['show_clear_notification'] );
		$show_empty              = absint( $instance['show_empty'] );
		?>
		<p>
			<label for="bp-notification-title">
				<strong><?php _e( 'Title:', 'buddypress-notifications-widget' ); ?> </strong>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
					   name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
					   value="<?php echo esc_attr( $title ); ?>"/>
			</label>
		</p>

		<p>
			<label for="bp-notification-link-title">
				<?php _e( 'Link widget title to notification tab', 'buddypress-notifications-widget' ); ?>
				<input class="widefat" id="<?php echo $this->get_field_id( 'link_title' ); ?>"
					   name="<?php echo $this->get_field_name( 'link_title' ); ?>" type="checkbox"
					   value="1" <?php checked( 1, $link_title ); ?> />
			</label>
		</p>

		<p>
			<label for="bp-show-notification-count-in-title">
				<?php _e( 'Show Notification count in title', 'buddypress-notifications-widget' ); ?>
				<input class="widefat" id="<?php echo $this->get_field_id( 'show_count_in_title' ); ?>"
					   name="<?php echo $this->get_field_name( 'show_count_in_title' ); ?>" type="checkbox"
					   value="1" <?php checked( 1, $show_count_in_title ); ?> />
			</label>
		</p>

		<p>
			<label for="bp-show-notification-count"><?php _e( 'Show Notification count text', 'buddypress-notifications-widget' ); ?>
				<input class="widefat" id="<?php echo $this->get_field_id( 'show_count' ); ?>"
					   name="<?php echo $this->get_field_name( 'show_count' ); ?>" type="checkbox" value="1"
					<?php checked( 1, $show_count ); ?> />
			</label>
		</p>
		<p>
			<label for="bp-show-notification-list"><?php _e( 'Show the list of Notifications', 'buddypress-notifications-widget' ); ?>
				<input class="widefat" id="<?php echo $this->get_field_id( 'show_list' ); ?>"
					   name="<?php echo $this->get_field_name( 'show_list' ); ?>" type="checkbox"
					   value="1" <?php checked( 1, $show_list ); ?> />
			</label>
		</p>
		<p>
			<label for="bp-show-empty-widget"><?php _e( 'Show widget even when there are no notifications?', 'buddypress-notifications-widget' ); ?>
				<input class="widefat" id="<?php echo $this->get_field_id( 'show_empty' ); ?>"
					   name="<?php echo $this->get_field_name( 'show_empty' ); ?>" type="checkbox"
					   value="1" <?php checked( $show_empty, 1 ); ?> />
			</label>
		</p>
		<p>
			<label for="bp-show_clear_notification"><?php _e( 'Show the Clear Notifications button', 'buddypress-notifications-widget' ); ?>
				<input class="widefat" id="<?php echo $this->get_field_id( 'show_clear_notification' ); ?>"
					   name="<?php echo $this->get_field_name( 'show_clear_notification' ); ?>" type="checkbox"
					   value="1" <?php checked( 1, $show_clear_notification ); ?> />
			</label>
		</p>
		<?php
	}

	/**
	 * Print notifications list
	 *
	 * @param $notifications
	 * @param $count
	 */
	public function print_list( $notifications, $count ) {

		echo '<ul class="bp-notification-list">';

		if ( $notifications ) {

			$counter = 0;

			for ( $i = 0; $i < $count; $i ++ ) {

				$notification_item = '';
				if ( is_array( $notifications[ $i ] ) ) {
					$notification_item = sprintf( '<a href="%s">%s</a>', $notifications[ $i ]['link'], $notifications[ $i ]['text'] );
				} else {
					$notification_item = $notifications[ $i ];
				}

				$alt = ( 0 == $counter % 2 ) ? ' class="alt"' : '';

				?>

				<li <?php echo $alt ?>><?php echo $notification_item ?></li>

				<?php $counter ++;

			}
		} else { ?>

			<li><?php _e( "You don't have any new notification.", 'buddypress-notifications-widget' ); ?></li>

			<?php
		}

		echo '</ul>';
	}
}
