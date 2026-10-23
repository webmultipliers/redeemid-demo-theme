<?php
/**
 * Title: Harborview Ledger home (page)
 * Slug: sfwp-test/page-home
 * Categories: sfwp-test
 * Post Types: page
 * Description: The faux news front page: a lead story, the latest list, a membership band and one row per section.
 */

$sfwp_cats = array_values( array_filter( array_map( 'sfwp_test_term_id', array_keys( sfwp_test_news_categories() ) ) ) );

/**
 * A core/query over news categories, with the theme's card markup.
 */
$sfwp_query = static function ( int $id, array $cats, int $per_page, int $offset, string $class, string $columns_json, string $inner ): string {
	$query = [
		'queryId' => $id,
		'query'   => [
			'perPage'  => $per_page,
			'pages'    => 0,
			'offset'   => $offset,
			'postType' => 'post',
			'order'    => 'desc',
			'orderBy'  => 'date',
			'inherit'  => false,
			'sticky'   => 'exclude',
			'taxQuery' => [ 'category' => $cats ],
		],
		'className' => $class,
	];

	return '<!-- wp:query ' . wp_json_encode( $query ) . " -->\n<div class=\"wp-block-query " . esc_attr( $class ) . "\">\n"
		. '<!-- wp:post-template' . ( $columns_json ? ' ' . $columns_json : '' ) . " -->\n" . $inner . "\n<!-- /wp:post-template -->\n"
		. "</div>\n<!-- /wp:query -->\n";
};

$sfwp_card = '<!-- wp:group {"className":"sfwp-card","layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group sfwp-card">
<!-- wp:post-terms {"term":"category","className":"sfwp-kicker"} /-->
<!-- wp:post-title {"isLink":true,"level":3,"className":"sfwp-card__title"} /-->
<!-- wp:post-excerpt {"excerptLength":30,"className":"sfwp-card__dek"} /-->
<!-- wp:group {"className":"sfwp-card__meta","layout":{"type":"flex","flexWrap":"wrap"}} -->
<div class="wp-block-group sfwp-card__meta">
<!-- wp:sfwp-test/story-meta {"showByline":true} /-->
<!-- wp:post-date {"className":"sfwp-card__date"} /-->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->';

$sfwp_lead = '<!-- wp:group {"className":"sfwp-lead","layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group sfwp-lead">
<!-- wp:post-terms {"term":"category","className":"sfwp-kicker"} /-->
<!-- wp:post-title {"isLink":true,"level":2,"className":"sfwp-lead__title"} /-->
<!-- wp:post-excerpt {"className":"sfwp-lead__dek"} /-->
<!-- wp:group {"className":"sfwp-card__meta","layout":{"type":"flex","flexWrap":"wrap"}} -->
<div class="wp-block-group sfwp-card__meta">
<!-- wp:sfwp-test/story-meta {"showByline":true} /-->
<!-- wp:post-date {"className":"sfwp-card__date"} /-->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->';

$sfwp_headline = '<!-- wp:group {"className":"sfwp-latest__item","layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group sfwp-latest__item">
<!-- wp:post-title {"isLink":true,"level":3,"className":"sfwp-latest__title"} /-->
<!-- wp:sfwp-test/story-meta /-->
</div>
<!-- /wp:group -->';
?>
<!-- wp:columns {"align":"wide","className":"sfwp-front"} -->
<div class="wp-block-columns alignwide sfwp-front">
	<!-- wp:column {"width":"66%"} -->
	<div class="wp-block-column" style="flex-basis:66%">
		<?php echo $sfwp_query( 101, $sfwp_cats, 1, 0, 'sfwp-front__lead', '', $sfwp_lead ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</div>
	<!-- /wp:column -->
	<!-- wp:column {"width":"34%","className":"sfwp-latest"} -->
	<div class="wp-block-column sfwp-latest" style="flex-basis:34%">
		<!-- wp:heading {"level":2,"className":"sfwp-section-title"} -->
		<h2 class="wp-block-heading sfwp-section-title">Latest</h2>
		<!-- /wp:heading -->
		<?php echo $sfwp_query( 102, $sfwp_cats, 5, 1, 'sfwp-front__latest', '', $sfwp_headline ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</div>
	<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:group {"align":"wide","className":"sfwp-band","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
<div class="wp-block-group alignwide sfwp-band">
	<!-- wp:paragraph -->
	<p><strong>Most of the Ledger is free.</strong> Members read our investigations, features and the editorial board in full. Stories are marked Free, Members, Silver, Gold or Gold + Silver.</p>
	<!-- /wp:paragraph -->
	<!-- wp:buttons -->
	<div class="wp-block-buttons">
		<!-- wp:button -->
		<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/join/">Become a member</a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->

<?php
$sfwp_id = 110;

foreach ( sfwp_test_news_categories() as $sfwp_slug => $sfwp_name ) :
	$sfwp_term = sfwp_test_term_id( $sfwp_slug );

	if ( ! $sfwp_term ) {
		continue;
	}
	?>
<!-- wp:group {"align":"wide","className":"sfwp-section","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide sfwp-section">
	<!-- wp:heading {"level":2,"className":"sfwp-section-title"} -->
	<h2 class="wp-block-heading sfwp-section-title"><a href="<?php echo esc_url( wp_make_link_relative( (string) get_category_link( $sfwp_term ) ) ); ?>"><?php echo esc_html( $sfwp_name ); ?></a></h2>
	<!-- /wp:heading -->
	<?php echo $sfwp_query( $sfwp_id++, [ $sfwp_term ], 3, 0, 'sfwp-section__grid', '{"layout":{"type":"grid","columnCount":3}}', $sfwp_card ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
</div>
<!-- /wp:group -->
<?php endforeach; ?>

<!-- wp:paragraph {"align":"wide","className":"sfwp-note"} -->
<p class="alignwide sfwp-note">The Harborview Ledger is fiction, written to test the WP2 Shopify plugin. Testers: the <a href="/test-lab/">Test lab</a> has the gate cases, the test accounts and the leak checks.</p>
<!-- /wp:paragraph -->
