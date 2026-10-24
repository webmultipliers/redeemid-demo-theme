<?php
/**
 * Seeds the Test lab: the pages and post the sandbox tests run against, plus
 * the Harborview Ledger news stories (inc/news-content.php), the demo
 * content types (inc/content-types.php) and the test accounts
 * (inc/fixtures.php).
 *
 * Content comes from the theme's patterns/ files, so a deploy that changes a
 * pattern can be applied with Shopify → Test lab → Restore. Seeding only
 * creates what is missing; it never overwrites a page someone edited unless
 * an administrator asks for a restore.
 *
 * @package sfwp-test
 */

defined( 'ABSPATH' ) || exit;

/** Bump when a new seeded item is added, so existing sites pick it up on the next request. */
const SFWP_TEST_CONTENT_VERSION = 7;
const SFWP_TEST_CONTENT_OPTION  = 'sfwp_test_content_version';
const SFWP_TEST_SEED_META       = '_sfwp_test_seed';

/**
 * Every seeded item. An item takes its markup from a theme pattern
 * (`pattern`) or a callback (`content`), and may set an excerpt, a category
 * (or several), tags, terms of other taxonomies, a publish date and post meta.
 *
 * @return array<string, array<string, mixed>>
 */
function sfwp_test_seed_items(): array {
	return [
		'home'                 => [ 'type' => 'page', 'title' => __( 'Home', 'sfwp-test' ), 'slug' => 'home', 'pattern' => 'page-home' ],
		'about'                => [ 'type' => 'page', 'title' => __( 'About the Ledger', 'sfwp-test' ), 'slug' => 'about', 'pattern' => 'page-about' ],
		'test-lab'             => [ 'type' => 'page', 'title' => __( 'Test lab', 'sfwp-test' ), 'slug' => 'test-lab', 'pattern' => 'page-test-lab' ],
		'auth-callback'        => [ 'type' => 'page', 'title' => __( 'Signing you in', 'sfwp-test' ), 'slug' => sfwp_test_callback_slug(), 'pattern' => 'page-auth-callback' ],
		'sign-in-controls'     => [ 'type' => 'page', 'title' => __( 'Sign-in controls', 'sfwp-test' ), 'slug' => 'sign-in-controls', 'pattern' => 'page-sign-in-controls' ],
		'members-area'         => [ 'type' => 'page', 'title' => __( 'Members area', 'sfwp-test' ), 'slug' => 'members-area', 'pattern' => 'page-members-area' ],
		'tag-gates'            => [ 'type' => 'page', 'title' => __( 'Tag gates', 'sfwp-test' ), 'slug' => 'tag-gates', 'pattern' => 'page-tag-gates' ],
		'gate-redirect'        => [ 'type' => 'page', 'title' => __( 'Redirect gate', 'sfwp-test' ), 'slug' => 'gate-redirect', 'pattern' => 'page-gate-redirect' ],
		'join'                 => [ 'type' => 'page', 'title' => __( 'Become a member', 'sfwp-test' ), 'slug' => 'join', 'pattern' => 'page-join' ],
		'account'              => [ 'type' => 'page', 'title' => __( 'Account', 'sfwp-test' ), 'slug' => 'account', 'pattern' => 'page-account' ],
		'members-only-article' => [ 'type' => 'post', 'title' => __( 'Members-only article', 'sfwp-test' ), 'slug' => 'members-only-article', 'pattern' => 'post-members-only-article' ],
	] + sfwp_test_shop_seed_items() + sfwp_test_news_seed_items() + sfwp_test_cpt_seed_items();
}

/**
 * The Shop page, only while Shopify's WordPress plugin is active: its blocks
 * render nothing without it.
 */
function sfwp_test_shop_seed_items(): array {
	return defined( 'SHOPIFY_WP_VERSION' )
		? [ 'shop' => [ 'type' => 'page', 'title' => __( 'Shop', 'sfwp-test' ), 'slug' => 'shop', 'pattern' => 'page-shop' ] ]
		: [];
}

