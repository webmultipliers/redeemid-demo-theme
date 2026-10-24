<?php
/**
 * The theme's own rows in the plugin's Shopify Health block.
 *
 * Plugin, Blockstudio, permalink, callback-page, sign-in and configuration
 * checks belong to the plugin (WP2_Shopify\Support\Health), which shows them
 * in the wp2-shopify/health block and under Shopify → Debug. The theme only
 * adds what is about the theme and its test content, through the plugin's
 * wp2_shopify_health_checks filter.
 *
 * @package sfwp-test
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'wp2_shopify_health_checks', 'sfwp_test_health_rows' );

/**
 * @param mixed $checks The plugin's rows.
 * @return mixed
 */
function sfwp_test_health_rows( $checks ) {
	if ( ! is_array( $checks ) ) {
		return $checks;
	}

	$theme_blocks = WP_Block_Type_Registry::get_instance()->is_registered( 'sfwp-test/story-meta' );
	$checks[]     = [
		'label'  => __( 'Theme blocks registered beside the plugin’s', 'sfwp-test' ),
		'state'  => $theme_blocks ? 'ok' : 'warn',
		'detail' => $theme_blocks ? __( 'Blockstudio theme folder intact', 'sfwp-test' ) : __( 'theme blockstudio/ folder not picked up', 'sfwp-test' ),
	];

	if ( function_exists( 'bs_validate_block' ) ) {
		$stale    = sfwp_test_invalid_blocks();
		$checks[] = [
			'label'  => __( 'Seeded blocks match their fields', 'sfwp-test' ),
			'state'  => $stale ? 'warn' : 'ok',
			'detail' => $stale
				/* translators: %s: list of "page: block field" entries */
				? sprintf( __( 'stored in an older shape (still rendered, via the plugin\'s upgrade shim): %s. Use Shopify → Test lab → Restore.', 'sfwp-test' ), implode( ', ', array_slice( $stale, 0, 5 ) ) . ( count( $stale ) > 5 ? ', …' : '' ) )
				: __( 'checked with bs_validate_block()', 'sfwp-test' ),
		];
	}

	$version  = (int) get_option( SFWP_TEST_CONTENT_OPTION, 0 );
	$checks[] = [
		'label'  => __( 'Test content', 'sfwp-test' ),
		'state'  => $version >= SFWP_TEST_CONTENT_VERSION ? 'ok' : 'warn',
		'detail' => $version >= SFWP_TEST_CONTENT_VERSION ? sprintf( 'v%d', $version ) : __( 'not seeded yet', 'sfwp-test' ),
	];

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
