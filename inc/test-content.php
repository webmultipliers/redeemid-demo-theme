<?php
/**
 * Seeds the Test lab: the pages and post the sandbox tests run against, plus
 * the Harborview Ledger news stories (inc/news-content.php), the demo
 * content types (inc/content-types.php) and the test accounts
 * (inc/fixtures.php).
 *
 * Content comes from the theme's patterns/ files, so a deploy that changes a
 * pattern can be applied with RedeemID → Test lab → Restore. Seeding only
 * creates what is missing; it never overwrites a page someone edited unless
 * an administrator asks for a restore.
 *
 * @package redeemid-demo
 */

defined( 'ABSPATH' ) || exit;

/** Bump when a new seeded item is added, so existing sites pick it up on the next request. */
const REDEEMID_DEMO_CONTENT_VERSION = 10;
const REDEEMID_DEMO_CONTENT_OPTION  = 'redeemid_demo_content_version';
const REDEEMID_DEMO_SEED_META       = '_redeemid_demo_seed';

/**
 * Every seeded item. An item takes its markup from a theme pattern
 * (`pattern`) or a callback (`content`), and may set an excerpt, a category
 * (or several), tags, terms of other taxonomies, a publish date and post meta.
 *
 * @return array<string, array<string, mixed>>
 */
function redeemid_demo_seed_items(): array {
	return [
		'home'                 => [ 'type' => 'page', 'title' => __( 'Home', 'redeemid-demo' ), 'slug' => 'home', 'pattern' => 'page-home' ],
		'about'                => [ 'type' => 'page', 'title' => __( 'About the Ledger', 'redeemid-demo' ), 'slug' => 'about', 'pattern' => 'page-about' ],
		'test-lab'             => [ 'type' => 'page', 'title' => __( 'Test lab', 'redeemid-demo' ), 'slug' => 'test-lab', 'pattern' => 'page-test-lab' ],
		'auth-callback'        => [ 'type' => 'page', 'title' => __( 'Signing you in', 'redeemid-demo' ), 'slug' => redeemid_demo_callback_slug(), 'pattern' => 'page-auth-callback' ],
		'sign-in-controls'     => [ 'type' => 'page', 'title' => __( 'Sign-in controls', 'redeemid-demo' ), 'slug' => 'sign-in-controls', 'pattern' => 'page-sign-in-controls' ],
		'members-area'         => [ 'type' => 'page', 'title' => __( 'Members area', 'redeemid-demo' ), 'slug' => 'members-area', 'pattern' => 'page-members-area' ],
		'tag-gates'            => [ 'type' => 'page', 'title' => __( 'Tag gates', 'redeemid-demo' ), 'slug' => 'tag-gates', 'pattern' => 'page-tag-gates' ],
		'gate-redirect'        => [ 'type' => 'page', 'title' => __( 'Redirect gate', 'redeemid-demo' ), 'slug' => 'gate-redirect', 'pattern' => 'page-gate-redirect' ],
		'join'                 => [ 'type' => 'page', 'title' => __( 'Become a member', 'redeemid-demo' ), 'slug' => 'join', 'pattern' => 'page-join' ],
		'account'              => [ 'type' => 'page', 'title' => __( 'Account', 'redeemid-demo' ), 'slug' => 'account', 'pattern' => 'page-account' ],
		'members-only-article' => [ 'type' => 'post', 'title' => __( 'Members-only article', 'redeemid-demo' ), 'slug' => 'members-only-article', 'pattern' => 'post-members-only-article' ],
		'shop'                 => [ 'type' => 'page', 'title' => __( 'Shop', 'redeemid-demo' ), 'slug' => 'shop', 'pattern' => 'page-shop' ],
	] + redeemid_demo_news_seed_items() + redeemid_demo_cpt_seed_items();
}

/**
 * Seed on the first request after activation or after a deploy that bumps the version.
 * Runs on init, after patterns and the plugin's blocks are registered.
 */
