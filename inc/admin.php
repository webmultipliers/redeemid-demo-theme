<?php
/**
 * RedeemID → Test lab: lists the seeded test content and test accounts, and
 * restores them from the theme after a deploy changes them.
 *
 * The page sits under the plugin's top-level Shopify menu. Without the
 * plugin (or with one too old to have that menu), it falls back to
 * Appearance → Test lab.
 *
 * @package redeemid-demo
 */

defined( 'ABSPATH' ) || exit;

const REDEEMID_DEMO_ADMIN_PAGE = 'redeemid-demo-lab';

add_action(
	'admin_menu',
	static function (): void {
		global $admin_page_hooks;

		$parent = defined( 'REDEEMID_ADMIN_MENU' ) ? (string) REDEEMID_ADMIN_MENU : 'redeemid';

		if ( isset( $admin_page_hooks[ $parent ] ) ) {
			add_submenu_page( $parent, __( 'Test lab', 'redeemid-demo' ), __( 'Test lab', 'redeemid-demo' ), 'manage_options', REDEEMID_DEMO_ADMIN_PAGE, 'redeemid_demo_admin_render' );
		} else {
			add_theme_page( __( 'Test lab', 'redeemid-demo' ), __( 'Test lab', 'redeemid-demo' ), 'manage_options', REDEEMID_DEMO_ADMIN_PAGE, 'redeemid_demo_admin_render' );
		}
	},
	20 // After the plugin registers its menu.
);

/**
 * The Test lab screen, wherever it is in the menu.
 */
function redeemid_demo_admin_url( array $args = [], string $anchor = '' ): string {
	return add_query_arg( [ 'page' => REDEEMID_DEMO_ADMIN_PAGE ] + $args, admin_url( 'admin.php' ) ) . ( $anchor ? '#' . $anchor : '' );
}

add_action( 'admin_post_redeemid_demo_restore', 'redeemid_demo_admin_restore' );
add_action( 'admin_post_redeemid_demo_accounts', 'redeemid_demo_admin_accounts' );

/**
 * Reset the test accounts, or point them at real Shopify customers.
 */
function redeemid_demo_admin_accounts(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do that.', 'redeemid-demo' ), '', [ 'response' => 403 ] );
	}

	check_admin_referer( 'redeemid_demo_accounts' );

	$links = [];
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- each value is sanitized and parsed below.
	$posted = isset( $_POST['customer'] ) && is_array( $_POST['customer'] ) ? wp_unslash( $_POST['customer'] ) : [];

	foreach ( array_keys( redeemid_demo_fixtures() ) as $login ) {
		$value = class_exists( \RedeemID\Support\CustomerId::class )
			? \RedeemID\Support\CustomerId::parse( sanitize_text_field( (string) ( $posted[ $login ] ?? '' ) ) )
			: null;

		if ( $value ) {
			$links[ $login ] = $value->numeric;
		}
	}

	update_option( REDEEMID_DEMO_FIXTURE_LINKS, $links, false );
	redeemid_demo_seed_fixtures();

	wp_safe_redirect( redeemid_demo_admin_url( [ 'accounts' => 'reset' ], 'accounts' ) );
	exit;
}

function redeemid_demo_admin_restore(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do that.', 'redeemid-demo' ), '', [ 'response' => 403 ] );
	}

	check_admin_referer( 'redeemid_demo_restore' );

	$results = redeemid_demo_seed( true );
	redeemid_demo_seed_fixtures();
	redeemid_demo_set_front_page();
	update_option( REDEEMID_DEMO_CONTENT_OPTION, REDEEMID_DEMO_CONTENT_VERSION );

	$counts = array_count_values( $results );
	wp_safe_redirect(
		redeemid_demo_admin_url(
			[
				'restored' => (int) ( $counts['restored'] ?? 0 ) + (int) ( $counts['created'] ?? 0 ),
				'failed'   => (int) ( $counts['failed'] ?? 0 ),
			]
		)
	);
	exit;
}

