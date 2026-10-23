<?php
/**
 * Test accounts: WordPress users seeded in known membership states, for
 * moving between them with the User Switching plugin.
 *
 * Linked fixtures carry a made-up Shopify customer ID and a synced mirror
 * written straight into the plugin's user meta, so the gate decides on them
 * exactly as it would for a real customer. They are not real Shopify
 * customers, so the theme:
 *
 * - tells the plugin they are fixtures (wp2_shopify_customer_is_fixture), so
 *   its reconciler skips them and its Customers screen labels them;
 * - re-applies their state when someone switches to them, and when the
 *   front-end resync runs for them, instead of asking Shopify.
 *
 * An administrator can point a fixture at a real customer in the dev store
 * (Shopify → Test lab → Test accounts). It is then an ordinary linked user:
 * the plugin syncs it from Shopify and resync goes to Shopify too.
 *
 * @package sfwp-test
 */

defined( 'ABSPATH' ) || exit;

const SFWP_TEST_FIXTURE_META  = '_sfwp_test_fixture';
const SFWP_TEST_FIXTURE_LINKS = 'sfwp_test_fixture_links';

/**
 * @return array<string, array{name: string, linked: bool, tags: string[], state: string, stale: bool, expect: string}>
 */
function sfwp_test_fixtures(): array {
	return [
		'sfwp-reader'  => [
			'name'   => 'Riley Reader',
			'linked' => false,
			'tags'   => [],
			'state'  => 'enabled',
			'stale'  => false,
			'expect' => __( 'Signed in to WordPress, not linked to Shopify. Free and Members stories open; tag gates stay locked (not linked).', 'sfwp-test' ),
		],
		'sfwp-member'  => [
			'name'   => 'Milo Member',
			'linked' => true,
			'tags'   => [],
			'state'  => 'enabled',
			'stale'  => false,
			'expect' => __( 'Linked, no tags. Free and Members stories open; Silver, Gold and Gold + Silver stay locked.', 'sfwp-test' ),
		],
		'sfwp-silver'  => [
			'name'   => 'Sasha Silver',
			'linked' => true,
			'tags'   => [ 'silver-member' ],
			'state'  => 'enabled',
			'stale'  => false,
			'expect' => __( 'silver-member. Silver stories open; Gold and Gold + Silver stay locked.', 'sfwp-test' ),
		],
		'sfwp-gold'    => [
			'name'   => 'Gia Gold',
			'linked' => true,
			'tags'   => [ 'gold-member' ],
			'state'  => 'enabled',
			'stale'  => false,
			'expect' => __( 'gold-member. Gold stories and the members-only article open; Silver and Gold + Silver stay locked.', 'sfwp-test' ),
		],
		'sfwp-premium' => [
			'name'   => 'Paz Premium',
			'linked' => true,
			'tags'   => [ 'gold-member', 'silver-member' ],
			'state'  => 'enabled',
			'stale'  => false,
			'expect' => __( 'gold-member and silver-member. Everything opens except the negative control.', 'sfwp-test' ),
		],
		'sfwp-lapsed'  => [
			'name'   => 'Lou Lapsed',
			'linked' => true,
			'tags'   => [ 'gold-member' ],
			'state'  => 'disabled',
			'stale'  => false,
			'expect' => __( 'gold-member, but the Shopify account is disabled. Every tag gate stays locked.', 'sfwp-test' ),
		],
		'sfwp-stale'   => [
			'name'   => 'Stella Stale',
			'linked' => true,
			'tags'   => [ 'gold-member' ],
			'state'  => 'enabled',
			'stale'  => true,
			'expect' => __( 'gold-member, last synced 30 days ago (past the freshness window). Tag gates stay locked until a real sync.', 'sfwp-test' ),
		],
	];
}

/**
 * Made-up customer IDs, far outside the range a dev store hands out.
 */
function sfwp_test_fixture_customer_id( string $login ): string {
	$index = array_search( $login, array_keys( sfwp_test_fixtures() ), true );

	return (string) ( 990000000001 + (int) $index );
}

/**
 * @return array<string, string> Fixture login => real Shopify customer ID.
 */
function sfwp_test_fixture_links(): array {
	$links = get_option( SFWP_TEST_FIXTURE_LINKS, [] );

	return is_array( $links ) ? array_filter( array_map( 'strval', $links ), 'ctype_digit' ) : [];
}

function sfwp_test_fixture_login( int $user_id ): string {
	$login = (string) get_user_meta( $user_id, SFWP_TEST_FIXTURE_META, true );

	return isset( sfwp_test_fixtures()[ $login ] ) ? $login : '';
}

/**
 * A fixture that is simulated, not pointed at a real Shopify customer.
 */
function sfwp_test_is_simulated( int $user_id ): bool {
	$login = sfwp_test_fixture_login( $user_id );

	return '' !== $login && ! isset( sfwp_test_fixture_links()[ $login ] );
}

