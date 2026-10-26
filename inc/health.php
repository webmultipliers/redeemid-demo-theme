<?php
/**
 * The theme's own rows in the plugin's Shopify Health block.
 *
 * Plugin, Blockstudio, permalink, callback-page, sign-in and configuration
 * checks belong to the plugin (RedeemID\Support\Health), which shows them
 * in the redeemid/health block and under RedeemID → Debug. The theme only
 * adds what is about the theme and its test content, through the plugin's
 * redeemid_health_checks filter.
 *
 * @package redeemid-demo
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'redeemid_health_checks', 'redeemid_demo_health_rows' );

/**
 * @param mixed $checks The plugin's rows.
 * @return mixed
 */
function redeemid_demo_health_rows( $checks ) {
	if ( ! is_array( $checks ) ) {
		return $checks;
	}

	$theme_blocks = WP_Block_Type_Registry::get_instance()->is_registered( 'redeemid-demo/story-meta' );
	$checks[]     = [
		'label'  => __( 'Theme blocks registered beside the plugin’s', 'redeemid-demo' ),
		'state'  => $theme_blocks ? 'ok' : 'warn',
		'detail' => $theme_blocks ? __( 'Blockstudio theme folder intact', 'redeemid-demo' ) : __( 'theme blockstudio/ folder not picked up', 'redeemid-demo' ),
	];

	if ( function_exists( 'bs_validate_block' ) ) {
		$stale    = redeemid_demo_invalid_blocks();
		$checks[] = [
			'label'  => __( 'Seeded blocks match their fields', 'redeemid-demo' ),
			'state'  => $stale ? 'warn' : 'ok',
			'detail' => $stale
				/* translators: %s: list of "page: block field" entries */
				? sprintf( __( 'stored in a shape their fields don\'t read: %s. Use RedeemID → Test lab → Restore.', 'redeemid-demo' ), implode( ', ', array_slice( $stale, 0, 5 ) ) . ( count( $stale ) > 5 ? ', …' : '' ) )
				: __( 'checked with bs_validate_block()', 'redeemid-demo' ),
		];
	}

	$version  = (int) get_option( REDEEMID_DEMO_CONTENT_OPTION, 0 );
	$checks[] = [
		'label'  => __( 'Test content', 'redeemid-demo' ),
		'state'  => $version >= REDEEMID_DEMO_CONTENT_VERSION ? 'ok' : 'warn',
		'detail' => $version >= REDEEMID_DEMO_CONTENT_VERSION ? sprintf( 'v%d', $version ) : __( 'not seeded yet', 'redeemid-demo' ),
	];

	return $checks;
}

/**
 * Seeded blocks whose stored values no longer match their Blockstudio fields,
 * for example a URL saved as text in a link field.
 * Uses Blockstudio's own validator rather than re-implementing field checks.
 *
 * @return string[] "Page title: block field" entries.
 */
function redeemid_demo_invalid_blocks(): array {
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

	foreach ( array_keys( redeemid_demo_seed_items() ) as $key ) {
		$post = redeemid_demo_find( (string) $key );

		if ( $post ) {
			$walk( parse_blocks( $post->post_content ), $post );
		}
	}

	return array_values( array_unique( $found ) );
}
