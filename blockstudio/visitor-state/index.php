<?php
/**
 * Visitor state panel, rendered as a Blockstudio dynamic island.
 *
 * The page carries the same neutral placeholder for everyone, so it stays
 * cacheable. Blockstudio's island runtime then asks the site, in one batched,
 * never-cached request for every island on the page, to render the panel for
 * the visitor, using the plugin's own status helper. Nonces are only counted,
 * never printed. See https://blockstudio.dev/docs/blocks/islands.
 *
 * @var array $a                   Block attributes.
 * @var bool  $isEditor            Rendering inside the block editor.
 * @var bool  $isIslandFragment    Rendering the visitor's own fragment.
 */

defined( 'ABSPATH' ) || exit;

$sfwp_active = class_exists( \WP2_Shopify\Rest\Status::class );
$sfwp_rows   = [
	'signedIn'  => __( 'Signed in to WordPress', 'sfwp-test' ),
	'name'      => __( 'Display name', 'sfwp-test' ),
	'linked'    => __( 'Linked to a Shopify customer', 'sfwp-test' ),
	'shadow'    => __( 'Shadow user (created by Shopify sign-in)', 'sfwp-test' ),
	'lastSync'  => __( 'Last sync from Shopify', 'sfwp-test' ),
	'available' => __( 'Shopify sign-in available', 'sfwp-test' ),
	'nonces'    => __( 'Fresh nonces delivered', 'sfwp-test' ),
];
$sfwp_values = array_fill_keys( array_keys( $sfwp_rows ), '…' );
$sfwp_status = null;
$sfwp_note   = __( 'Checking who you are…', 'sfwp-test' );

if ( $isEditor ) {
	$sfwp_note = __( 'Fills in on the live page from the WP2 Shopify status helper.', 'sfwp-test' );
} elseif ( ! $sfwp_active ) {
	$sfwp_note = __( 'The WP2 Shopify plugin is not active, so there is no status to show.', 'sfwp-test' );
} elseif ( $isIslandFragment ) {
	$sfwp_status = \WP2_Shopify\Rest\Status::for_current_user();
	$sfwp_in     = ! empty( $sfwp_status['signedIn'] );
	$sfwp_yes    = static fn ( $value ): string => $value ? __( 'Yes', 'sfwp-test' ) : __( 'No', 'sfwp-test' );
	$sfwp_sync   = (int) ( $sfwp_status['lastSync'] ?? 0 );

	// Redact nonces: the panel proves they arrive, it never shows them.
	if ( isset( $sfwp_status['nonces'] ) ) {
		$sfwp_status['nonces'] = array_fill_keys( array_keys( $sfwp_status['nonces'] ), '(redacted)' );
	}

	$sfwp_values = [
		'signedIn'  => $sfwp_yes( $sfwp_in ),
		'name'      => $sfwp_in ? ( $sfwp_status['displayName'] ?? '' ) ?: '—' : '—',
		'linked'    => $sfwp_in ? $sfwp_yes( ! empty( $sfwp_status['linked'] ) ) : '—',
		'shadow'    => $sfwp_in ? $sfwp_yes( ! empty( $sfwp_status['shadow'] ) ) : '—',
		'lastSync'  => ! $sfwp_in ? '—' : ( $sfwp_sync
			/* translators: 1: date and time, 2: how long ago, such as "5 mins" */
			? sprintf( __( '%1$s (%2$s ago)', 'sfwp-test' ), wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $sfwp_sync ), human_time_diff( $sfwp_sync ) )
			: __( 'Never', 'sfwp-test' ) ),
		'available' => $sfwp_yes( ! empty( $sfwp_status['available'] ) ),
		'nonces'    => $sfwp_in ? $sfwp_yes( ! empty( $sfwp_status['nonces']['logout'] ) && ! empty( $sfwp_status['nonces']['rest'] ) ) : __( 'Not for guests', 'sfwp-test' ),
	];

	if ( empty( $sfwp_status['available'] ) ) {
		$sfwp_note = __( 'Shopify sign-in is not available: the store domain or client ID is missing, or Shopify discovery failed. See the Shopify admin menu.', 'sfwp-test' );
	} elseif ( ! $sfwp_in ) {
		$sfwp_note = __( 'You are a guest. Use “Log in with Shopify” in the header to start the flow.', 'sfwp-test' );
	} elseif ( empty( $sfwp_status['linked'] ) && empty( $sfwp_status['shadow'] ) ) {
		$sfwp_note = __( 'You are signed in to WordPress but not connected to Shopify. Try “Connect Shopify account” on the Account page.', 'sfwp-test' );
	} else {
		$sfwp_note = __( 'Signed in and connected to Shopify.', 'sfwp-test' );
	}
}

$sfwp_class = 'sfwp-panel sfwp-visitor';
if ( $sfwp_status && ! empty( $sfwp_status['signedIn'] ) ) {
	$sfwp_class .= ' is-signed-in';
}
if ( $sfwp_status && ( ! empty( $sfwp_status['linked'] ) || ! empty( $sfwp_status['shadow'] ) ) ) {
	$sfwp_class .= ' is-linked';
}
?>
<div useBlockProps class="<?php echo esc_attr( $sfwp_class ); ?>">
	<p class="sfwp-panel__title"><?php esc_html_e( 'Visitor state', 'sfwp-test' ); ?></p>
	<?php if ( $sfwp_active && ! $isEditor ) : ?>
		<table class="sfwp-panel__table">
			<tbody>
				<?php foreach ( $sfwp_rows as $sfwp_key => $sfwp_label ) : ?>
					<tr>
						<th scope="row"><?php echo esc_html( $sfwp_label ); ?></th>
						<td><?php echo esc_html( $sfwp_values[ $sfwp_key ] ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
	<p class="sfwp-panel__note" aria-live="polite"><?php echo esc_html( $sfwp_note ); ?></p>
	<?php if ( $sfwp_status && ! empty( $a['showRaw'] ) ) : ?>
		<details class="sfwp-panel__raw">
			<summary><?php esc_html_e( 'Raw status response', 'sfwp-test' ); ?></summary>
			<pre><?php echo esc_html( (string) wp_json_encode( $sfwp_status, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) ); ?></pre>
		</details>
	<?php endif; ?>
</div>
