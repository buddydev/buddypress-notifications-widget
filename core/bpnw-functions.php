<?php
/**
 * Core plugin functions file.
 *
 * @package buddypress-notifications-widget
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Load template
 *
 * @param string $template Tempate name.
 * @param false  $load If true it will load template.
 * @param array  $args Available args in template.
 *
 * @return string
 */
function bpnw_load_template( $template, $load = false, $args = array() ) {
	$locate = locate_template( 'bp-notifications-widget/' . $template, false, false, $args );

	if ( ! $locate ) {
		$locate = buddypress_notification_widget()->path . 'templates/' . $template;
	}

	if ( $load ) {
		include $locate;
	}

	return $locate;
}

/**
 * Render notification avatar
 *
 * @param BP_Notifications_Notification $notification notification object.
 */
function bpnw_notification_avatar( $notification ) {
	$component = $notification->component_name;

	switch ( $component ) {
		case 'groups':
			if ( ! empty( $notification->item_id ) ) {
				$item_id = $notification->item_id;
				$object  = 'group';
			}
			break;
		case 'follow':
		case 'friends':
			if ( ! empty( $notification->item_id ) ) {
				$item_id = $notification->item_id;
				$object  = 'user';
			}
			break;
		default:
			if ( ! empty( $notification->secondary_item_id ) ) {
				$item_id = $notification->secondary_item_id;
				$object  = 'user';
			} else {
				$item_id = $notification->item_id;
				$object  = 'user';
			}
			break;
	}

	if ( isset( $item_id, $object ) ) {

		if ( $object === 'group' ) {
			$group = new BP_Groups_Group( $item_id );
			$link  = bp_get_group_permalink( $group );
		} else {
			$user = new WP_User( $item_id );
			$link = bp_core_get_user_domain( $user->ID, $user->user_nicename, $user->user_login );
		}

		?>
		<a href="<?php echo $link ?>">
			<?php echo bp_core_fetch_avatar( [ 'item_id' => $item_id, 'object' => $object ] ); ?>
			<?php (isset($user) ? bb_user_status( $user->ID ) : ''); ?>
		</a>
		<?php
	}
}