<?php
/**
 * Access badge and byline for a Harborview Ledger story. The badge only
 * labels the story; the membership gate inside the post, or the plugin's
 * content rules for the Investigations stories, decide access.
 *
 * @var array $a        Block attributes.
 * @var bool  $isEditor Rendering inside the block editor.
 */

defined( 'ABSPATH' ) || exit;

$sfwp_post   = get_the_ID();
$sfwp_access = $sfwp_post ? (string) get_post_meta( $sfwp_post, SFWP_TEST_ACCESS_META, true ) : '';
$sfwp_byline = $sfwp_post ? (string) get_post_meta( $sfwp_post, SFWP_TEST_BYLINE_META, true ) : '';
$sfwp_levels = sfwp_test_access_levels();

if ( 'rule' === $sfwp_access ) {
	$sfwp_access = sfwp_test_rule_access( get_post( $sfwp_post ) );
}

if ( $isEditor && '' === $sfwp_access ) {
	$sfwp_access = 'gold';
	$sfwp_byline = 'Byline';
}

if ( '' === $sfwp_access && '' === $sfwp_byline ) {
	return;
}
?>
<div useBlockProps class="sfwp-story-meta">
	<?php if ( isset( $sfwp_levels[ $sfwp_access ] ) ) : ?>
		<span class="sfwp-access is-<?php echo esc_attr( $sfwp_access ); ?>"><?php echo esc_html( $sfwp_levels[ $sfwp_access ]['label'] ); ?></span>
	<?php endif; ?>
	<?php if ( ! empty( $a['showByline'] ) && '' !== $sfwp_byline ) : ?>
		<?php /* translators: %s: author name */ ?>
		<span class="sfwp-byline"><?php echo esc_html( sprintf( __( 'By %s', 'sfwp-test' ), $sfwp_byline ) ); ?></span>
	<?php endif; ?>
</div>
