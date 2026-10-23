<?php
/**
 * Checks behind the sfwp-test/sandbox-health block.
 *
 * @package sfwp-test
 */

defined( 'ABSPATH' ) || exit;

/**
 * Plugin blocks the test pages depend on.
 */
const SFWP_TEST_PLUGIN_BLOCKS = [
	'wp2-shopify/auth-action',
	'wp2-shopify/membership-gate',
	'wp2-shopify/membership-gate-content',
	'wp2-shopify/membership-gate-fallback',
];

/**
 * @param bool $show_config Include which credential constants are defined (never their values).
 * @return array<int, array{label: string, state: string, detail: string}>
 */
function sfwp_test_health_checks( bool $show_config ): array {
	$checks = [];
	$add    = static function ( string $label, string $state, string $detail = '' ) use ( &$checks ): void {
		$checks[] = [ 'label' => $label, 'state' => $state, 'detail' => $detail ];
	};

	$plugin = defined( 'WP2_SHOPIFY_VERSION' );
	$add( __( 'WP2 Shopify plugin', 'sfwp-test' ), $plugin ? 'ok' : 'fail', $plugin ? 'v' . WP2_SHOPIFY_VERSION : __( 'not active', 'sfwp-test' ) );

	$add(
		__( 'Blockstudio', 'sfwp-test' ),
		defined( 'BLOCKSTUDIO_VERSION' ) ? 'ok' : 'fail',
		defined( 'BLOCKSTUDIO_VERSION' ) ? 'v' . BLOCKSTUDIO_VERSION : __( 'not loaded (the plugin bundles it)', 'sfwp-test' )
	);

	$registry   = WP_Block_Type_Registry::get_instance();
	$missing    = array_values( array_filter( SFWP_TEST_PLUGIN_BLOCKS, static fn( $name ) => ! $registry->is_registered( $name ) ) );
	$registered = count( SFWP_TEST_PLUGIN_BLOCKS ) - count( $missing );
	$add(
		__( 'Plugin blocks registered', 'sfwp-test' ),
		$missing ? 'fail' : 'ok',
		$missing
			/* translators: %s: block names */
			? sprintf( __( 'missing: %s', 'sfwp-test' ), implode( ', ', $missing ) )
			/* translators: %d: number of blocks */
			: sprintf( _n( '%d block', '%d blocks', $registered, 'sfwp-test' ), $registered )
	);

	$theme_blocks = $registry->is_registered( 'sfwp-test/visitor-state' ) && $registry->is_registered( 'sfwp-test/sandbox-health' );
	$add( __( 'Theme blocks registered beside the plugin’s', 'sfwp-test' ), $theme_blocks ? 'ok' : 'warn', $theme_blocks ? __( 'Blockstudio theme folder intact', 'sfwp-test' ) : __( 'theme blockstudio/ folder not picked up', 'sfwp-test' ) );

	$pretty = '' !== (string) get_option( 'permalink_structure' );
	$add( __( 'Pretty permalinks', 'sfwp-test' ), $pretty ? 'ok' : 'fail', $pretty ? (string) get_option( 'permalink_structure' ) : __( 'required for the sign-in callback', 'sfwp-test' ) );

	$slug     = sfwp_test_callback_slug();
	$callback = get_page_by_path( $slug );
	$add(
		__( 'Callback page', 'sfwp-test' ),
		$callback && 'publish' === $callback->post_status ? 'ok' : 'warn',
		$callback && 'publish' === $callback->post_status ? '/' . $slug . '/' : __( 'not published; the plugin falls back to a bare result page', 'sfwp-test' )
	);

	if ( class_exists( \WP2_Shopify\Shopify\Discovery::class ) ) {
		$available = \WP2_Shopify\Shopify\Discovery::available();
		$add( __( 'Shopify sign-in available', 'sfwp-test' ), $available ? 'ok' : 'fail', $available ? __( 'OIDC discovery answered', 'sfwp-test' ) : __( 'no discovery: check the store domain and client ID', 'sfwp-test' ) );
	}

	if ( $show_config ) {
		$set = static fn( string $name ): bool => defined( $name ) && '' !== (string) constant( $name );

		$add( 'WP2_SHOPIFY_STORE_DOMAIN', $set( 'WP2_SHOPIFY_STORE_DOMAIN' ) ? 'ok' : 'fail', $set( 'WP2_SHOPIFY_STORE_DOMAIN' ) ? __( 'set', 'sfwp-test' ) : __( 'missing (required)', 'sfwp-test' ) );
		$add( 'WP2_SHOPIFY_CLIENT_ID', $set( 'WP2_SHOPIFY_CLIENT_ID' ) ? 'ok' : 'fail', $set( 'WP2_SHOPIFY_CLIENT_ID' ) ? __( 'set', 'sfwp-test' ) : __( 'missing (required)', 'sfwp-test' ) );
		$add( 'WP2_SHOPIFY_CLIENT_SECRET', 'info', $set( 'WP2_SHOPIFY_CLIENT_SECRET' ) || $set( 'WP2_SHOPIFY_PRIVATE_ACCESS_TOKEN' ) ? __( 'set (confidential client)', 'sfwp-test' ) : __( 'not set (public client)', 'sfwp-test' ) );

		$admin = $set( 'WP2_SHOPIFY_ADMIN_CLIENT_ID' ) && $set( 'WP2_SHOPIFY_ADMIN_CLIENT_SECRET' );
		$add( __( 'Admin app credentials', 'sfwp-test' ), $admin ? 'ok' : 'warn', $admin ? __( 'set', 'sfwp-test' ) : __( 'missing: purchase-to-tag, reconciliation and webhook registration are off', 'sfwp-test' ) );
	}

	$version = (int) get_option( SFWP_TEST_CONTENT_OPTION, 0 );
	$add( __( 'Test content', 'sfwp-test' ), $version >= SFWP_TEST_CONTENT_VERSION ? 'ok' : 'warn', $version >= SFWP_TEST_CONTENT_VERSION ? sprintf( 'v%d', $version ) : __( 'not seeded yet', 'sfwp-test' ) );

	return $checks;
}

/**
 * The plugin's callback slug, or its default when the plugin is inactive.
 */
function sfwp_test_callback_slug(): string {
	return class_exists( \WP2_Shopify\Config::class ) ? \WP2_Shopify\Config::callback_slug() : 'auth-callback';
}
