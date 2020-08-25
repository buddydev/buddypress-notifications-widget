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