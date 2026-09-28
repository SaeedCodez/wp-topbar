<?php
/**
 * Uninstall cleanup.
 *
 * @package WP_Topbar
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'wptb_options' );

if ( is_multisite() ) {
	global $wpdb;

	$blog_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

	foreach ( $blog_ids as $blog_id ) {
		switch_to_blog( $blog_id );
		delete_option( 'wptb_options' );
		restore_current_blog();
	}
}
