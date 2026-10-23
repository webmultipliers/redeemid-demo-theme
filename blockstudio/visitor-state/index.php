<?php
/**
 * Visitor state panel.
 *
 * The server renders the same neutral shell for everyone, so the page stays
 * cacheable. script.view.js asks the plugin's status endpoint (the same one
 * the plugin's sign-in control uses) and fills the table in.
 *
 * @var array $a        Block attributes.
 * @var bool  $isEditor Rendering inside the block editor.
 */

defined( 'ABSPATH' ) || exit;

$sfwp_status_url = class_exists( \WP2_Shopify\Rest\Status::class )
	? add_query_arg( 'action', \WP2_Shopify\Rest\Status::AJAX_ACTION, admin_url( 'admin-ajax.php' ) )
	: '';

$sfwp_rows = [
	'signedIn'  => __( 'Signed in to WordPress', 'sfwp-test' ),
	'name'      => __( 'Display name', 'sfwp-test' ),
	'linked'    => __( 'Linked to a Shopify customer', 'sfwp-test' ),
	'shadow'    => __( 'Shadow user (created by Shopify sign-in)', 'sfwp-test' ),
	'lastSync'  => __( 'Last sync from Shopify', 'sfwp-test' ),
	'available' => __( 'Shopify sign-in available', 'sfwp-test' ),
	'nonces'    => __( 'Fresh nonces delivered', 'sfwp-test' ),
];
?>
<div useBlockProps class="sfwp-panel sfwp-visitor" data-sfwp-status="<?php echo esc_url( $sfwp_status_url ); ?>" data-sfwp-raw="<?php echo empty( $a['showRaw'] ) ? '0' : '1'; ?>">
	<p class="sfwp-panel__title"><?php esc_html_e( 'Visitor state', 'sfwp-test' ); ?></p>
	<?php if ( $isEditor ) : ?>
		<p class="sfwp-panel__note"><?php esc_html_e( 'Fills in on the live page from the WP2 Shopify status endpoint.', 'sfwp-test' ); ?></p>
	<?php elseif ( '' === $sfwp_status_url ) : ?>
		<p class="sfwp-panel__note"><?php esc_html_e( 'The WP2 Shopify plugin is not active, so there is no status to show.', 'sfwp-test' ); ?></p>
	<?php else : ?>
		<table class="sfwp-panel__table">
			<tbody>
				<?php foreach ( $sfwp_rows as $sfwp_key => $sfwp_label ) : ?>
					<tr>
						<th scope="row"><?php echo esc_html( $sfwp_label ); ?></th>
						<td data-sfwp-field="<?php echo esc_attr( $sfwp_key ); ?>">…</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<p class="sfwp-panel__note" data-sfwp-message aria-live="polite"><?php esc_html_e( 'Checking who you are…', 'sfwp-test' ); ?></p>
		<details class="sfwp-panel__raw" hidden>
			<summary><?php esc_html_e( 'Raw status response', 'sfwp-test' ); ?></summary>
			<pre data-sfwp-raw-output></pre>
		</details>
	<?php endif; ?>
</div>
