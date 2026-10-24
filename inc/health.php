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
	'wp2-shopify/plan-card',
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

	if ( class_exists( \WP2_Shopify\Integrations\OfficialPlugin::class ) ) {
		$official = \WP2_Shopify\Integrations\OfficialPlugin::info();
		$states   = [
			\WP2_Shopify\Integrations\OfficialPlugin::ABSENT      => 'info',
			\WP2_Shopify\Integrations\OfficialPlugin::UNCONNECTED => 'warn',
			\WP2_Shopify\Integrations\OfficialPlugin::SAME_STORE  => 'ok',
			\WP2_Shopify\Integrations\OfficialPlugin::OTHER_STORE => 'fail',
		];
		$add( __( 'Shopify’s WordPress plugin', 'sfwp-test' ), $states[ $official['state'] ] ?? 'warn', \WP2_Shopify\Integrations\OfficialPlugin::summary() );

		if ( \WP2_Shopify\Integrations\OfficialPlugin::ABSENT !== $official['state'] ) {
			$add( __( '/cart owner', 'sfwp-test' ), 'info', $official['owns_cart'] ? __( 'Shopify’s plugin (its rewrite rule wins over pages)', 'sfwp-test' ) : __( 'nobody yet: flush permalinks', 'sfwp-test' ) );

			if ( class_exists( \WP2_Shopify\Commerce\CartBridge::class ) ) {
				$bridge = \WP2_Shopify\Commerce\CartBridge::enabled();
				$last   = \WP2_Shopify\Commerce\CartBridge::last();
				$add(
					__( 'Cart sign-in bridge', 'sfwp-test' ),
					$bridge ? 'ok' : 'warn',
					$bridge
						/* translators: %s: date and time, or "never" */
						? sprintf( __( 'active, last attached %s', 'sfwp-test' ), $last['ok_at'] ? wp_date( 'Y-m-d H:i', (int) $last['ok_at'] ) : __( 'never', 'sfwp-test' ) )
						: __( 'off: needs the same store and WP2_SHOPIFY_PUBLIC_ACCESS_TOKEN', 'sfwp-test' )
				);
			}
		}
	}

	if ( $show_config ) {
		$set = static fn( string $name ): bool => defined( $name ) && '' !== (string) constant( $name );

		$add( 'WP2_SHOPIFY_STORE_DOMAIN', $set( 'WP2_SHOPIFY_STORE_DOMAIN' ) ? 'ok' : 'fail', $set( 'WP2_SHOPIFY_STORE_DOMAIN' ) ? __( 'set', 'sfwp-test' ) : __( 'missing (required)', 'sfwp-test' ) );
		$add( 'WP2_SHOPIFY_CLIENT_ID', $set( 'WP2_SHOPIFY_CLIENT_ID' ) ? 'ok' : 'fail', $set( 'WP2_SHOPIFY_CLIENT_ID' ) ? __( 'set', 'sfwp-test' ) : __( 'missing (required)', 'sfwp-test' ) );
		$add( 'WP2_SHOPIFY_CLIENT_SECRET', 'info', $set( 'WP2_SHOPIFY_CLIENT_SECRET' ) || $set( 'WP2_SHOPIFY_PRIVATE_ACCESS_TOKEN' ) ? __( 'set (confidential client)', 'sfwp-test' ) : __( 'not set (public client)', 'sfwp-test' ) );

		$add( 'WP2_SHOPIFY_PUBLIC_ACCESS_TOKEN', $set( 'WP2_SHOPIFY_PUBLIC_ACCESS_TOKEN' ) ? 'ok' : 'warn', $set( 'WP2_SHOPIFY_PUBLIC_ACCESS_TOKEN' ) ? __( 'set', 'sfwp-test' ) : __( 'missing: plan cards show as unavailable', 'sfwp-test' ) );

		$admin = $set( 'WP2_SHOPIFY_ADMIN_CLIENT_ID' ) && $set( 'WP2_SHOPIFY_ADMIN_CLIENT_SECRET' );
		$add( __( 'Admin app credentials', 'sfwp-test' ), $admin ? 'ok' : 'warn', $admin ? __( 'set', 'sfwp-test' ) : __( 'missing: purchase-to-tag, reconciliation and webhook registration are off', 'sfwp-test' ) );
	}

	if ( function_exists( 'bs_validate_block' ) ) {
		$stale = sfwp_test_invalid_blocks();
		$add(
			__( 'Seeded blocks match their fields', 'sfwp-test' ),
			$stale ? 'warn' : 'ok',
			$stale
				/* translators: %s: list of "page: block field" entries */
				? sprintf( __( 'stored in an older shape (still rendered, via the plugin\'s upgrade shim): %s. Use Shopify → Test lab → Restore.', 'sfwp-test' ), implode( ', ', array_slice( $stale, 0, 5 ) ) . ( count( $stale ) > 5 ? ', …' : '' ) )
				: __( 'checked with bs_validate_block()', 'sfwp-test' )
		);
	}

	$version = (int) get_option( SFWP_TEST_CONTENT_OPTION, 0 );
	$add( __( 'Test content', 'sfwp-test' ), $version >= SFWP_TEST_CONTENT_VERSION ? 'ok' : 'warn', $version >= SFWP_TEST_CONTENT_VERSION ? sprintf( 'v%d', $version ) : __( 'not seeded yet', 'sfwp-test' ) );

	return $checks;
}

/**
 * Seeded blocks whose stored values no longer match their Blockstudio fields,
 * for example a URL saved as text before the field became a link field.
 * Uses Blockstudio's own validator rather than re-implementing field checks.
 *
 * @return string[] "Page title: block field" entries.
 */
function sfwp_test_invalid_blocks(): array {
	$found = [];
	$walk  = static function ( array $blocks, WP_Post $post ) use ( &$walk, &$found ): void {
		foreach ( $blocks as $block ) {
			if ( ! empty( $block['blockName'] ) ) {
				foreach ( bs_validate_block( $block['blockName'], $block['attrs'] ?? [] ) ?? [] as $finding ) {
					$found[] = sprintf( '%s: %s %s', $post->post_title, $block['blockName'], $finding['path'] );
				}
			}

			$walk( $block['innerBlocks'] ?? [], $post );
		}
	};

	foreach ( array_keys( sfwp_test_seed_items() ) as $key ) {
		$post = sfwp_test_find( (string) $key );

		if ( $post ) {
			$walk( parse_blocks( $post->post_content ), $post );
		}
	}

	return array_values( array_unique( $found ) );
}

/**
 * The plugin's callback slug, or its default when the plugin is inactive.
 */
function sfwp_test_callback_slug(): string {
	return class_exists( \WP2_Shopify\Config::class ) ? \WP2_Shopify\Config::callback_slug() : 'auth-callback';
}