function redeemid_demo_maybe_seed(): void {
	if ( wp_installing() || (int) get_option( REDEEMID_DEMO_CONTENT_OPTION, 0 ) >= REDEEMID_DEMO_CONTENT_VERSION ) {
		return;
	}

	// One request seeds; concurrent ones skip instead of creating duplicates.
	if ( ! add_option( 'redeemid_demo_seeding', time(), '', false ) ) {
		if ( time() - (int) get_option( 'redeemid_demo_seeding' ) < MINUTE_IN_SECONDS ) {
			return;
		}
		update_option( 'redeemid_demo_seeding', time(), false );
	}

	redeemid_demo_seed( false );
	redeemid_demo_seed_fixtures();
	redeemid_demo_set_front_page();

	update_option( REDEEMID_DEMO_CONTENT_OPTION, REDEEMID_DEMO_CONTENT_VERSION );
	delete_option( 'redeemid_demo_seeding' );
}
add_action( 'init', 'redeemid_demo_maybe_seed', 20 );

/**
 * Create missing items, and with $restore also rewrite existing seeded items
 * from their patterns. $only limits both to those item keys.
 *
 * @param string[]|null $only Item keys, or null for every item.
 * @return array<string, string> Item key => created|restored|kept|failed.
 */
function redeemid_demo_seed( bool $restore, ?array $only = null ): array {
	$results = [];
	$author  = redeemid_demo_author();
	$items   = redeemid_demo_seed_items();

	redeemid_demo_seed_categories();
	redeemid_demo_seed_cpt_terms();

	foreach ( null === $only ? $items : array_intersect_key( $items, array_flip( $only ) ) as $key => $item ) {
		$content  = isset( $item['content'] ) ? trim( (string) ( $item['content'] )() ) : redeemid_demo_pattern_content( $item['pattern'] );
		$existing = redeemid_demo_find( $key );

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
			'meta_input'   => [ REDEEMID_DEMO_SEED_META => $key ] + ( $item['meta'] ?? [] ),
		];

		if ( isset( $item['excerpt'] ) ) {
			// An explicit excerpt keeps listings to the public dek, never the gated body.
			$postarr['post_excerpt'] = $item['excerpt'];
		}

		$categories = array_filter( array_map( 'redeemid_demo_term_id', $item['categories'] ?? ( isset( $item['category'] ) ? [ $item['category'] ] : [] ) ) );

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
	redeemid_demo_seed_access_rules( $restore && null === $only );
	redeemid_demo_seed_features( $restore && null === $only );

	return $results;
}

/**
 * Make the news home the front page on first activation, if the site still
 * lists latest posts or has no front page.
 */
function redeemid_demo_set_front_page(): void {
	$home = redeemid_demo_find( 'home' );

	if ( ! $home ) {
		return;
	}

	$front = (int) get_option( 'page_on_front' );

	if ( 'posts' === get_option( 'show_on_front' ) || ! $front ) {
		update_option( 'page_on_front', $home->ID );
		update_option( 'show_on_front', 'page' );
	}
}

/**
 * A seeded item: by its seed key first, then by slug for a page someone created by hand.
 */
function redeemid_demo_find( string $key ): ?WP_Post {
	$item  = redeemid_demo_seed_items()[ $key ] ?? null;
	$found = get_posts(
		[
			'post_type'      => $item['type'] ?? 'any',
			'post_status'    => 'any',
			'meta_key'       => REDEEMID_DEMO_SEED_META, // phpcs:ignore WordPress.DB.SlowDBQuery
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
function redeemid_demo_pattern_content( string $pattern ): string {
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
function redeemid_demo_callback_slug(): string {
	return class_exists( \RedeemID\Config::class ) ? \RedeemID\Config::callback_slug() : 'auth-callback';
}

function redeemid_demo_author(): int {
	if ( current_user_can( 'manage_options' ) ) {
		return get_current_user_id();
	}

	$admins = get_users( [ 'role' => 'administrator', 'number' => 1, 'orderby' => 'ID', 'fields' => 'ID' ] );

	return $admins ? (int) $admins[0] : 0;
}
