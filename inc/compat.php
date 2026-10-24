<?php
/**
 * Blocks the theme used to ship and the plugin now provides.
 *
 * Up to theme 0.5, the refresh button, the visitor panel and the health
 * checklist were theme blocks. They are plugin blocks now, so content saved
 * with the old names (a page edited by hand, a template part customised in
 * the Site Editor) renders the plugin block instead of nothing. Content the
 * theme seeded is rewritten once by sfwp_test_upgrade_content().
 *
 * @package sfwp-test
 */

defined( 'ABSPATH' ) || exit;

/**
 * Old theme block name => the plugin block that replaced it.
 */
const SFWP_TEST_MOVED_BLOCKS = [
	'sfwp-test/resync'         => 'wp2-shopify/membership-refresh',
	'sfwp-test/visitor-state'  => 'wp2-shopify/account-status',
	'sfwp-test/sandbox-health' => 'wp2-shopify/health',
];

add_filter(
	'render_block_data',
	static function ( array $parsed ): array {
		$name = $parsed['blockName'] ?? '';

		if ( ! isset( SFWP_TEST_MOVED_BLOCKS[ $name ] ) ) {
			return $parsed;
		}

		$parsed['blockName'] = SFWP_TEST_MOVED_BLOCKS[ $name ];

		// The theme's visitor panel showed the raw response unless told not to; the plugin's hides it by default.
		if ( 'sfwp-test/visitor-state' === $name && ! array_key_exists( 'showRaw', (array) $parsed['attrs'] ) ) {
			$parsed['attrs']['showRaw'] = true;
		}

		return $parsed;
	},
	4 // Before the plugin's own upgrade shim at 5.
);

/**
 * One-off rewrites of seeded content when the content version goes up.
 *
 * - Before 5: rename the moved blocks in every seeded item, and rewrite the
 *   gated news stories from the theme so their gates ask for the plugin's
 *   access levels (the Access Badge names a gate's level, not its tags).
 *
 * - Before 6: rewrite the home, Become a member and Test lab pages from the
 *   theme, for the new front page, the reader-facing membership page and the
 *   content-type rule table.
 *
 * - Before 7: rewrite the Account and Test lab pages from the theme, for the
 *   Membership Features list and the feature table.
 *
 * @param int $from The content version the site had.
 */
function sfwp_test_upgrade_content( int $from ): void {
	if ( $from < 1 ) {
		return;
	}

	if ( $from < 6 ) {
		sfwp_test_seed( true, [ 'home', 'join', 'test-lab' ] );
	}

	if ( $from < 7 ) {
		sfwp_test_seed( true, [ 'account', 'test-lab' ] );
	}

	if ( $from >= 5 ) {
		return;
	}

	$stories = array_values( array_filter( array_keys( sfwp_test_seed_items() ), static fn( $key ) => str_starts_with( (string) $key, 'news-' ) ) );
	sfwp_test_seed( true, $stories );

	foreach ( array_keys( sfwp_test_seed_items() ) as $key ) {
		$post = sfwp_test_find( (string) $key );

		if ( ! $post || ! str_contains( $post->post_content, 'wp:sfwp-test/' ) ) {
			continue;
		}

		$content = $post->post_content;

		foreach ( SFWP_TEST_MOVED_BLOCKS as $old => $new ) {
			$content = str_replace( [ '<!-- wp:' . $old . ' ', '<!-- /wp:' . $old . ' ' ], [ '<!-- wp:' . $new . ' ', '<!-- /wp:' . $new . ' ' ], $content );
		}

		if ( $content !== $post->post_content ) {
			kses_remove_filters();
			wp_update_post( wp_slash( [ 'ID' => $post->ID, 'post_content' => $content ] ) );
			kses_init();
		}
	}
}
