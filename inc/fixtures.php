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
 * - tells the plugin they are fixtures (redeemid_customer_is_fixture), so
 *   its resync skips them and its Customers screen labels them;
 * - re-applies their state when someone switches to them, and when the
 *   plugin's membership refresh runs for them (redeemid_pre_sync_now),
 *   instead of asking Shopify.
 *
 * An administrator can point a fixture at a real customer in the dev store
 * (RedeemID → Test lab → Test accounts). It is then an ordinary linked user:
 * the plugin syncs it from Shopify and resync goes to Shopify too.
 *
 * @package redeemid-demo
 */

defined( 'ABSPATH' ) || exit;

const REDEEMID_DEMO_FIXTURE_META  = '_redeemid_demo_fixture';
const REDEEMID_DEMO_FIXTURE_LINKS = 'redeemid_demo_fixture_links';

/**
 * @return array<string, array{name: string, linked: bool, tags: string[], state: string, stale: bool, expect: string}>
 */
function redeemid_demo_fixtures(): array {
	return [
		'ledger-reader'  => [
			'name'   => 'Riley Reader',
			'linked' => false,
			'tags'   => [],
			'state'  => 'enabled',
			'stale'  => false,
			'expect' => __( 'Signed in to WordPress, not linked to Shopify. Free and Members stories open; tag gates stay locked (not linked).', 'redeemid-demo' ),
		],
		'ledger-member'  => [
			'name'   => 'Milo Member',
			'linked' => true,
			'tags'   => [],
			'state'  => 'enabled',
			'stale'  => false,
			'expect' => __( 'Linked, no tags. Free and Members stories open; Silver, Gold and Gold + Silver stay locked.', 'redeemid-demo' ),
		],
		'ledger-silver'  => [
			'name'   => 'Sasha Silver',
			'linked' => true,
			'tags'   => [ 'silver-member' ],
			'state'  => 'enabled',
			'stale'  => false,
			'expect' => __( 'silver-member. Silver stories open; Gold and Gold + Silver stay locked.', 'redeemid-demo' ),
		],
		'ledger-gold'    => [
			'name'   => 'Gia Gold',
			'linked' => true,
			'tags'   => [ 'gold-member' ],
			'state'  => 'enabled',
			'stale'  => false,
			'expect' => __( 'gold-member. Gold stories and the members-only article open; Silver and Gold + Silver stay locked.', 'redeemid-demo' ),
		],
		'ledger-premium' => [
			'name'   => 'Paz Premium',
			'linked' => true,
			'tags'   => [ 'gold-member', 'silver-member' ],
			'state'  => 'enabled',
			'stale'  => false,
			'expect' => __( 'gold-member and silver-member. Everything opens except the negative control.', 'redeemid-demo' ),
		],
		'ledger-lapsed'  => [
			'name'   => 'Lou Lapsed',
			'linked' => true,
			'tags'   => [ 'gold-member' ],
			'state'  => 'disabled',
			'stale'  => false,
			'expect' => __( 'gold-member, but the Shopify account is disabled. Every tag gate stays locked.', 'redeemid-demo' ),
		],
		'ledger-stale'   => [
			'name'   => 'Stella Stale',
			'linked' => true,
			'tags'   => [ 'gold-member' ],
			'state'  => 'enabled',
			'stale'  => true,
			'expect' => __( 'gold-member, last synced 30 days ago. Synced tags never expire, so Gold still opens.', 'redeemid-demo' ),
		],
		'ledger-addon'   => [
			'name'   => 'Ada Add-on',
			'linked' => true,
			'tags'   => [ 'silver-member', 'ad-free-addon' ],
			'state'  => 'enabled',
			'stale'  => false,
			'expect' => __( 'silver-member plus the ad-free-addon tag. Silver stories open; the add-on gives Ad-free reading without Gold.', 'redeemid-demo' ),
		],
	];
}

/**
 * Made-up customer IDs, far outside the range a dev store hands out.
 */
function redeemid_demo_fixture_customer_id( string $login ): string {
	$index = array_search( $login, array_keys( redeemid_demo_fixtures() ), true );

	return (string) ( 990000000001 + (int) $index );
}

/**
 * @return array<string, string> Fixture login => real Shopify customer ID.
 */
