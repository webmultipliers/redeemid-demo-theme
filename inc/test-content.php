<?php
/**
 * Seeds the Test lab: the pages and post the sandbox tests run against.
 *
 * Content comes from the theme's patterns/ files, so a deploy that changes a
 * pattern can be applied with Appearance → Test lab → Restore. Seeding only
 * creates what is missing; it never overwrites a page someone edited unless
 * an administrator asks for a restore.
 *
 * @package sfwp-test
 */

defined( 'ABSPATH' ) || exit;

/** Bump when a new seeded item is added, so existing sites pick it up on the next request. */
const SFWP_TEST_CONTENT_VERSION = 1;
const SFWP_TEST_CONTENT_OPTION  = 'sfwp_test_content_version';
const SFWP_TEST_SEED_META       = '_sfwp_test_seed';

/**
 * @return array<string, array{type: string, title: string, slug: string, pattern: string}>
 */
function sfwp_test_seed_items(): array {
	return [
		'test-lab'             => [ 'type' => 'page', 'title' => __( 'Test lab', 'sfwp-test' ), 'slug' => 'test-lab', 'pattern' => 'page-test-lab' ],
		'auth-callback'        => [ 'type' => 'page', 'title' => __( 'Signing you in', 'sfwp-test' ), 'slug' => sfwp_test_callback_slug(), 'pattern' => 'page-auth-callback' ],
		'sign-in-controls'     => [ 'type' => 'page', 'title' => __( 'Sign-in controls', 'sfwp-test' ), 'slug' => 'sign-in-controls', 'pattern' => 'page-sign-in-controls' ],
		'members-area'         => [ 'type' => 'page', 'title' => __( 'Members area', 'sfwp-test' ), 'slug' => 'members-area', 'pattern' => 'page-members-area' ],
		'tag-gates'            => [ 'type' => 'page', 'title' => __( 'Tag gates', 'sfwp-test' ), 'slug' => 'tag-gates', 'pattern' => 'page-tag-gates' ],
		'gate-redirect'        => [ 'type' => 'page', 'title' => __( 'Redirect gate', 'sfwp-test' ), 'slug' => 'gate-redirect', 'pattern' => 'page-gate-redirect' ],
		'join'                 => [ 'type' => 'page', 'title' => __( 'Become a member', 'sfwp-test' ), 'slug' => 'join', 'pattern' => 'page-join' ],
		'account'              => [ 'type' => 'page', 'title' => __( 'Account', 'sfwp-test' ), 'slug' => 'account', 'pattern' => 'page-account' ],
		'members-only-article' => [ 'type' => 'post', 'title' => __( 'Members-only article', 'sfwp-test' ), 'slug' => 'members-only-article', 'pattern' => 'post-members-only-article' ],
	];
}

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

	$first_run = 0 === (int) get_option( SFWP_TEST_CONTENT_OPTION, 0 );

	sfwp_test_seed( false );

	if ( $first_run && 'posts' === get_option( 'show_on_front' ) ) {
		$lab = sfwp_test_find( 'test-lab' );

		if ( $lab ) {
			update_option( 'page_on_front', $lab->ID );
			update_option( 'show_on_front', 'page' );
		}
	}

	update_option( SFWP_TEST_CONTENT_OPTION, SFWP_TEST_CONTENT_VERSION );
	delete_option( 'sfwp_test_seeding' );
}
add_action( 'init', 'sfwp_test_maybe_seed', 20 );

/**
 * Create missing items, and with $restore also rewrite existing seeded items
 * from their patterns.
 *
 * @return array<string, string> Item key => created|restored|kept|failed.
 */
function sfwp_test_seed( bool $restore ): array {
	$results = [];
	$author  = sfwp_test_author();

	foreach ( sfwp_test_seed_items() as $key => $item ) {
		$content  = sfwp_test_pattern_content( $item['pattern'] );
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
			'meta_input'   => [ SFWP_TEST_SEED_META => $key ],
		];

		if ( $existing ) {
			$postarr['ID'] = $existing->ID;
		}

		// Block markup must be stored as is; kses would strip the gate's comment attributes for low-privilege authors.
		kses_remove_filters();
		$id = wp_insert_post( wp_slash( $postarr ), true );
		kses_init();

		$results[ $key ] = is_wp_error( $id ) ? 'failed' : ( $existing ? 'restored' : 'created' );
	}

	return $results;
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

function sfwp_test_author(): int {
	if ( current_user_can( 'manage_options' ) ) {
		return get_current_user_id();
	}

	$admins = get_users( [ 'role' => 'administrator', 'number' => 1, 'orderby' => 'ID', 'fields' => 'ID' ] );

	return $admins ? (int) $admins[0] : 0;
}
