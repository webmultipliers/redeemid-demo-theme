<?php
/**
 * Moves a site seeded by the theme under its old name (Shopify for WP Test,
 * prefix sfwp) to the RedeemID Demo names, once, before anything is seeded.
 *
 * Without it the theme would not recognise its own pages, posts, test
 * accounts and demo types, and would seed a second copy of each. The
 * plugin's own data and block names are moved by the plugin (RedeemID\Legacy).
 *
 * - Options sfwp_test_* and meta _sfwp_test_* get the redeemid_demo_ prefix.
 * - Demo post types and taxonomies sfwp_* become ledger_*.
 * - Test accounts sfwp-* become ledger-*, with their example.com addresses.
 * - Seeded content gets the new theme block, class and marker names.
 *
 * @package redeemid-demo
 */

defined( 'ABSPATH' ) || exit;

const REDEEMID_DEMO_RENAMED_OPTION = 'redeemid_demo_renamed';

/**
 * Old names in seeded markup => new names. Longest first.
 */
const REDEEMID_DEMO_RENAMED_MARKUP = [
	'sfwp-test/' => 'redeemid-demo/',
	'SFWP-'      => 'LEDGER-',
	'sfwp-'      => 'ledger-',
	'sfwp_'      => 'ledger_',
];

/**
 * The new name of an option or meta key the theme stored.
 */
function redeemid_demo_renamed_key( string $old ): string {
	return (string) preg_replace( [ '/^(_?)sfwp_test_/', '/^(_?)sfwp_/' ], [ '$1redeemid_demo_', '$1ledger_' ], $old );
}

function redeemid_demo_rename(): void {
	global $wpdb;

	if ( get_option( REDEEMID_DEMO_RENAMED_OPTION ) ) {
		return;
	}

	// phpcs:disable WordPress.DB.DirectDatabaseQuery -- a one-time rename across core tables; the caches are flushed below.

	foreach ( $wpdb->get_col( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE 'sfwp\\_%'" ) as $old ) {
		$new = redeemid_demo_renamed_key( $old );

		if ( false === get_option( $new ) ) {
			add_option( $new, get_option( $old ), '', false );
		}

		delete_option( $old );
	}

	foreach ( [ $wpdb->postmeta, $wpdb->usermeta, $wpdb->termmeta ] as $table ) {
		foreach ( $wpdb->get_col( "SELECT DISTINCT meta_key FROM {$table} WHERE meta_key LIKE '\\_sfwp\\_%'" ) as $old ) { // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- core table name.
			$wpdb->update( $table, [ 'meta_key' => redeemid_demo_renamed_key( $old ) ], [ 'meta_key' => $old ] ); // phpcs:ignore WordPress.DB.SlowDBQuery
		}
	}

	$wpdb->query( "UPDATE {$wpdb->posts} SET post_type = CONCAT( 'ledger_', SUBSTRING( post_type, 6 ) ) WHERE post_type LIKE 'sfwp\\_%'" );
	$wpdb->query( "UPDATE {$wpdb->term_taxonomy} SET taxonomy = CONCAT( 'ledger_', SUBSTRING( taxonomy, 6 ) ) WHERE taxonomy LIKE 'sfwp\\_%'" );

	foreach ( $wpdb->get_results( "SELECT ID, user_login FROM {$wpdb->users} WHERE user_login LIKE 'sfwp-%'" ) as $user ) { // phpcs:ignore WordPressVIPMinimum.Variables.RestrictedVariables.user_meta__wpdb__users -- core has no API to change a login.
		$login = 'ledger-' . substr( (string) $user->user_login, strlen( 'sfwp-' ) );

		if ( ! username_exists( $login ) ) {
			$wpdb->update( $wpdb->users, [ 'user_login' => $login, 'user_nicename' => $login, 'user_email' => $login . '@example.com' ], [ 'ID' => (int) $user->ID ] ); // phpcs:ignore WordPressVIPMinimum.Variables.RestrictedVariables.user_meta__wpdb__users
		}
	}

	$seeded = $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s", REDEEMID_DEMO_SEED_META ) );

	foreach ( $seeded as $post_id ) {
		$content = (string) $wpdb->get_var( $wpdb->prepare( "SELECT post_content FROM {$wpdb->posts} WHERE ID = %d", $post_id ) );
		$renamed = strtr( $content, REDEEMID_DEMO_RENAMED_MARKUP );

		if ( $renamed !== $content ) {
			$wpdb->update( $wpdb->posts, [ 'post_content' => $renamed ], [ 'ID' => (int) $post_id ] );
		}
	}

	// phpcs:enable

	// The demo types' rewrite rules carry their old query vars.
	delete_option( 'redeemid_demo_rewrite_version' );
	wp_cache_flush();
	update_option( REDEEMID_DEMO_RENAMED_OPTION, 1, false );
}

add_action( 'init', 'redeemid_demo_rename', 1 );