function redeemid_demo_fixture_links(): array {
	$links = get_option( REDEEMID_DEMO_FIXTURE_LINKS, [] );

	return is_array( $links ) ? array_filter( array_map( 'strval', $links ), 'ctype_digit' ) : [];
}

function redeemid_demo_fixture_login( int $user_id ): string {
	$login = (string) get_user_meta( $user_id, REDEEMID_DEMO_FIXTURE_META, true );

	return isset( redeemid_demo_fixtures()[ $login ] ) ? $login : '';
}

/**
 * A fixture that is simulated, not pointed at a real Shopify customer.
 */
function redeemid_demo_is_simulated( int $user_id ): bool {
	$login = redeemid_demo_fixture_login( $user_id );

	return '' !== $login && ! isset( redeemid_demo_fixture_links()[ $login ] );
}

function redeemid_demo_fixture_user( string $login ): ?WP_User {
	$user = get_user_by( 'login', $login );

	return $user instanceof WP_User ? $user : null;
}

/**
 * Create missing fixture users and put every simulated one in its state.
 */
function redeemid_demo_seed_fixtures(): void {
	foreach ( redeemid_demo_fixtures() as $login => $fixture ) {
		$user = redeemid_demo_fixture_user( $login );

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
					'description'  => __( 'Test account seeded by the RedeemID Demo theme. Switch to it with User Switching.', 'redeemid-demo' ),
				]
			);

			if ( is_wp_error( $id ) ) {
				continue;
			}

			$user = get_user_by( 'id', $id );
		}

		update_user_meta( $user->ID, REDEEMID_DEMO_FIXTURE_META, $login );
		redeemid_demo_apply_fixture( $user->ID );
	}
}

/**
 * Write a fixture's mirror into the plugin's user meta. A fixture pointed
 * at a real customer gets the real ID and is synced from Shopify instead.
 */
function redeemid_demo_apply_fixture( int $user_id ): void {
	$login = redeemid_demo_fixture_login( $user_id );

	if ( '' === $login || ! class_exists( \RedeemID\Sync\Mirror::class ) ) {
		return;
	}

	$mirror  = \RedeemID\Sync\Mirror::class;
	$fixture = redeemid_demo_fixtures()[ $login ];
	$real    = redeemid_demo_fixture_links()[ $login ] ?? '';

	if ( '' !== $real ) {
		$id = \RedeemID\Support\CustomerId::parse( $real );

		if ( $id && (string) get_user_meta( $user_id, $mirror::NUMERIC, true ) !== $id->numeric ) {
			$mirror::unlink( $user_id );
			$mirror::link( $user_id, $id );
		}

		if ( $id && class_exists( \RedeemID\Sync\Reconciler::class ) && \RedeemID\Shopify\Admin::available() ) {
			\RedeemID\Sync\Reconciler::sync_user( $user_id, 'test_fixture' );
		}

		return;
	}

	if ( ! $fixture['linked'] ) {
		$mirror::unlink( $user_id );
		return;
	}

	$mirror::link( $user_id, \RedeemID\Support\CustomerId::parse( redeemid_demo_fixture_customer_id( $login ) ) );
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
	'redeemid_customer_is_fixture',
	static fn( $is_fixture, $user_id ) => $is_fixture || redeemid_demo_is_simulated( (int) $user_id ),
	10,
	2
);

add_filter(
	'redeemid_customer_badges',
	static function ( $badges, $user_id ) {
		$login = redeemid_demo_fixture_login( (int) $user_id );

		if ( '' !== $login ) {
			$badges   = (array) $badges;
			$badges[] = redeemid_demo_is_simulated( (int) $user_id ) ? __( 'Test fixture', 'redeemid-demo' ) : __( 'Test account (real customer)', 'redeemid-demo' );
		}

		return $badges;
	},
	10,
	2
);

// User Switching: re-apply a fixture's state whenever someone switches to it.
add_action( 'switch_to_user', static fn( $user_id ) => redeemid_demo_apply_fixture( (int) $user_id ), 10, 1 );

/**
 * "Refresh membership" for a simulated fixture: re-apply its seeded state
 * instead of asking Shopify about a customer that does not exist. The plugin
 * asks through redeemid_pre_sync_now before it syncs, from its REST route
 * and its form handler alike.
 */
