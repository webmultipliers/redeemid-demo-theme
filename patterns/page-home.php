<?php
/**
 * Title: Harborview Ledger home (page)
 * Slug: sfwp-test/page-home
 * Categories: sfwp-test
 * Post Types: page
 * Description: The faux news front page: a lead story with four seconds, the latest list, how membership works, Ledger Reports, one row per section, briefings, city guides and corrections.
 */

$sfwp_cats = array_values( array_filter( array_map( 'sfwp_test_term_id', array_keys( sfwp_test_news_categories() ) ) ) );

/**
 * A core/query with the theme's markup for each item. $cats limits posts to
 * news categories; other post types pass none.
 */
$sfwp_query = static function ( int $id, string $type, array $cats, int $per_page, int $offset, string $class, string $columns_json, string $inner ): string {
	$query = [
		'queryId' => $id,
		'query'   => [
			'perPage'  => $per_page,
			'pages'    => 0,
			'offset'   => $offset,
			'postType' => $type,
			'order'    => 'desc',
			'orderBy'  => 'date',
			'inherit'  => false,
			'sticky'   => 'exclude',
		] + ( $cats ? [ 'taxQuery' => [ 'category' => $cats ] ] : [] ),
		'className' => $class,
	];

	return '<!-- wp:query ' . wp_json_encode( $query ) . " -->\n<div class=\"wp-block-query " . esc_attr( $class ) . "\">\n"
		. '<!-- wp:post-template' . ( $columns_json ? ' ' . $columns_json : '' ) . " -->\n" . $inner . "\n<!-- /wp:post-template -->\n"
		. "</div>\n<!-- /wp:query -->\n";
};

$sfwp_meta = '<!-- wp:group {"className":"sfwp-card__meta","layout":{"type":"flex","flexWrap":"wrap"}} -->
<div class="wp-block-group sfwp-card__meta">
<!-- wp:sfwp-test/story-meta {"showByline":true} /-->
<!-- wp:post-date {"className":"sfwp-card__date"} /-->
</div>
<!-- /wp:group -->';

$sfwp_card = '<!-- wp:group {"className":"sfwp-card","layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group sfwp-card">
<!-- wp:post-terms {"term":"category","className":"sfwp-kicker"} /-->
<!-- wp:post-title {"isLink":true,"level":3,"className":"sfwp-card__title"} /-->
<!-- wp:post-excerpt {"excerptLength":30,"className":"sfwp-card__dek"} /-->
' . $sfwp_meta . '
</div>
<!-- /wp:group -->';

$sfwp_lead = '<!-- wp:group {"className":"sfwp-lead","layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group sfwp-lead">
<!-- wp:post-terms {"term":"category","className":"sfwp-kicker"} /-->
<!-- wp:post-title {"isLink":true,"level":2,"className":"sfwp-lead__title"} /-->
<!-- wp:post-excerpt {"className":"sfwp-lead__dek"} /-->
' . $sfwp_meta . '
</div>
<!-- /wp:group -->';

$sfwp_headline = '<!-- wp:group {"className":"sfwp-latest__item","layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group sfwp-latest__item">
<!-- wp:post-terms {"term":"category","className":"sfwp-kicker"} /-->
<!-- wp:post-title {"isLink":true,"level":3,"className":"sfwp-latest__title"} /-->
<!-- wp:sfwp-test/story-meta /-->
</div>
<!-- /wp:group -->';

$sfwp_report = '<!-- wp:group {"className":"sfwp-report-card","layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group sfwp-report-card">
<!-- wp:post-terms {"term":"sfwp_series","className":"sfwp-kicker"} /-->
<!-- wp:post-title {"isLink":true,"level":3,"className":"sfwp-report-card__title"} /-->
<!-- wp:post-excerpt {"className":"sfwp-report-card__dek"} /-->
' . $sfwp_meta . '
</div>
<!-- /wp:group -->';

$sfwp_briefing = '<!-- wp:group {"className":"sfwp-latest__item","layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group sfwp-latest__item">
<!-- wp:post-terms {"term":"sfwp_edition","className":"sfwp-kicker"} /-->
<!-- wp:post-title {"isLink":true,"level":3,"className":"sfwp-latest__title"} /-->
<!-- wp:group {"className":"sfwp-card__meta","layout":{"type":"flex","flexWrap":"wrap"}} -->
<div class="wp-block-group sfwp-card__meta">
<!-- wp:sfwp-test/story-meta /-->
<!-- wp:post-date {"className":"sfwp-card__date"} /-->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->';

