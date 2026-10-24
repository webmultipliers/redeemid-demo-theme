<?php
/**
 * Front-end resync control, rendered as a Blockstudio dynamic island.
 *
 * The page carries an empty placeholder for everyone. The island fragment,
 * rendered per visitor by a never-cached request, shows the button only to
 * signed-in, linked visitors and carries their REST nonce, so the nonce never
 * reaches the cached page. script.view.js only handles the click.
 *
 * @var array $a                   Block attributes.
 * @var bool  $isEditor            Rendering inside the block editor.
 * @var bool  $isIslandFragment    Rendering the visitor's own fragment.
 */

defined( 'ABSPATH' ) || exit;

$sfwp_label = '' !== trim( (string) ( $a['label'] ?? '' ) ) ? (string) $a['label'] : __( 'Refresh membership', 'sfwp-test' );
$sfwp_class = 'sfwp-resync' . ( empty( $a['compact'] ) ? '' : ' is-compact' );

if ( $isEditor ) {
	?>
	<div useBlockProps class="<?php echo esc_attr( $sfwp_class ); ?>">
		<button type="button" class="sfwp-resync__button" disabled><?php echo esc_html( $sfwp_label ); ?></button>
		<span class="sfwp-resync__message"><?php esc_html_e( 'Shows to signed-in, linked visitors only.', 'sfwp-test' ); ?></span>
	</div>
	<?php
	return;
}

$sfwp_status = $isIslandFragment && class_exists( \WP2_Shopify\Rest\Status::class ) ? \WP2_Shopify\Rest\Status::for_current_user() : [];
$sfwp_linked = ! empty( $sfwp_status['signedIn'] ) && ( ! empty( $sfwp_status['linked'] ) || ! empty( $sfwp_status['shadow'] ) ) && ! empty( $sfwp_status['nonces']['rest'] );
$sfwp_sync   = (int) ( $sfwp_status['lastSync'] ?? 0 );
$sfwp_note   = $sfwp_sync
	/* translators: %s: how long ago, such as "5 mins" */
	? sprintf( __( 'Synced from Shopify %s ago.', 'sfwp-test' ), human_time_diff( $sfwp_sync ) )
	: __( 'Not synced yet.', 'sfwp-test' );

// No early return for the empty state: Blockstudio reads a template that returns as one that
// rendered nothing, and would then skip loading script.view.js for the page.
if ( ! $sfwp_linked ) :
	// Placeholder, guests and unlinked accounts: nothing to re-sync, but keep the wrapper for the island.
	?>
	<div useBlockProps class="<?php echo esc_attr( $sfwp_class ); ?>" hidden></div>
<?php else : ?>
	<div useBlockProps class="<?php echo esc_attr( $sfwp_class ); ?>"
		data-sfwp-resync-endpoint="<?php echo esc_url( rest_url( 'wp2-shopify/v1/account/sync' ) ); ?>"
		data-sfwp-resync-nonce="<?php echo esc_attr( $sfwp_status['nonces']['rest'] ); ?>">
		<button type="button" class="sfwp-resync__button"><?php echo esc_html( $sfwp_label ); ?></button>
		<span class="sfwp-resync__message" aria-live="polite"><?php echo empty( $a['compact'] ) ? esc_html( $sfwp_note ) : ''; ?></span>
	</div>
<?php endif; ?>