function redeemid_demo_admin_render(): void {
	// phpcs:disable WordPress.Security.NonceVerification -- display only.
	$restored = isset( $_GET['restored'] ) ? absint( $_GET['restored'] ) : null;
	$failed   = isset( $_GET['failed'] ) ? absint( $_GET['failed'] ) : 0;
	$accounts = isset( $_GET['accounts'] );
	// phpcs:enable
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Test lab', 'redeemid-demo' ); ?></h1>

		<?php if ( null !== $restored ) : ?>
			<div class="notice <?php echo $failed ? 'notice-warning' : 'notice-success'; ?> is-dismissible">
				<p>
					<?php
					/* translators: 1: number restored, 2: number failed */
					echo esc_html( sprintf( __( 'Restored %1$d items from the theme. %2$d failed.', 'redeemid-demo' ), $restored, $failed ) );
					?>
				</p>
			</div>
		<?php endif; ?>

		<?php if ( $accounts ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Test accounts reset.', 'redeemid-demo' ); ?></p></div>
		<?php endif; ?>

		<?php redeemid_demo_admin_render_accounts(); ?>

		<h2><?php esc_html_e( 'Test content', 'redeemid-demo' ); ?></h2>
		<p><?php esc_html_e( 'The theme creates these pages and posts on the first request after it is activated. Missing items are created again whenever the theme’s content version goes up; existing ones are left alone.', 'redeemid-demo' ); ?></p>

		<table class="widefat striped" style="max-width:56rem">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Item', 'redeemid-demo' ); ?></th>
					<th><?php esc_html_e( 'Address', 'redeemid-demo' ); ?></th>
					<th><?php esc_html_e( 'Status', 'redeemid-demo' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( redeemid_demo_seed_items() as $key => $item ) : ?>
					<?php $post = redeemid_demo_find( $key ); ?>
					<tr>
						<td>
							<?php if ( $post ) : ?>
								<a href="<?php echo esc_url( (string) get_edit_post_link( $post ) ); ?>"><?php echo esc_html( $item['title'] ); ?></a>
							<?php else : ?>
								<?php echo esc_html( $item['title'] ); ?>
							<?php endif; ?>
						</td>
						<td>
							<?php if ( $post ) : ?>
								<a href="<?php echo esc_url( (string) get_permalink( $post ) ); ?>"><?php echo esc_html( wp_make_link_relative( (string) get_permalink( $post ) ) ); ?></a>
							<?php else : ?>
								—
							<?php endif; ?>
						</td>
						<td><?php echo esc_html( $post ? $post->post_status : __( 'missing', 'redeemid-demo' ) ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<h2><?php esc_html_e( 'Restore from the theme', 'redeemid-demo' ); ?></h2>
		<p><?php esc_html_e( 'Rewrites every item above with the theme’s current version, creates any that are missing, and resets the test accounts. Edits made to these items in WordPress are lost.', 'redeemid-demo' ); ?></p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="redeemid_demo_restore">
			<?php wp_nonce_field( 'redeemid_demo_restore' ); ?>
			<?php submit_button( __( 'Restore test content', 'redeemid-demo' ), 'secondary', 'submit', false ); ?>
		</form>

		<h2><?php esc_html_e( 'Sandbox health', 'redeemid-demo' ); ?></h2>
		<?php if ( class_exists( \RedeemID\Support\Health::class ) ) : ?>
			<p><?php esc_html_e( 'From the plugin’s health checklist, with the theme’s own rows at the end. RedeemID → Debug has the full diagnostics.', 'redeemid-demo' ); ?></p>
			<ul>
				<?php foreach ( \RedeemID\Support\Health::checks() as $check ) : ?>
					<li><strong><?php echo esc_html( $check['label'] ); ?>:</strong> <?php echo esc_html( $check['detail'] ?: $check['state'] ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<p><?php esc_html_e( 'The RedeemID plugin is not active, or is too old to have a health checklist.', 'redeemid-demo' ); ?></p>
		<?php endif; ?>
	</div>
	<?php
}

function redeemid_demo_admin_render_accounts(): void {
	$switching = class_exists( 'user_switching' );
	$links     = redeemid_demo_fixture_links();
	?>
	<h2 id="accounts"><?php esc_html_e( 'Test accounts', 'redeemid-demo' ); ?></h2>
	<p><?php esc_html_e( 'WordPress users in known membership states. Switch to one, browse the site, then use “Switch back” in the toolbar. Simulated accounts have a made-up Shopify customer ID; their state is put back every time you switch to them or press Refresh membership. Enter a real customer ID from the dev store to test that account against Shopify instead.', 'redeemid-demo' ); ?></p>

	<?php if ( ! $switching ) : ?>
		<div class="notice notice-warning inline"><p>
			<?php esc_html_e( 'User Switching is not active, so the Switch links are missing.', 'redeemid-demo' ); ?>
			<a href="<?php echo esc_url( admin_url( 'plugin-install.php?s=user-switching&tab=search&type=term' ) ); ?>"><?php esc_html_e( 'Install User Switching', 'redeemid-demo' ); ?></a>
		</p></div>
	<?php endif; ?>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="redeemid_demo_accounts">
		<?php wp_nonce_field( 'redeemid_demo_accounts' ); ?>
		<table class="widefat striped" style="max-width:72rem">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Account', 'redeemid-demo' ); ?></th>
					<th><?php esc_html_e( 'State', 'redeemid-demo' ); ?></th>
					<th><?php esc_html_e( 'What should happen', 'redeemid-demo' ); ?></th>
					<th><?php esc_html_e( 'Real Shopify customer ID', 'redeemid-demo' ); ?></th>
					<th><?php esc_html_e( 'Switch', 'redeemid-demo' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( redeemid_demo_fixtures() as $login => $fixture ) : ?>
					<?php $user = redeemid_demo_fixture_user( $login ); ?>
					<tr>
						<td>
							<strong><?php echo esc_html( $fixture['name'] ); ?></strong><br>
							<code><?php echo esc_html( $login ); ?></code>
						</td>
						<td><?php echo esc_html( redeemid_demo_fixture_summary( $login ) ); ?></td>
						<td><?php echo esc_html( $fixture['expect'] ); ?></td>
						<td>
							<?php if ( $fixture['linked'] ) : ?>
								<input type="text" inputmode="numeric" class="regular-text" style="width:12rem" name="customer[<?php echo esc_attr( $login ); ?>]" value="<?php echo esc_attr( $links[ $login ] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'simulated', 'redeemid-demo' ); ?>">
							<?php else : ?>
								—
							<?php endif; ?>
						</td>
						<td>
							<?php $url = $user ? redeemid_demo_switch_url( $user, home_url( '/' ) ) : ''; ?>
							<?php if ( $url ) : ?>
								<a class="button button-small" href="<?php echo esc_url( $url ); ?>"><?php esc_html_e( 'Switch to', 'redeemid-demo' ); ?></a>
							<?php elseif ( ! $user ) : ?>
								<?php esc_html_e( 'missing', 'redeemid-demo' ); ?>
							<?php else : ?>
								—
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<p><?php submit_button( __( 'Save and reset test accounts', 'redeemid-demo' ), 'secondary', 'submit', false ); ?></p>
	</form>
	<?php
}
