<?php
/**
 * RedeemID → Demo: the demo's test accounts, its seeded content (with a
 * restore from the theme after a deploy changes it) and sandbox health, one
 * tab each.
 *
 * The page sits under the plugin's top-level RedeemID menu. Without the
 * plugin it falls back to Appearance → Demo. It uses core's own admin markup
 * (nav tabs, widefat tables), so it looks the same in either place.
 *
 * @package redeemid-demo
 */

defined( 'ABSPATH' ) || exit;

const REDEEMID_DEMO_ADMIN_PAGE = 'redeemid-demo';

add_action(
	'admin_menu',
	static function (): void {
		global $admin_page_hooks;

		$parent = defined( 'REDEEMID_ADMIN_MENU' ) ? (string) REDEEMID_ADMIN_MENU : 'redeemid';

		if ( isset( $admin_page_hooks[ $parent ] ) ) {
			add_submenu_page( $parent, __( 'Demo', 'redeemid-demo' ), __( 'Demo', 'redeemid-demo' ), 'manage_options', REDEEMID_DEMO_ADMIN_PAGE, 'redeemid_demo_admin_render' );
		} else {
			add_theme_page( __( 'Demo', 'redeemid-demo' ), __( 'Demo', 'redeemid-demo' ), 'manage_options', REDEEMID_DEMO_ADMIN_PAGE, 'redeemid_demo_admin_render' );
		}
	},
	20 // After the plugin registers its menu.
);

/**
 * The Demo screen's tabs: slug => label. The first is the default and has no
 * tab argument.
 *
 * @return array<string, string>
 */
function redeemid_demo_admin_tabs(): array {
	return [
		'accounts' => __( 'Test accounts', 'redeemid-demo' ),
		'content'  => __( 'Test content', 'redeemid-demo' ),
		'health'   => __( 'Sandbox health', 'redeemid-demo' ),
	];
}

/**
 * The Demo screen, or one of its tabs, wherever it is in the menu.
 */
function redeemid_demo_admin_url( string $tab = '', array $args = [] ): string {
	$tab = array_key_first( redeemid_demo_admin_tabs() ) === $tab ? '' : $tab;

	return add_query_arg( [ 'page' => REDEEMID_DEMO_ADMIN_PAGE ] + ( '' !== $tab ? [ 'tab' => $tab ] : [] ) + $args, admin_url( 'admin.php' ) );
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

	wp_safe_redirect( redeemid_demo_admin_url( 'accounts', [ 'accounts' => 'reset' ] ) );
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
			'content',
			[
				'restored' => (int) ( $counts['restored'] ?? 0 ) + (int) ( $counts['created'] ?? 0 ),
				'failed'   => (int) ( $counts['failed'] ?? 0 ),
			]
		)
	);
	exit;
}

function redeemid_demo_admin_render(): void {
	$tabs = redeemid_demo_admin_tabs();
	// phpcs:disable WordPress.Security.NonceVerification -- display only.
	$tab      = sanitize_key( wp_unslash( $_GET['tab'] ?? '' ) );
	$restored = isset( $_GET['restored'] ) ? absint( $_GET['restored'] ) : null;
	$failed   = isset( $_GET['failed'] ) ? absint( $_GET['failed'] ) : 0;
	$accounts = isset( $_GET['accounts'] );
	// phpcs:enable
	$tab = isset( $tabs[ $tab ] ) ? $tab : (string) array_key_first( $tabs );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Demo', 'redeemid-demo' ); ?></h1>

		<nav class="nav-tab-wrapper" aria-label="<?php esc_attr_e( 'Demo sections', 'redeemid-demo' ); ?>">
			<?php foreach ( $tabs as $slug => $label ) : ?>
				<a href="<?php echo esc_url( redeemid_demo_admin_url( $slug ) ); ?>" class="nav-tab<?php echo $slug === $tab ? ' nav-tab-active' : ''; ?>"<?php echo $slug === $tab ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</nav>

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

		<?php
		if ( 'content' === $tab ) {
			redeemid_demo_admin_render_content();
		} elseif ( 'health' === $tab ) {
			redeemid_demo_admin_render_health();
		} else {
			redeemid_demo_admin_render_accounts();
		}
		?>
	</div>
	<?php
}

/**
 * The Test content tab: every seeded item, and the restore button.
 */
function redeemid_demo_admin_render_content(): void {
	?>
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
	<?php
}

/**
 * The Sandbox health tab: the plugin's health checklist with the theme's rows.
 */
function redeemid_demo_admin_render_health(): void {
	if ( ! class_exists( \RedeemID\Support\Health::class ) ) {
		echo '<p>' . esc_html__( 'The RedeemID plugin is not active, or is too old to have a health checklist.', 'redeemid-demo' ) . '</p>';
		return;
	}
	?>
	<p><?php esc_html_e( 'From the plugin’s health checklist, with the theme’s own rows at the end. RedeemID → Debug has the full diagnostics.', 'redeemid-demo' ); ?></p>
	<table class="widefat striped" style="max-width:56rem">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Check', 'redeemid-demo' ); ?></th>
				<th><?php esc_html_e( 'Result', 'redeemid-demo' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( \RedeemID\Support\Health::checks() as $check ) : ?>
				<tr>
					<td><?php echo esc_html( $check['label'] ); ?></td>
					<td><?php echo esc_html( $check['detail'] ?: $check['state'] ); ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php
}

function redeemid_demo_admin_render_accounts(): void {
	$switching = class_exists( 'user_switching' );
	$links     = redeemid_demo_fixture_links();
	?>
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
