<?php
/**
 * Title: Harborview Ledger home (page)
 * Slug: redeemid-demo/page-home
 * Categories: redeemid-demo
 * Post Types: page
 * Description: The faux news front page: a lead story with four seconds, the latest list, how membership works, Ledger Reports, one row per section, briefings, city guides and corrections.
 */

$ledger_cats = array_values( array_filter( array_map( 'redeemid_demo_term_id', array_keys( redeemid_demo_news_categories() ) ) ) );

/**
 * A core/query with the theme's markup for each item. $cats limits posts to
 * news categories; other post types pass none.
 */
$ledger_query = static function ( int $id, string $type, array $cats, int $per_page, int $offset, string $class, string $columns_json, string $inner ): string {
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

$ledger_meta = '<!-- wp:group {"className":"ledger-card__meta","layout":{"type":"flex","flexWrap":"wrap"}} -->
<div class="wp-block-group ledger-card__meta">
<!-- wp:redeemid-demo/story-meta {"showByline":true} /-->
<!-- wp:post-date {"className":"ledger-card__date"} /-->
</div>
<!-- /wp:group -->';

$ledger_card = '<!-- wp:group {"className":"ledger-card","layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group ledger-card">
<!-- wp:post-terms {"term":"category","className":"ledger-kicker"} /-->
<!-- wp:post-title {"isLink":true,"level":3,"className":"ledger-card__title"} /-->
<!-- wp:post-excerpt {"excerptLength":30,"className":"ledger-card__dek"} /-->
' . $ledger_meta . '
</div>
<!-- /wp:group -->';

$ledger_lead = '<!-- wp:group {"className":"ledger-lead","layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group ledger-lead">
<!-- wp:post-terms {"term":"category","className":"ledger-kicker"} /-->
<!-- wp:post-title {"isLink":true,"level":2,"className":"ledger-lead__title"} /-->
<!-- wp:post-excerpt {"className":"ledger-lead__dek"} /-->
' . $ledger_meta . '
</div>
<!-- /wp:group -->';

$ledger_headline = '<!-- wp:group {"className":"ledger-latest__item","layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group ledger-latest__item">
<!-- wp:post-terms {"term":"category","className":"ledger-kicker"} /-->
<!-- wp:post-title {"isLink":true,"level":3,"className":"ledger-latest__title"} /-->
<!-- wp:redeemid-demo/story-meta /-->
</div>
<!-- /wp:group -->';

$ledger_report = '<!-- wp:group {"className":"ledger-report-card","layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group ledger-report-card">
<!-- wp:post-terms {"term":"ledger_series","className":"ledger-kicker"} /-->
<!-- wp:post-title {"isLink":true,"level":3,"className":"ledger-report-card__title"} /-->
<!-- wp:post-excerpt {"className":"ledger-report-card__dek"} /-->
' . $ledger_meta . '
</div>
<!-- /wp:group -->';

$ledger_briefing = '<!-- wp:group {"className":"ledger-latest__item","layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group ledger-latest__item">
<!-- wp:post-terms {"term":"ledger_edition","className":"ledger-kicker"} /-->
<!-- wp:post-title {"isLink":true,"level":3,"className":"ledger-latest__title"} /-->
<!-- wp:group {"className":"ledger-card__meta","layout":{"type":"flex","flexWrap":"wrap"}} -->
<div class="wp-block-group ledger-card__meta">
<!-- wp:redeemid-demo/story-meta /-->
<!-- wp:post-date {"className":"ledger-card__date"} /-->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->';

$ledger_guide = '<!-- wp:group {"className":"ledger-card","layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group ledger-card">
<!-- wp:post-terms {"term":"ledger_neighborhood","className":"ledger-kicker"} /-->
<!-- wp:post-title {"isLink":true,"level":3,"className":"ledger-card__title"} /-->
<!-- wp:post-excerpt {"excerptLength":24,"className":"ledger-card__dek"} /-->
' . $ledger_meta . '
</div>
<!-- /wp:group -->';

$ledger_correction = '<!-- wp:group {"className":"ledger-correction","layout":{"type":"flex","flexWrap":"wrap"}} -->
<div class="wp-block-group ledger-correction">
<!-- wp:post-date {"className":"ledger-card__date"} /-->
<!-- wp:post-title {"isLink":true,"level":3,"className":"ledger-correction__title"} /-->
</div>
<!-- /wp:group -->';

/*
 * What each kind of reader gets. The labels match the plugin's levels and
 * the badges on every story, so the band explains what the badges mean.
 */
$ledger_tiers = [
	[ 'free', __( 'Free', 'redeemid-demo' ), __( 'Everyone', 'redeemid-demo' ), __( 'City Hall, transit, science, the night market, corrections and most city guides. No account needed.', 'redeemid-demo' ) ],
	[ 'signed-in', __( 'Signed in', 'redeemid-demo' ), __( 'Free account', 'redeemid-demo' ), __( 'Every briefing, election coverage and the editorial board. Sign in with your Shopify account.', 'redeemid-demo' ) ],
	[ 'silver', __( 'Silver', 'redeemid-demo' ), __( 'Silver members', 'redeemid-demo' ), __( 'The culture desk and features, the arts briefing and the Fennel Hill guides.', 'redeemid-demo' ) ],
	[ 'gold', __( 'Gold', 'redeemid-demo' ), __( 'Gold members', 'redeemid-demo' ), __( 'Investigations and Ledger Reports, the stories that take months and a lawyer’s review.', 'redeemid-demo' ) ],
	[ 'premium', __( 'Gold + Silver', 'redeemid-demo' ), __( 'Both', 'redeemid-demo' ), __( 'Everything we publish, including Harbor Money and stories that cross both desks.', 'redeemid-demo' ) ],
];
?>
<!-- wp:columns {"align":"wide","className":"ledger-front"} -->
<div class="wp-block-columns alignwide ledger-front">
	<!-- wp:column {"width":"68%","className":"ledger-front__main"} -->
	<div class="wp-block-column ledger-front__main" style="flex-basis:68%">
		<?php echo $ledger_query( 101, 'post', $ledger_cats, 1, 0, 'ledger-front__lead', '', $ledger_lead ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php echo $ledger_query( 103, 'post', $ledger_cats, 4, 1, 'ledger-front__seconds', '{"layout":{"type":"grid","columnCount":2}}', $ledger_card ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</div>
	<!-- /wp:column -->
	<!-- wp:column {"width":"32%","className":"ledger-latest"} -->
	<div class="wp-block-column ledger-latest" style="flex-basis:32%">
		<!-- wp:heading {"level":2,"className":"ledger-section-title"} -->
		<h2 class="wp-block-heading ledger-section-title">Latest</h2>
		<!-- /wp:heading -->
		<?php echo $ledger_query( 102, 'post', $ledger_cats, 6, 5, 'ledger-front__latest', '', $ledger_headline ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</div>
	<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:group {"align":"wide","className":"ledger-tiers","layout":{"type":"constrained","contentSize":"1120px"}} -->
<div class="wp-block-group alignwide ledger-tiers">
	<!-- wp:group {"className":"ledger-tiers__intro","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"bottom"}} -->
	<div class="wp-block-group ledger-tiers__intro">
		<!-- wp:group {"layout":{"type":"flex","orientation":"vertical"},"style":{"spacing":{"blockGap":"0.25rem"}}} -->
		<div class="wp-block-group">
			<!-- wp:paragraph {"className":"ledger-kicker"} -->
			<p class="ledger-kicker">How the Ledger works</p>
			<!-- /wp:paragraph -->
			<!-- wp:heading {"level":2,"className":"ledger-tiers__title"} -->
			<h2 class="wp-block-heading ledger-tiers__title">Most of the Ledger is free. Members pay for the rest.</h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"className":"ledger-tiers__lede"} -->
			<p class="ledger-tiers__lede">Every story carries a badge that says who can read it. No badge, or <em>Free</em>, means everyone.</p>
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
	<!-- wp:group {"className":"ledger-tiers__grid","layout":{"type":"grid","minimumColumnWidth":"11rem"}} -->
	<div class="wp-block-group ledger-tiers__grid">
		<?php foreach ( $ledger_tiers as $ledger_tier ) : ?>
		<!-- wp:group {"className":"ledger-tier ledger-tier--<?php echo esc_attr( $ledger_tier[0] ); ?>","layout":{"type":"flex","orientation":"vertical"}} -->
		<div class="wp-block-group ledger-tier ledger-tier--<?php echo esc_attr( $ledger_tier[0] ); ?>">
			<!-- wp:paragraph {"className":"ledger-tier__badge"} -->
			<p class="ledger-tier__badge"><span class="redeemid-access-badge" data-redeemid-level="<?php echo esc_attr( $ledger_tier[0] ); ?>"><?php echo esc_html( $ledger_tier[1] ); ?></span></p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph {"className":"ledger-tier__who"} -->
			<p class="ledger-tier__who"><?php echo esc_html( $ledger_tier[2] ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph {"className":"ledger-tier__what"} -->
			<p class="ledger-tier__what"><?php echo esc_html( $ledger_tier[3] ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
		<?php endforeach; ?>
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"ledger-reports","backgroundColor":"contrast","textColor":"base","layout":{"type":"constrained","contentSize":"1120px"}} -->
<div class="wp-block-group alignfull ledger-reports has-base-color has-contrast-background-color has-text-color has-background">
	<!-- wp:group {"className":"ledger-reports__head","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"bottom"}} -->
	<div class="wp-block-group ledger-reports__head">
		<!-- wp:group {"layout":{"type":"flex","orientation":"vertical"},"style":{"spacing":{"blockGap":"0.25rem"}}} -->
		<div class="wp-block-group">
			<!-- wp:heading {"level":2,"className":"ledger-reports__title"} -->
			<h2 class="wp-block-heading ledger-reports__title"><a href="/reports/">Ledger Reports</a></h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"className":"ledger-reports__lede"} -->
			<p class="ledger-reports__lede">Data investigations for Gold members. Every report opens to everyone a year after we publish it.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
		<!-- wp:paragraph {"className":"ledger-reports__all"} -->
		<p class="ledger-reports__all"><a href="/reports/">All reports →</a></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
	<?php echo $ledger_query( 104, 'ledger_report', [], 3, 0, 'ledger-reports__grid', '{"layout":{"type":"grid","columnCount":3}}', $ledger_report ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
</div>
<!-- /wp:group -->

<?php
$ledger_id = 110;

foreach ( redeemid_demo_news_categories() as $ledger_slug => $ledger_name ) :
	$ledger_term = redeemid_demo_term_id( $ledger_slug );

	if ( ! $ledger_term ) {
		continue;
	}
	?>
<!-- wp:group {"align":"wide","className":"ledger-section","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide ledger-section">
	<!-- wp:heading {"level":2,"className":"ledger-section-title"} -->
	<h2 class="wp-block-heading ledger-section-title"><a href="<?php echo esc_url( wp_make_link_relative( (string) get_category_link( $ledger_term ) ) ); ?>"><?php echo esc_html( $ledger_name ); ?></a></h2>
	<!-- /wp:heading -->
	<?php echo $ledger_query( $ledger_id++, 'post', [ $ledger_term ], 3, 0, 'ledger-section__grid', '{"layout":{"type":"grid","columnCount":3}}', $ledger_card ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
</div>
<!-- /wp:group -->
<?php endforeach; ?>

<!-- wp:columns {"align":"wide","className":"ledger-more"} -->
<div class="wp-block-columns alignwide ledger-more">
	<!-- wp:column {"width":"34%","className":"ledger-latest"} -->
	<div class="wp-block-column ledger-latest" style="flex-basis:34%">
		<!-- wp:heading {"level":2,"className":"ledger-section-title"} -->
		<h2 class="wp-block-heading ledger-section-title"><a href="/briefings/">Briefings</a></h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"className":"ledger-section-lede"} -->
		<p class="ledger-section-lede">The day in five minutes, free with a Ledger account.</p>
		<!-- /wp:paragraph -->
		<?php echo $ledger_query( 130, 'ledger_briefing', [], 4, 0, 'ledger-front__briefings', '', $ledger_briefing ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</div>
	<!-- /wp:column -->
	<!-- wp:column {"width":"66%"} -->
	<div class="wp-block-column" style="flex-basis:66%">
		<!-- wp:heading {"level":2,"className":"ledger-section-title"} -->
		<h2 class="wp-block-heading ledger-section-title"><a href="/guides/">City guides</a></h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"className":"ledger-section-lede"} -->
		<p class="ledger-section-lede">Where to eat, walk and wait for the ferry. Most guides are free.</p>
		<!-- /wp:paragraph -->
		<?php echo $ledger_query( 131, 'ledger_guide', [], 4, 0, 'ledger-section__grid', '{"layout":{"type":"grid","columnCount":2}}', $ledger_guide ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</div>
	<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:group {"align":"wide","className":"ledger-section ledger-corrections","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide ledger-section ledger-corrections">
	<!-- wp:heading {"level":2,"className":"ledger-section-title"} -->
	<h2 class="wp-block-heading ledger-section-title"><a href="/corrections/">Corrections</a></h2>
	<!-- /wp:heading -->
	<?php echo $ledger_query( 132, 'ledger_notice', [], 3, 0, 'ledger-front__corrections', '', $ledger_correction ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
</div>
<!-- /wp:group -->