/**
 * Seed the Shop page when Shopify's plugin is activated after the rest of the
 * test content, without reseeding anything else.
 */
function sfwp_test_maybe_seed_shop(): void {
	if ( ! defined( 'SHOPIFY_WP_VERSION' ) || wp_installing() || get_option( 'sfwp_test_shop_seeded' ) ) {
		return;
	}

	if ( (int) get_option( SFWP_TEST_CONTENT_OPTION, 0 ) < SFWP_TEST_CONTENT_VERSION ) {
		return; // The full seed will create it.
	}

	if ( ! sfwp_test_find( 'shop' ) ) {
		sfwp_test_seed( false );
	}

	update_option( 'sfwp_test_shop_seeded', time(), false );
}
add_action( 'init', 'sfwp_test_maybe_seed_shop', 21 );

/**
 * Seed on the first request after activation or after a deploy that bumps the version.
 * Runs on init, after patterns and the plugin's blocks are registered.
 */
function sfwp_test_maybe_seed(): void {
	if ( wp_installing() || (int) get_option( SFWP_TEST_CONTENT_OPTION, 0 ) >= SFWP_TEST_CONTENT_VERSION ) {
		return;
	}

	// One request seeds; concurrent ones skip instead of creating duplicates.
	if ( ! add_option( 'sfwp_test_seeding', time(), '', false ) ) {
		if ( time() - (int) get_option( 'sfwp_test_seeding' ) < MINUTE_IN_SECONDS ) {
			return;
		}
		update_option( 'sfwp_test_seeding', time(), false );
	}

	$from = (int) get_option( SFWP_TEST_CONTENT_OPTION, 0 );

	sfwp_test_seed( false );
	sfwp_test_upgrade_content( $from );
	sfwp_test_seed_fixtures();
	sfwp_test_set_front_page();

	update_option( SFWP_TEST_CONTENT_OPTION, SFWP_TEST_CONTENT_VERSION );
	delete_option( 'sfwp_test_seeding' );
}
add_action( 'init', 'sfwp_test_maybe_seed', 20 );

/**
 * Create missing items, and with $restore also rewrite existing seeded items
 * from their patterns. $only limits both to those item keys.
 *
 * @param string[]|null $only Item keys, or null for every item.
 * @return array<string, string> Item key => created|restored|kept|failed.
 */
function sfwp_test_seed( bool $restore, ?array $only = null ): array {
	$results = [];
	$author  = sfwp_test_author();
	$items   = sfwp_test_seed_items();

	sfwp_test_seed_categories();
	sfwp_test_seed_cpt_terms();

	foreach ( null === $only ? $items : array_intersect_key( $items, array_flip( $only ) ) as $key => $item ) {
		$content  = isset( $item['content'] ) ? trim( (string) ( $item['content'] )() ) : sfwp_test_pattern_content( $item['pattern'] );
		$existing = sfwp_test_find( $key );

		if ( '' === $content ) {
			$results[ $key ] = 'failed';
			continue;
		}

		if ( $existing && ! $restore ) {
			$results[ $key ] = 'kept';
			continue;
		}

		$postarr = [
			'post_type'    => $item['type'],
			'post_title'   => $item['title'],
			'post_name'    => $item['slug'],
			'post_content' => $content,
			'post_status'  => 'publish',
			'post_author'  => $author,
			'comment_status' => 'closed',
			'meta_input'   => [ SFWP_TEST_SEED_META => $key ] + ( $item['meta'] ?? [] ),
		];

		if ( isset( $item['excerpt'] ) ) {
			// An explicit excerpt keeps listings to the public dek, never the gated body.
			$postarr['post_excerpt'] = $item['excerpt'];
		}

		$categories = array_filter( array_map( 'sfwp_test_term_id', $item['categories'] ?? ( isset( $item['category'] ) ? [ $item['category'] ] : [] ) ) );

		if ( $categories ) {
			$postarr['post_category'] = array_values( $categories );
		}

		if ( ! empty( $item['tags'] ) ) {
			$postarr['tags_input'] = $item['tags'];
		}

		if ( isset( $item['date'] ) ) {
			$postarr['post_date']     = get_date_from_gmt( $item['date'] );
			$postarr['post_date_gmt'] = $item['date'];
		}

		if ( $existing ) {
			$postarr['ID'] = $existing->ID;
		}

		// Block markup must be stored as is; kses would strip the gate's comment attributes for low-privilege authors.
		kses_remove_filters();
		$id = wp_insert_post( wp_slash( $postarr ), true );
		kses_init();

		if ( ! is_wp_error( $id ) ) {
			// After the insert, not tax_input: seeding often runs for a guest, who may not assign terms.
			foreach ( $item['terms'] ?? [] as $taxonomy => $slugs ) {
				if ( taxonomy_exists( $taxonomy ) ) {
					wp_set_object_terms( $id, $slugs, $taxonomy );
				}
			}
		}

		$results[ $key ] = is_wp_error( $id ) ? 'failed' : ( $existing ? 'restored' : 'created' );
	}

	// A partial restore (a content upgrade) leaves rules an administrator changed alone.
	sfwp_test_seed_access_rules( $restore && null === $only );
	sfwp_test_seed_features( $restore && null === $only );

	return $results;
}

