<?php
/**
 * BuddyPress notifications template
 *
 * @package buddypress-notifications-widget
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$show_count              = isset( $args['show_count'] ) ? absint( $args['show_count'] ) : 1;
$show_list               = isset( $args['show_list'] ) ? absint( $args['show_list'] ) : 1;
$show_clear_notification = isset( $args['show_clear_notification'] ) ? absint( $args['show_clear_notification'] ) : 1;
$count                   = isset( $args['count'] ) ? absint( $args['count'] ) : 1;
$notifications           = isset( $args['notifications'] ) ? $args['notifications'] : array();
$notification_link       = isset( $args['notification_link'] ) ? esc_url( $args['notification_link'] ) : '';
?>
<div class='bpnw-notification-list bp-notification-widget-notifications-list'>

	<?php if ( ! empty( $show_count ) && ( $count > 0 || empty( $show_list ) ) ) : ?>
		<a href="<?php echo esc_url( $notification_link ); ?>"><?php echo sprintf( __( 'You have %d new notifications', 'buddypress-notifications-widget' ), $count ); ?></a>
	<?php endif; ?>

	<?php if ( $show_list ) : ?>
		<ul class="bp-notification-list">

			<?php if ( $notifications ) : ?>
				<?php $counter = 0; for ( $i = 0; $i < $count; $i ++ ) : ?>

					<?php
					$notification_item = is_array( $notifications[ $i ] ) ? sprintf( '<a href="%s">%s</a>', $notifications[ $i ]['link'], $notifications[ $i ]['text'] ) : $notifications[ $i ];
					$alt               = ( 0 == $counter % 2 ) ? ' class="alt"' : '';
					$counter ++;
					?>

					<li <?php echo $alt ?>><?php echo $notification_item ?></li>
				<?php endfor; ?>
			<?php else : ?>
				<li><?php _e( "You don't have any new notification.", 'buddypress-notifications-widget' ); ?></li>
			<?php endif; ?>
		</ul>
	<?php endif; ?>

	<?php if ( $count > 0 && $show_clear_notification ) : ?>
		<a data-clear-text="<?php _e( 'clearing...', 'buddypress-notifications-widget' ); ?>" class="bp-notifications-widget-clear-link" href="<?php echo esc_url( bp_loggedin_user_domain() . '?clear-all=true' . '&_wpnonce=' . wp_create_nonce( 'bp-notifications-widget-clear-all-' . bp_loggedin_user_id() ) ); ?>"><?php _e( '[x] Clear All Notifications', 'buddypress-notifications-widget' ) ?></a>
	<?php endif; ?>
</div>