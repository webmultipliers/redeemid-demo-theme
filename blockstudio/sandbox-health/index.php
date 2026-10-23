<?php
/**
 * Sandbox health checklist. Everything shown to visitors is a site-level
 * yes/no; which credential constants exist is shown to administrators only,
 * and no value is ever printed.
 *
 * @var bool $isEditor Rendering inside the block editor.
 */

defined( 'ABSPATH' ) || exit;

$sfwp_checks = sfwp_test_health_checks( current_user_can( 'manage_options' ) );
?>
<div useBlockProps class="sfwp-panel sfwp-health">
	<p class="sfwp-panel__title"><?php esc_html_e( 'Sandbox health', 'sfwp-test' ); ?></p>
	<ul class="sfwp-health__list">
		<?php foreach ( $sfwp_checks as $sfwp_check ) : ?>
			<li class="sfwp-health__item is-<?php echo esc_attr( $sfwp_check['state'] ); ?>">
				<span class="sfwp-health__state"><?php echo esc_html( [ 'ok' => '✓', 'warn' => '!', 'fail' => '✗', 'info' => '·' ][ $sfwp_check['state'] ] ?? '·' ); ?></span>
				<span class="sfwp-health__label"><?php echo esc_html( $sfwp_check['label'] ); ?></span>
				<span class="sfwp-health__detail"><?php echo esc_html( $sfwp_check['detail'] ); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php if ( ! current_user_can( 'manage_options' ) ) : ?>
		<p class="sfwp-panel__note"><?php esc_html_e( 'Administrators also see which Shopify constants are set.', 'sfwp-test' ); ?></p>
	<?php endif; ?>
</div>
