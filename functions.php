<?php
/**
 * RedeemID Demo theme.
 *
 * A block theme for the RedeemID sandbox. It only consumes the plugin:
 * sign-in, gating, access badges, features, membership refresh, account
 * status and health are the plugin's blocks and hooks. The theme adds layout, styles,
 * demo content and sandbox-only test tooling (inc/), plus one layout block in
 * blockstudio/ that registers through the plugin's bundled Blockstudio.
 * Without the plugin the theme still renders; the plugin blocks are absent.
 *
 * @package redeemid-demo
 */

defined( 'ABSPATH' ) || exit;

define( 'REDEEMID_DEMO_VERSION', wp_get_theme( get_template() )->get( 'Version' ) );

require_once __DIR__ . '/inc/health.php';
require_once __DIR__ . '/inc/news-content.php';
require_once __DIR__ . '/inc/content-types.php';
require_once __DIR__ . '/inc/features.php';
require_once __DIR__ . '/inc/test-content.php';
require_once __DIR__ . '/inc/fixtures.php';
require_once __DIR__ . '/inc/admin.php';

add_action(
	'after_setup_theme',
	static function (): void {
		add_theme_support( 'editor-styles' );
		add_editor_style( 'assets/theme.css' );
	}
);

add_action(
	'wp_enqueue_scripts',
	static function (): void {
		wp_enqueue_style( 'redeemid-demo', get_theme_file_uri( 'assets/theme.css' ), [], REDEEMID_DEMO_VERSION );
	}
);

add_action(
	'init',
	static function (): void {
		register_block_pattern_category( 'redeemid-demo', [ 'label' => __( 'RedeemID demos', 'redeemid-demo' ) ] );
	}
);

/**
 * Test lab pages get `ledger-lab`, so the gate's debugging labels (which area
 * of a gate reached the page) show there and not on the Ledger's stories.
 */
add_filter(
	'body_class',
	static function ( array $classes ): array {
		$key = is_singular() ? (string) get_post_meta( (int) get_queried_object_id(), REDEEMID_DEMO_SEED_META, true ) : '';

		if ( '' !== $key && ! in_array( $key, [ 'home', 'about', 'join' ], true ) && ! str_starts_with( $key, 'news-' ) && ! str_starts_with( $key, 'cpt-' ) ) {
			$classes[] = 'ledger-lab';
		}

		return $classes;
	}
);