add_filter(
	'redeemid_pre_sync_now',
	static function ( $result, $user_id ) {
		if ( null !== $result || ! redeemid_demo_is_simulated( (int) $user_id ) ) {
			return $result;
		}

		redeemid_demo_apply_fixture( (int) $user_id );

		return 'synced';
	},
	10,
	2
);

/**
 * A User Switching link to a fixture, landing on $redirect; '' when the
 * plugin is missing or the current user may not switch.
 */
function redeemid_demo_switch_url( WP_User $user, string $redirect = '' ): string {
	if ( ! class_exists( 'user_switching' ) || ! method_exists( 'user_switching', 'maybe_switch_url' ) ) {
		return '';
	}

	$url = user_switching::maybe_switch_url( $user );

	return $url ? add_query_arg( 'redirect_to', rawurlencode( $redirect ?: home_url( '/' ) ), $url ) : '';
}

/**
 * The account a switched-in session came from, if any.
 */
function redeemid_demo_switched_from(): ?WP_User {
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
		$old   = redeemid_demo_switched_from();
		$admin = current_user_can( 'manage_options' );

		if ( ! $admin && ! $old ) {
			return;
		}

		$current = redeemid_demo_fixture_login( get_current_user_id() );
		$here    = is_admin() ? home_url( '/' ) : home_url( add_query_arg( [] ) );

		$bar->add_node(
			[
				'id'    => 'redeemid-demo-accounts',
				'title' => '' !== $current
					/* translators: %s: test account name */
					? esc_html( sprintf( __( 'Testing as %s', 'redeemid-demo' ), redeemid_demo_fixtures()[ $current ]['name'] ) )
					: esc_html__( 'Test accounts', 'redeemid-demo' ),
				'href'  => $admin ? redeemid_demo_admin_url( [], 'accounts' ) : false,
			]
		);

		if ( $old && method_exists( 'user_switching', 'switch_back_url' ) ) {
			$bar->add_node(
				[
					'parent' => 'redeemid-demo-accounts',
					'id'     => 'redeemid-demo-switch-back',
					/* translators: %s: original user name */
					'title'  => esc_html( sprintf( __( 'Switch back to %s', 'redeemid-demo' ), $old->display_name ) ),
					'href'   => add_query_arg( 'redirect_to', rawurlencode( $here ), user_switching::switch_back_url( $old ) ),
				]
			);
		}

		if ( ! $admin ) {
			return;
		}

		foreach ( redeemid_demo_fixtures() as $login => $fixture ) {
			$user = redeemid_demo_fixture_user( $login );
			$url  = $user ? redeemid_demo_switch_url( $user, $here ) : '';

			$bar->add_node(
				[
					'parent' => 'redeemid-demo-accounts',
					'id'     => 'redeemid-demo-' . $login,
					'title'  => esc_html( $fixture['name'] . ' · ' . redeemid_demo_fixture_summary( $login ) ),
					'href'   => $url ?: false,
				]
			);
		}

		if ( ! class_exists( 'user_switching' ) ) {
			$bar->add_node(
				[
					'parent' => 'redeemid-demo-accounts',
					'id'     => 'redeemid-demo-install-switching',
					'title'  => __( 'Install User Switching to switch accounts', 'redeemid-demo' ),
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
function redeemid_demo_fixture_summary( string $login ): string {
	$fixture = redeemid_demo_fixtures()[ $login ];

	if ( isset( redeemid_demo_fixture_links()[ $login ] ) ) {
		/* translators: %s: Shopify customer ID */
		return sprintf( __( 'real customer %s', 'redeemid-demo' ), redeemid_demo_fixture_links()[ $login ] );
	}

	if ( ! $fixture['linked'] ) {
		return __( 'not linked', 'redeemid-demo' );
	}

	$parts = [ $fixture['tags'] ? implode( ' + ', $fixture['tags'] ) : __( 'no tags', 'redeemid-demo' ) ];

	if ( 'disabled' === $fixture['state'] ) {
		$parts[] = __( 'disabled', 'redeemid-demo' );
	}

	if ( $fixture['stale'] ) {
		$parts[] = __( 'stale', 'redeemid-demo' );
	}

	return implode( ', ', $parts );
}