function sfwp_test_fixture_user( string $login ): ?WP_User {
	$user = get_user_by( 'login', $login );

	return $user instanceof WP_User ? $user : null;
}

/**
 * Create missing fixture users and put every simulated one in its state.
 */
function sfwp_test_seed_fixtures(): void {
	foreach ( sfwp_test_fixtures() as $login => $fixture ) {
		$user = sfwp_test_fixture_user( $login );

		if ( ! $user ) {
			[ $first, $last ] = explode( ' ', $fixture['name'], 2 );

			$id = wp_insert_user(
				[
					'user_login'   => $login,
					'user_pass'    => wp_generate_password( 32 ),
					'user_email'   => $login . '@example.com',
					'display_name' => $fixture['name'],
					'first_name'   => $first,
					'last_name'    => $last,
					'role'         => 'subscriber',
					'description'  => __( 'Test account seeded by the Shopify for WP Test theme. Switch to it with User Switching.', 'sfwp-test' ),
				]
			);

			if ( is_wp_error( $id ) ) {
				continue;
			}

			$user = get_user_by( 'id', $id );
		}

		update_user_meta( $user->ID, SFWP_TEST_FIXTURE_META, $login );
		sfwp_test_apply_fixture( $user->ID );
	}
}

/**
 * Write a fixture's mirror into the plugin's user meta. A fixture pointed
 * at a real customer gets the real ID and is synced from Shopify instead.
 */
function sfwp_test_apply_fixture( int $user_id ): void {
	$login = sfwp_test_fixture_login( $user_id );

	if ( '' === $login || ! class_exists( \WP2_Shopify\Sync\Mirror::class ) ) {
		return;
	}

	$mirror  = \WP2_Shopify\Sync\Mirror::class;
	$fixture = sfwp_test_fixtures()[ $login ];
	$real    = sfwp_test_fixture_links()[ $login ] ?? '';

	if ( '' !== $real ) {
		$id = \WP2_Shopify\Support\CustomerId::parse( $real );

		if ( $id && (string) get_user_meta( $user_id, $mirror::NUMERIC, true ) !== $id->numeric ) {
			$mirror::unlink( $user_id );
			$mirror::link( $user_id, $id );
		}

		if ( $id && class_exists( \WP2_Shopify\Sync\Reconciler::class ) && \WP2_Shopify\Shopify\Admin::available() ) {
			\WP2_Shopify\Sync\Reconciler::sync_user( $user_id, 'test_fixture' );
		}

		return;
	}

	if ( ! $fixture['linked'] ) {
		$mirror::unlink( $user_id );
		return;
	}

	$mirror::link( $user_id, \WP2_Shopify\Support\CustomerId::parse( sfwp_test_fixture_customer_id( $login ) ) );
	$mirror::set_tags( $user_id, $fixture['tags'] );

	[ $first, $last ] = explode( ' ', $fixture['name'], 2 );
	$mirror::set_profile(
		$user_id,
		[
			'first_name'     => $first,
			'last_name'      => $last,
			'email'          => $login . '@example.com',
			'verified_email' => true,
		]
	);
	$mirror::set_status( $user_id, $fixture['state'] );
	$mirror::stamp( $user_id, 'test_fixture' );

	if ( $fixture['stale'] ) {
		update_user_meta( $user_id, $mirror::LAST_SYNC, time() - 30 * DAY_IN_SECONDS );
	}
}

// The plugin skips fixtures in reconciliation and labels them on its Customers screen.
add_filter(
	'wp2_shopify_customer_is_fixture',
	static fn( $is_fixture, $user_id ) => $is_fixture || sfwp_test_is_simulated( (int) $user_id ),
	10,
	2
);

add_filter(
	'wp2_shopify_customer_badges',
	static function ( $badges, $user_id ) {
		$login = sfwp_test_fixture_login( (int) $user_id );

		if ( '' !== $login ) {
			$badges   = (array) $badges;
			$badges[] = sfwp_test_is_simulated( (int) $user_id ) ? __( 'Test fixture', 'sfwp-test' ) : __( 'Test account (real customer)', 'sfwp-test' );
		}

		return $badges;
	},
	10,
	2
);

// User Switching: re-apply a fixture's state whenever someone switches to it.
add_action( 'switch_to_user', static fn( $user_id ) => sfwp_test_apply_fixture( (int) $user_id ), 10, 1 );

/**
 * The front-end resync for a simulated fixture: re-apply its seeded state
 * and answer like the plugin's POST /account/sync would, without calling
 * Shopify for a customer that does not exist.
 */
