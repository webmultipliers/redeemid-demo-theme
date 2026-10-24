<?php
/**
 * The Ledger's story meta line: the plugin's Access Badge, then the byline.
 *
 * Access is the plugin's job. The badge is wp2-shopify/access-badge, rendered
 * for the post in context; it reads the story's content rule (Investigations)
 * or the level of the Membership Gate in its body. The theme only adds the
 * byline, which is seeded story data, and styles the badge per level in
 * assets/theme.css.
 *
 * @var array $a        Block attributes.
 * @var int   $post_id  The post in context (each loop item, or the page).
 * @var bool  $isEditor Rendering inside the block editor.
 */

defined( 'ABSPATH' ) || exit;

$sfwp_post   = (int) $post_id;
$sfwp_byline = $sfwp_post ? (string) get_post_meta( $sfwp_post, SFWP_TEST_BYLINE_META, true ) : '';
$sfwp_badge  = '';

if ( $sfwp_post && WP_Block_Type_Registry::get_instance()->is_registered( 'wp2-shopify/access-badge' ) ) {
	// Pass this block's post on as the badge's context, so each card of a query loop gets its own badge.
	$sfwp_badge = ( new WP_Block(
		[ 'blockName' => 'wp2-shopify/access-badge', 'attrs' => [], 'innerBlocks' => [], 'innerHTML' => '', 'innerContent' => [] ],
		[ 'postId' => $sfwp_post, 'postType' => get_post_type( $sfwp_post ) ]
	) )->render();
}

if ( $isEditor && '' === $sfwp_byline ) {
	$sfwp_byline = 'Byline';
}

$sfwp_byline = ! empty( $a['showByline'] ) ? $sfwp_byline : '';
?>
<?php if ( '' !== trim( $sfwp_badge ) || '' !== $sfwp_byline ) : ?>
<div useBlockProps class="sfwp-story-meta">
	<?php echo $sfwp_badge; // phpcs:ignore WordPress.Security.EscapeOutput -- rendered and escaped by the plugin's block. ?>
	<?php if ( '' !== $sfwp_byline ) : ?>
		<?php /* translators: %s: author name */ ?>
		<span class="sfwp-byline"><?php echo esc_html( sprintf( __( 'By %s', 'sfwp-test' ), $sfwp_byline ) ); ?></span>
	<?php endif; ?>
</div>
<?php endif; ?>
