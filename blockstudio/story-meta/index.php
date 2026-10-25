<?php
/**
 * The Ledger's story meta line: the plugin's Access Badge, then the byline.
 *
 * Access is the plugin's job. The badge is redeemid/access-badge, rendered
 * for the post in context; it reads the story's content rule (Investigations)
 * or the level of the Membership Gate in its body, and labels open items
 * Free (the badge's free label). The theme only adds the byline, which is
 * seeded story data, and styles the badge per level in assets/theme.css.
 *
 * @var array $a        Block attributes.
 * @var int   $post_id  The post in context (each loop item, or the page).
 * @var bool  $isEditor Rendering inside the block editor.
 */

defined( 'ABSPATH' ) || exit;

$ledger_post   = (int) $post_id;
$ledger_byline = $ledger_post ? (string) get_post_meta( $ledger_post, REDEEMID_DEMO_BYLINE_META, true ) : '';
$ledger_badge  = '';

if ( $ledger_post && WP_Block_Type_Registry::get_instance()->is_registered( 'redeemid/access-badge' ) ) {
	// Pass this block's post on as the badge's context, so each card of a query loop gets its own badge.
	$ledger_badge = ( new WP_Block(
		// "Free" on open items, so free and paid stories read differently in every list.
		[ 'blockName' => 'redeemid/access-badge', 'attrs' => [ 'freeLabel' => __( 'Free', 'redeemid-demo' ) ], 'innerBlocks' => [], 'innerHTML' => '', 'innerContent' => [] ],
		[ 'postId' => $ledger_post, 'postType' => get_post_type( $ledger_post ) ]
	) )->render();
}

if ( $isEditor && '' === $ledger_byline ) {
	$ledger_byline = 'Byline';
}

$ledger_byline = ! empty( $a['showByline'] ) ? $ledger_byline : '';
?>
<?php if ( '' !== trim( $ledger_badge ) || '' !== $ledger_byline ) : ?>
<div useBlockProps class="ledger-story-meta">
	<?php echo $ledger_badge; // phpcs:ignore WordPress.Security.EscapeOutput -- rendered and escaped by the plugin's block. ?>
	<?php if ( '' !== $ledger_byline ) : ?>
		<?php /* translators: %s: author name */ ?>
		<span class="ledger-byline"><?php echo esc_html( sprintf( __( 'By %s', 'redeemid-demo' ), $ledger_byline ) ); ?></span>
	<?php endif; ?>
</div>
<?php endif; ?>