add_filter(
	'rest_pre_dispatch',
	static function ( $result, $server, $request ) {
		if ( null !== $result || 'POST' !== $request->get_method() || '/wp2-shopify/v1/account/sync' !== $request->get_route() ) {
			return $result;
		}

		$user_id = get_current_user_id();

		if ( ! $user_id || ! sfwp_test_is_simulated( $user_id ) ) {
			return $result;
		}

		sfwp_test_apply_fixture( $user_id );

		$response = new WP_REST_Response( [ 'result' => 'synced', 'fixture' => true ] );
		$response->header( 'Cache-Control', 'no-store, private' );

		return $response;
	},
	10,
	3
);

/**
 * A User Switching link to a fixture, landing on $redirect; '' when the
 * plugin is missing or the current user may not switch.
 */
function sfwp_test_switch_url( WP_User $user, string $redirect = '' ): string {
	if ( ! class_exists( 'user_switching' ) || ! method_exists( 'user_switching', 'maybe_switch_url' ) ) {
		return '';
	}

	$url = user_switching::maybe_switch_url( $user );

	return $url ? add_query_arg( 'redirect_to', rawurlencode( $redirect ?: home_url( '/' ) ), $url ) : '';
}

/**
 * The account a switched-in session came from, if any.
 */
function sfwp_test_switched_from(): ?WP_User {
	if ( ! function_exists( 'current_user_switched' ) ) {
		return null;
	}

	$old = current_user_switched();

	return $old instanceof WP_User ? $old : null;
}

/**
 * Admin bar → Test accounts: switch to any fixture (administrators), or back
 * to the original account (while switched into a fixture).
 */
add_action(
	'admin_bar_menu',
	static function ( WP_Admin_Bar $bar ): void {
		$old   = sfwp_test_switched_from();
		$admin = current_user_can( 'manage_options' );

		if ( ! $admin && ! $old ) {
			return;
		}

		$current = sfwp_test_fixture_login( get_current_user_id() );
		$here    = is_admin() ? home_url( '/' ) : home_url( add_query_arg( [] ) );

		$bar->add_node(
			[
				'id'    => 'sfwp-test-accounts',
				'title' => '' !== $current
					/* translators: %s: test account name */
					? sprintf( __( 'Testing as %s', 'sfwp-test' ), sfwp_test_fixtures()[ $current ]['name'] )
					: __( 'Test accounts', 'sfwp-test' ),
				'href'  => $admin ? sfwp_test_admin_url( [], 'accounts' ) : false,
			]
		);

		if ( $old && method_exists( 'user_switching', 'switch_back_url' ) ) {
			$bar->add_node(
				[
					'parent' => 'sfwp-test-accounts',
					'id'     => 'sfwp-test-switch-back',
					/* translators: %s: original user name */
					'title'  => sprintf( __( 'Switch back to %s', 'sfwp-test' ), $old->display_name ),
					'href'   => add_query_arg( 'redirect_to', rawurlencode( $here ), user_switching::switch_back_url( $old ) ),
				]
			);
		}

		if ( ! $admin ) {
			return;
		}

		foreach ( sfwp_test_fixtures() as $login => $fixture ) {
			$user = sfwp_test_fixture_user( $login );
			$url  = $user ? sfwp_test_switch_url( $user, $here ) : '';

			$bar->add_node(
				[
					'parent' => 'sfwp-test-accounts',
					'id'     => 'sfwp-test-' . $login,
					'title'  => esc_html( $fixture['name'] . ' · ' . sfwp_test_fixture_summary( $login ) ),
					'href'   => $url ?: false,
				]
			);
		}

		if ( ! class_exists( 'user_switching' ) ) {
			$bar->add_node(
				[
					'parent' => 'sfwp-test-accounts',
					'id'     => 'sfwp-test-install-switching',
					'title'  => __( 'Install User Switching to switch accounts', 'sfwp-test' ),
					'href'   => admin_url( 'plugin-install.php?s=user-switching&tab=search&type=term' ),
				]
			);
		}
	},
	90
);

/**
 * Short state label, e.g. "gold-member, disabled".
 */
function sfwp_test_fixture_summary( string $login ): string {
	$fixture = sfwp_test_fixtures()[ $login ];

	if ( isset( sfwp_test_fixture_links()[ $login ] ) ) {
		/* translators: %s: Shopify customer ID */
		return sprintf( __( 'real customer %s', 'sfwp-test' ), sfwp_test_fixture_links()[ $login ] );
	}

	if ( ! $fixture['linked'] ) {
		return __( 'not linked', 'sfwp-test' );
	}

	$parts = [ $fixture['tags'] ? implode( ' + ', $fixture['tags'] ) : __( 'no tags', 'sfwp-test' ) ];

	if ( 'disabled' === $fixture['state'] ) {
		$parts[] = __( 'disabled', 'sfwp-test' );
	}

	if ( $fixture['stale'] ) {
		$parts[] = __( 'stale', 'sfwp-test' );
	}

	return implode( ', ', $parts );
}
