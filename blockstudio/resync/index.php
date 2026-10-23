<?php
/**
 * Front-end resync control. The server renders the same hidden shell for
 * everyone; script.view.js asks the plugin's status endpoint who the visitor
 * is and shows the button only to signed-in, linked visitors. The REST nonce
 * comes from that status call, never from the cached page.
 *
 * @var array $a        Block attributes.
 * @var bool  $isEditor Rendering inside the block editor.
 */

defined( 'ABSPATH' ) || exit;

$sfwp_status_url = class_exists( \WP2_Shopify\Rest\Status::class )
	? add_query_arg( 'action', \WP2_Shopify\Rest\Status::AJAX_ACTION, admin_url( 'admin-ajax.php' ) )
	: '';

if ( '' === $sfwp_status_url && ! $isEditor ) {
	return;
}

$sfwp_label = '' !== trim( (string) ( $a['label'] ?? '' ) ) ? (string) $a['label'] : __( 'Refresh membership', 'sfwp-test' );
?>
<div useBlockProps class="sfwp-resync<?php echo empty( $a['compact'] ) ? '' : ' is-compact'; ?>"
	data-sfwp-resync-status="<?php echo esc_url( $sfwp_status_url ); ?>"
	data-sfwp-resync-endpoint="<?php echo esc_url( rest_url( 'wp2-shopify/v1/account/sync' ) ); ?>"
	<?php echo $isEditor ? '' : 'hidden'; ?>>
	<button type="button" class="sfwp-resync__button"><?php echo esc_html( $sfwp_label ); ?></button>
	<span class="sfwp-resync__message" aria-live="polite"><?php echo $isEditor ? esc_html__( 'Shows to signed-in, linked visitors only.', 'sfwp-test' ) : ''; ?></span>
</div>
