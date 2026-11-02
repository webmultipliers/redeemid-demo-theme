<?php
/**
 * The Ledger's contribution form: give any whole amount, or a preset one.
 *
 * Shopify has no name-your-own-price field, so the store sells a $1 product
 * (handle `contribution`) and the amount is the quantity. The form posts the
 * variant as `id` and the amount as `quantity` to the store's /cart/add, and
 * `return_to=/checkout` sends the reader from there to checkout. No
 * JavaScript, and no plugin support: RedeemID's buy-now caps a line at 10,
 * which suits plan cards, not contributions.
 *
 * The amount field and the preset buttons share the name `quantity`. A preset
 * button comes after the field, so its value is the last `quantity` sent and
 * Shopify takes it; `formnovalidate` lets a preset submit with the field
 * empty. /cart/add adds to the reader's Shopify cart, so anything already
 * there goes to checkout too.
 *
 * The product (and so the variant) comes from RedeemID's Storefront reads,
 * cached like every catalog read. Nothing renders unless the store is set
 * and the product's first variant costs exactly 1.00, since only then is the
 * quantity the amount.
 *
 * What a contribution does afterwards is Shopify's job: a Flow workflow on
 * Order paid tags the customer `supporter`, RedeemID mirrors the tag, and the
 * theme's `supporter` feature (inc/features.php) shows the thank-you here.
 *
 * @var array $a        Block attributes.
 * @var bool  $isEditor Rendering inside the block editor.
 */

defined( 'ABSPATH' ) || exit;

$ledger_store   = class_exists( \RedeemID\Config::class ) ? \RedeemID\Config::store_domain() : '';
$ledger_variant = '';
$ledger_symbol  = '';

if ( '' !== $ledger_store && class_exists( \RedeemID\Shopify\Storefront::class ) ) {
	$ledger_product = \RedeemID\Shopify\Storefront::product( (string) ( $a['handle'] ?? 'contribution' ) );
	$ledger_first   = is_array( $ledger_product ) ? ( $ledger_product['variants'][0] ?? null ) : null;

	if ( is_array( $ledger_first ) && ! empty( $ledger_first['available'] ) && 1.0 === (float) ( $ledger_first['price']['amount'] ?? 0 ) && preg_match( '#(\d+)$#', (string) $ledger_first['id'], $ledger_match ) ) {
		$ledger_variant = $ledger_match[1];
		$ledger_symbol  = 'USD' === ( $ledger_first['price']['currency'] ?? '' ) ? '$' : '';
	}
}

if ( '' === $ledger_variant ) {
	if ( $isEditor ) {
		echo '<p useBlockProps class="ledger-contribute ledger-contribute--missing">' . esc_html__( 'The contribution form needs RedeemID connected to a store with a $1 product (handle "contribution").', 'redeemid-demo' ) . '</p>';
	}
	return;
}

$ledger_presets = array_values( array_unique( array_filter( array_map( 'absint', explode( ',', (string) ( $a['presets'] ?? '' ) ) ) ) ) );
$ledger_amount  = max( 1, absint( $a['amount'] ?? 15 ) );
$ledger_compact = ! empty( $a['compact'] );
$ledger_field   = wp_unique_id( 'ledger-contribute-' );
$ledger_money   = static fn( int $n ): string => '' !== $ledger_symbol ? $ledger_symbol . number_format_i18n( $n ) : number_format_i18n( $n );
$ledger_thanks  = function_exists( 'redeemid_has_feature' ) && redeemid_has_feature( 'supporter' );
$ledger_heading = trim( (string) ( $a['heading'] ?? '' ) );
$ledger_body    = trim( (string) ( $a['body'] ?? '' ) );
$ledger_title   = $ledger_field . '-title';
?>
<section useBlockProps class="ledger-contribute<?php echo $ledger_compact ? ' ledger-contribute--compact' : ''; ?>"<?php echo '' !== $ledger_heading ? ' aria-labelledby="' . esc_attr( $ledger_title ) . '"' : ''; ?>>
	<?php if ( '' !== $ledger_heading ) : ?>
		<h2 id="<?php echo esc_attr( $ledger_title ); ?>" class="ledger-contribute__title"><?php echo esc_html( $ledger_heading ); ?></h2>
	<?php endif; ?>

	<?php if ( $ledger_thanks ) : ?>
		<p class="ledger-contribute__thanks"><?php esc_html_e( 'You’re a Ledger supporter. Thank you. Give again any time.', 'redeemid-demo' ); ?></p>
	<?php elseif ( '' !== $ledger_body ) : ?>
		<p class="ledger-contribute__body"><?php echo esc_html( $ledger_body ); ?></p>
	<?php endif; ?>

	<form class="ledger-contribute__form" action="<?php echo esc_url( 'https://' . $ledger_store . '/cart/add' ); ?>" method="post">
		<input type="hidden" name="id" value="<?php echo esc_attr( $ledger_variant ); ?>">
		<input type="hidden" name="return_to" value="/checkout">

		<div class="ledger-contribute__own">
			<label for="<?php echo esc_attr( $ledger_field ); ?>"><?php esc_html_e( 'Amount', 'redeemid-demo' ); ?></label>
			<span class="ledger-contribute__input">
				<?php if ( '' !== $ledger_symbol ) : ?>
					<span aria-hidden="true"><?php echo esc_html( $ledger_symbol ); ?></span>
				<?php endif; ?>
				<input id="<?php echo esc_attr( $ledger_field ); ?>" name="quantity" type="number" inputmode="numeric" min="1" step="1" value="<?php echo esc_attr( (string) $ledger_amount ); ?>" required>
			</span>
			<button type="submit" class="wp-element-button"><?php esc_html_e( 'Contribute', 'redeemid-demo' ); ?></button>
		</div>

		<?php if ( $ledger_presets ) : ?>
			<fieldset class="ledger-contribute__presets">
				<legend><?php esc_html_e( 'Or give', 'redeemid-demo' ); ?></legend>
				<?php foreach ( $ledger_presets as $ledger_value ) : ?>
					<button type="submit" name="quantity" value="<?php echo esc_attr( (string) $ledger_value ); ?>" formnovalidate><?php echo esc_html( $ledger_money( $ledger_value ) ); ?></button>
				<?php endforeach; ?>
			</fieldset>
		<?php endif; ?>
	</form>

	<?php if ( ! $ledger_compact ) : ?>
		<p class="ledger-contribute__note"><?php esc_html_e( 'This is a demo: checkout is the real Shopify demo store. Look at the cart line (your amount as a quantity of a $1 item), then close it without paying.', 'redeemid-demo' ); ?></p>
	<?php endif; ?>
</section>
