<?php
/**
 * Appearance → Test lab: lists the seeded test content and restores it from
 * the theme's patterns after a deploy changes them.
 *
 * @package sfwp-test
 */

defined( 'ABSPATH' ) || exit;

const SFWP_TEST_ADMIN_PAGE = 'sfwp-test-lab';

add_action(
	'admin_menu',
	static function (): void {
		add_theme_page( __( 'Test lab', 'sfwp-test' ), __( 'Test lab', 'sfwp-test' ), 'manage_options', SFWP_TEST_ADMIN_PAGE, 'sfwp_test_admin_render' );
	}
);

add_action( 'admin_post_sfwp_test_restore', 'sfwp_test_admin_restore' );

function sfwp_test_admin_restore(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do that.', 'sfwp-test' ), '', [ 'response' => 403 ] );
	}

	check_admin_referer( 'sfwp_test_restore' );

	$results = sfwp_test_seed( true );
	update_option( SFWP_TEST_CONTENT_OPTION, SFWP_TEST_CONTENT_VERSION );

	$counts = array_count_values( $results );
	wp_safe_redirect(
		add_query_arg(
			[
				'page'     => SFWP_TEST_ADMIN_PAGE,
				'restored' => (int) ( $counts['restored'] ?? 0 ) + (int) ( $counts['created'] ?? 0 ),
				'failed'   => (int) ( $counts['failed'] ?? 0 ),
			],
			admin_url( 'themes.php' )
		)
	);
	exit;
}

function sfwp_test_admin_render(): void {
	// phpcs:disable WordPress.Security.NonceVerification -- display only.
	$restored = isset( $_GET['restored'] ) ? absint( $_GET['restored'] ) : null;
	$failed   = isset( $_GET['failed'] ) ? absint( $_GET['failed'] ) : 0;
	// phpcs:enable
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Test lab', 'sfwp-test' ); ?></h1>

		<?php if ( null !== $restored ) : ?>
			<div class="notice <?php echo $failed ? 'notice-warning' : 'notice-success'; ?> is-dismissible">
				<p>
					<?php
					/* translators: 1: number restored, 2: number failed */
					echo esc_html( sprintf( __( 'Restored %1$d items from the theme. %2$d failed.', 'sfwp-test' ), $restored, $failed ) );
					?>
				</p>
			</div>
		<?php endif; ?>

		<p><?php esc_html_e( 'The theme creates these pages and the post on the first request after it is activated. Missing items are created again whenever the theme’s content version goes up; existing ones are left alone.', 'sfwp-test' ); ?></p>

		<table class="widefat striped" style="max-width:56rem">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Item', 'sfwp-test' ); ?></th>
					<th><?php esc_html_e( 'Address', 'sfwp-test' ); ?></th>
					<th><?php esc_html_e( 'Status', 'sfwp-test' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( sfwp_test_seed_items() as $key => $item ) : ?>
					<?php $post = sfwp_test_find( $key ); ?>
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
						<td><?php echo esc_html( $post ? $post->post_status : __( 'missing', 'sfwp-test' ) ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<h2><?php esc_html_e( 'Restore from the theme', 'sfwp-test' ); ?></h2>
		<p><?php esc_html_e( 'Rewrites every item above with the theme’s current version, and creates any that are missing. Edits made to these items in WordPress are lost.', 'sfwp-test' ); ?></p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="sfwp_test_restore">
			<?php wp_nonce_field( 'sfwp_test_restore' ); ?>
			<?php submit_button( __( 'Restore test content', 'sfwp-test' ), 'secondary', 'submit', false ); ?>
		</form>

		<h2><?php esc_html_e( 'Sandbox health', 'sfwp-test' ); ?></h2>
		<ul>
			<?php foreach ( sfwp_test_health_checks( true ) as $check ) : ?>
				<li><strong><?php echo esc_html( $check['label'] ); ?>:</strong> <?php echo esc_html( $check['detail'] ?: $check['state'] ); ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
}