/**
 * Make the news home the front page: on first activation if the site still
 * lists latest posts, and on upgrade if the front page is the Test lab, as
 * content version 1 set it.
 */
function sfwp_test_set_front_page(): void {
	$home = sfwp_test_find( 'home' );
	$lab  = sfwp_test_find( 'test-lab' );

	if ( ! $home ) {
		return;
	}

	$front = (int) get_option( 'page_on_front' );

	if ( 'posts' === get_option( 'show_on_front' ) || ( $lab && $front === $lab->ID ) || ! $front ) {
		update_option( 'page_on_front', $home->ID );
		update_option( 'show_on_front', 'page' );
	}
}

/**
 * A seeded item: by its seed key first, then by slug for a page someone created by hand.
 */
function sfwp_test_find( string $key ): ?WP_Post {
	$item  = sfwp_test_seed_items()[ $key ] ?? null;
	$found = get_posts(
		[
			'post_type'      => $item['type'] ?? 'any',
			'post_status'    => 'any',
			'meta_key'       => SFWP_TEST_SEED_META, // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'     => $key, // phpcs:ignore WordPress.DB.SlowDBQuery
			'posts_per_page' => 1,
			'no_found_rows'  => true,
		]
	);

	if ( $found ) {
		return $found[0];
	}

	if ( ! $item ) {
		return null;
	}

	$by_slug = get_page_by_path( $item['slug'], OBJECT, $item['type'] );

	return $by_slug instanceof WP_Post && 'trash' !== $by_slug->post_status ? $by_slug : null;
}

/**
 * Markup of a theme pattern file, without its header.
 */
function sfwp_test_pattern_content( string $pattern ): string {
	$file = get_theme_file_path( 'patterns/' . $pattern . '.php' );

	if ( ! is_readable( $file ) ) {
		return '';
	}

	ob_start();
	include $file;

	return trim( (string) ob_get_clean() );
}

/**
 * The plugin's callback slug, or its default when the plugin is inactive.
 */
function sfwp_test_callback_slug(): string {
	return class_exists( \WP2_Shopify\Config::class ) ? \WP2_Shopify\Config::callback_slug() : 'auth-callback';
}

function sfwp_test_author(): int {
	if ( current_user_can( 'manage_options' ) ) {
		return get_current_user_id();
	}

	$admins = get_users( [ 'role' => 'administrator', 'number' => 1, 'orderby' => 'ID', 'fields' => 'ID' ] );

	return $admins ? (int) $admins[0] : 0;
}