$sfwp_guide = '<!-- wp:group {"className":"sfwp-card","layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group sfwp-card">
<!-- wp:post-terms {"term":"sfwp_neighborhood","className":"sfwp-kicker"} /-->
<!-- wp:post-title {"isLink":true,"level":3,"className":"sfwp-card__title"} /-->
<!-- wp:post-excerpt {"excerptLength":24,"className":"sfwp-card__dek"} /-->
' . $sfwp_meta . '
</div>
<!-- /wp:group -->';

$sfwp_correction = '<!-- wp:group {"className":"sfwp-correction","layout":{"type":"flex","flexWrap":"wrap"}} -->
<div class="wp-block-group sfwp-correction">
<!-- wp:post-date {"className":"sfwp-card__date"} /-->
<!-- wp:post-title {"isLink":true,"level":3,"className":"sfwp-correction__title"} /-->
</div>
<!-- /wp:group -->';

/*
 * What each kind of reader gets. The labels match the plugin's levels and
 * the badges on every story, so the band explains what the badges mean.
 */
$sfwp_tiers = [
	[ 'free', __( 'Free', 'sfwp-test' ), __( 'Everyone', 'sfwp-test' ), __( 'City Hall, transit, science, the night market, corrections and most city guides. No account needed.', 'sfwp-test' ) ],
	[ 'signed-in', __( 'Signed in', 'sfwp-test' ), __( 'Free account', 'sfwp-test' ), __( 'Every briefing, election coverage and the editorial board. Sign in with your Shopify account.', 'sfwp-test' ) ],
	[ 'silver', __( 'Silver', 'sfwp-test' ), __( 'Silver members', 'sfwp-test' ), __( 'The culture desk and features, the arts briefing and the Fennel Hill guides.', 'sfwp-test' ) ],
	[ 'gold', __( 'Gold', 'sfwp-test' ), __( 'Gold members', 'sfwp-test' ), __( 'Investigations and Ledger Reports, the stories that take months and a lawyer’s review.', 'sfwp-test' ) ],
	[ 'premium', __( 'Gold + Silver', 'sfwp-test' ), __( 'Both', 'sfwp-test' ), __( 'Everything we publish, including Harbor Money and stories that cross both desks.', 'sfwp-test' ) ],
];
?>
<!-- wp:columns {"align":"wide","className":"sfwp-front"} -->
<div class="wp-block-columns alignwide sfwp-front">
	<!-- wp:column {"width":"68%","className":"sfwp-front__main"} -->
	<div class="wp-block-column sfwp-front__main" style="flex-basis:68%">
		<?php echo $sfwp_query( 101, 'post', $sfwp_cats, 1, 0, 'sfwp-front__lead', '', $sfwp_lead ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php echo $sfwp_query( 103, 'post', $sfwp_cats, 4, 1, 'sfwp-front__seconds', '{"layout":{"type":"grid","columnCount":2}}', $sfwp_card ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</div>
	<!-- /wp:column -->
	<!-- wp:column {"width":"32%","className":"sfwp-latest"} -->
	<div class="wp-block-column sfwp-latest" style="flex-basis:32%">
		<!-- wp:heading {"level":2,"className":"sfwp-section-title"} -->
		<h2 class="wp-block-heading sfwp-section-title">Latest</h2>
		<!-- /wp:heading -->
		<?php echo $sfwp_query( 102, 'post', $sfwp_cats, 6, 5, 'sfwp-front__latest', '', $sfwp_headline ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</div>
	<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:group {"align":"wide","className":"sfwp-tiers","layout":{"type":"constrained","contentSize":"1120px"}} -->
<div class="wp-block-group alignwide sfwp-tiers">
	<!-- wp:group {"className":"sfwp-tiers__intro","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"bottom"}} -->
	<div class="wp-block-group sfwp-tiers__intro">
		<!-- wp:group {"layout":{"type":"flex","orientation":"vertical"},"style":{"spacing":{"blockGap":"0.25rem"}}} -->
		<div class="wp-block-group">
			<!-- wp:paragraph {"className":"sfwp-kicker"} -->
			<p class="sfwp-kicker">How the Ledger works</p>
			<!-- /wp:paragraph -->
			<!-- wp:heading {"level":2,"className":"sfwp-tiers__title"} -->
			<h2 class="wp-block-heading sfwp-tiers__title">Most of the Ledger is free. Members pay for the rest.</h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"className":"sfwp-tiers__lede"} -->
			<p class="sfwp-tiers__lede">Every story carries a badge that says who can read it. No badge, or <em>Free</em>, means everyone.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
		<!-- wp:buttons -->
		<div class="wp-block-buttons">
			<!-- wp:button -->
			<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/join/">See membership</a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:group -->
	<!-- wp:group {"className":"sfwp-tiers__grid","layout":{"type":"grid","minimumColumnWidth":"11rem"}} -->
	<div class="wp-block-group sfwp-tiers__grid">
		<?php foreach ( $sfwp_tiers as $sfwp_tier ) : ?>
		<!-- wp:group {"className":"sfwp-tier sfwp-tier--<?php echo esc_attr( $sfwp_tier[0] ); ?>","layout":{"type":"flex","orientation":"vertical"}} -->
		<div class="wp-block-group sfwp-tier sfwp-tier--<?php echo esc_attr( $sfwp_tier[0] ); ?>">
			<!-- wp:paragraph {"className":"sfwp-tier__badge"} -->
			<p class="sfwp-tier__badge"><span class="wp2-shopify-access-badge" data-wp2-level="<?php echo esc_attr( $sfwp_tier[0] ); ?>"><?php echo esc_html( $sfwp_tier[1] ); ?></span></p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph {"className":"sfwp-tier__who"} -->
			<p class="sfwp-tier__who"><?php echo esc_html( $sfwp_tier[2] ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph {"className":"sfwp-tier__what"} -->
			<p class="sfwp-tier__what"><?php echo esc_html( $sfwp_tier[3] ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
		<?php endforeach; ?>
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"sfwp-reports","backgroundColor":"contrast","textColor":"base","layout":{"type":"constrained","contentSize":"1120px"}} -->
<div class="wp-block-group alignfull sfwp-reports has-base-color has-contrast-background-color has-text-color has-background">
	<!-- wp:group {"className":"sfwp-reports__head","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"bottom"}} -->
	<div class="wp-block-group sfwp-reports__head">
		<!-- wp:group {"layout":{"type":"flex","orientation":"vertical"},"style":{"spacing":{"blockGap":"0.25rem"}}} -->
		<div class="wp-block-group">
			<!-- wp:heading {"level":2,"className":"sfwp-reports__title"} -->
			<h2 class="wp-block-heading sfwp-reports__title"><a href="/reports/">Ledger Reports</a></h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"className":"sfwp-reports__lede"} -->
			<p class="sfwp-reports__lede">Data investigations for Gold members. Every report opens to everyone a year after we publish it.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
		<!-- wp:paragraph {"className":"sfwp-reports__all"} -->
		<p class="sfwp-reports__all"><a href="/reports/">All reports →</a></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
	<?php echo $sfwp_query( 104, 'sfwp_report', [], 3, 0, 'sfwp-reports__grid', '{"layout":{"type":"grid","columnCount":3}}', $sfwp_report ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
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
	<?php echo $sfwp_query( $sfwp_id++, 'post', [ $sfwp_term ], 3, 0, 'sfwp-section__grid', '{"layout":{"type":"grid","columnCount":3}}', $sfwp_card ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
</div>
<!-- /wp:group -->
<?php endforeach; ?>

<!-- wp:columns {"align":"wide","className":"sfwp-more"} -->
<div class="wp-block-columns alignwide sfwp-more">
	<!-- wp:column {"width":"34%","className":"sfwp-latest"} -->
	<div class="wp-block-column sfwp-latest" style="flex-basis:34%">
		<!-- wp:heading {"level":2,"className":"sfwp-section-title"} -->
		<h2 class="wp-block-heading sfwp-section-title"><a href="/briefings/">Briefings</a></h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"className":"sfwp-section-lede"} -->
		<p class="sfwp-section-lede">The day in five minutes, free with a Ledger account.</p>
		<!-- /wp:paragraph -->
		<?php echo $sfwp_query( 130, 'sfwp_briefing', [], 4, 0, 'sfwp-front__briefings', '', $sfwp_briefing ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</div>
	<!-- /wp:column -->
	<!-- wp:column {"width":"66%"} -->
	<div class="wp-block-column" style="flex-basis:66%">
		<!-- wp:heading {"level":2,"className":"sfwp-section-title"} -->
		<h2 class="wp-block-heading sfwp-section-title"><a href="/guides/">City guides</a></h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"className":"sfwp-section-lede"} -->
		<p class="sfwp-section-lede">Where to eat, walk and wait for the ferry. Most guides are free.</p>
		<!-- /wp:paragraph -->
		<?php echo $sfwp_query( 131, 'sfwp_guide', [], 4, 0, 'sfwp-section__grid', '{"layout":{"type":"grid","columnCount":2}}', $sfwp_guide ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</div>
	<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:group {"align":"wide","className":"sfwp-section sfwp-corrections","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide sfwp-section sfwp-corrections">
	<!-- wp:heading {"level":2,"className":"sfwp-section-title"} -->
	<h2 class="wp-block-heading sfwp-section-title"><a href="/corrections/">Corrections</a></h2>
	<!-- /wp:heading -->
	<?php echo $sfwp_query( 132, 'sfwp_notice', [], 3, 0, 'sfwp-front__corrections', '', $sfwp_correction ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
</div>
<!-- /wp:group -->
