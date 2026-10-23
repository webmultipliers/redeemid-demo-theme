<?php
/**
 * Shopify for WP Test theme.
 *
 * A block theme for the WP2 Shopify sandbox. Its own blocks live in
 * blockstudio/ and register through the Blockstudio copy the plugin bundles,
 * next to the plugin's blocks. Without the plugin the theme still renders;
 * the plugin and theme blocks are simply absent.
 *
 * @package sfwp-test
 */

defined( 'ABSPATH' ) || exit;

define( 'SFWP_TEST_VERSION', wp_get_theme( get_template() )->get( 'Version' ) );

require_once __DIR__ . '/inc/health.php';
require_once __DIR__ . '/inc/news-content.php';
require_once __DIR__ . '/inc/test-content.php';
require_once __DIR__ . '/inc/fixtures.php';
require_once __DIR__ . '/inc/admin.php';
require_once __DIR__ . '/inc/shopify-official.php';

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
		wp_enqueue_style( 'sfwp-test', get_theme_file_uri( 'assets/theme.css' ), [], SFWP_TEST_VERSION );
	}
);

add_action(
	'init',
	static function (): void {
		register_block_pattern_category( 'sfwp-test', [ 'label' => __( 'Shopify for WP tests', 'sfwp-test' ) ] );
	}
);
