<?php
/**
 * Custom post types and taxonomies for the Ledger, so the plugin's content
 * rules are exercised beyond posts and categories.
 *
 * These are demo fixtures, not theme features: they exist so the plugin's
 * post type rules, term rules (flat and hierarchical, on one type or shared
 * across several) and per-item rules each have seeded content to run
 * against. Every access decision is still the plugin's.
 *
 * - Briefings (sfwp_briefing): the post type needs Signed in, with a word
 *   teaser. Taxonomy Editions (flat); Weekend opens its briefings for 30 days.
 * - Reports (sfwp_report): the post type needs Gold, opens after a year, and
 *   keeps locked reports out of search engines. Taxonomy Series
 *   (hierarchical, inherits): Harbor Money needs Gold + Silver and passes it
 *   to its children; its child Pensions has its own Silver rule.
 * - City guides (sfwp_guide): no post type rule. Taxonomy Neighborhoods
 *   (flat); Fennel Hill needs Silver.
 * - Corrections (sfwp_notice): gating switched off for the post type, so no
 *   term rule can lock a correction.
 * - Topics (sfwp_topic): one flat taxonomy shared by posts and all four
 *   types; Elections 2026 needs Signed in.
 *
 * Items marked SFWP-SECRET-CPT-<SLUG> must stay out of the source, feeds,
 * REST and search for visitors the rule locks out; SFWP-OPEN-CPT-<SLUG>
 * marks text that everyone should see.
 *
 * @package sfwp-test
 */

defined( 'ABSPATH' ) || exit;

/**
 * @return array<string, array{singular: string, plural: string, slug: string, icon: string, description: string}>
 */
function sfwp_test_post_types(): array {
	return [
		'sfwp_briefing' => [
			'singular'    => __( 'Briefing', 'sfwp-test' ),
			'plural'      => __( 'Briefings', 'sfwp-test' ),
			'slug'        => 'briefings',
			'icon'        => 'dashicons-email-alt',
			'description' => __( 'The morning, evening and weekend briefings: what happened, in five minutes. Free with a Ledger account.', 'sfwp-test' ),
		],
		'sfwp_report' => [
			'singular'    => __( 'Report', 'sfwp-test' ),
			'plural'      => __( 'Reports', 'sfwp-test' ),
			'slug'        => 'reports',
			'icon'        => 'dashicons-chart-bar',
			'description' => __( 'Ledger Reports: long data investigations for Gold members. Reports open to everyone a year after publication.', 'sfwp-test' ),
		],
		'sfwp_guide' => [
			'singular'    => __( 'City guide', 'sfwp-test' ),
			'plural'      => __( 'City guides', 'sfwp-test' ),
			'slug'        => 'guides',
			'icon'        => 'dashicons-location-alt',
			'description' => __( 'Where to eat, walk and wait for the ferry, neighborhood by neighborhood. Most guides are free.', 'sfwp-test' ),
		],
		'sfwp_notice' => [
			'singular'    => __( 'Correction', 'sfwp-test' ),
			'plural'      => __( 'Corrections', 'sfwp-test' ),
			'slug'        => 'corrections',
			'icon'        => 'dashicons-warning',
			'description' => __( 'Corrections and editor’s notes. Always free to read.', 'sfwp-test' ),
		],
	];
}

/**
 * @return array<string, array{singular: string, plural: string, slug: string, hierarchical: bool, types: string[]}>
 */
function sfwp_test_taxonomies(): array {
	return [
		'sfwp_edition'      => [ 'singular' => __( 'Edition', 'sfwp-test' ), 'plural' => __( 'Editions', 'sfwp-test' ), 'slug' => 'edition', 'hierarchical' => false, 'types' => [ 'sfwp_briefing' ] ],
		'sfwp_series'       => [ 'singular' => __( 'Series', 'sfwp-test' ), 'plural' => __( 'Series', 'sfwp-test' ), 'slug' => 'series', 'hierarchical' => true, 'types' => [ 'sfwp_report' ] ],
		'sfwp_neighborhood' => [ 'singular' => __( 'Neighborhood', 'sfwp-test' ), 'plural' => __( 'Neighborhoods', 'sfwp-test' ), 'slug' => 'neighborhood', 'hierarchical' => false, 'types' => [ 'sfwp_guide' ] ],
		'sfwp_topic'        => [ 'singular' => __( 'Topic', 'sfwp-test' ), 'plural' => __( 'Topics', 'sfwp-test' ), 'slug' => 'topic', 'hierarchical' => false, 'types' => [ 'post', 'sfwp_briefing', 'sfwp_report', 'sfwp_guide', 'sfwp_notice' ] ],
	];
}

/**
 * Registered early on init, before the plugin registers its access meta for
 * every gated post type (init 20) and before seeding (also init 20).
 */
function sfwp_test_register_content_types(): void {
	foreach ( sfwp_test_post_types() as $type => $t ) {
		register_post_type(
			$type,
			[
				'label'         => $t['plural'],
				'labels'        => [
					'name'          => $t['plural'],
					'singular_name' => $t['singular'],
					/* translators: %s: singular post type name */
					'add_new_item'  => sprintf( __( 'Add %s', 'sfwp-test' ), $t['singular'] ),
					/* translators: %s: singular post type name */
					'edit_item'     => sprintf( __( 'Edit %s', 'sfwp-test' ), $t['singular'] ),
					'all_items'     => $t['plural'],
				],
				'description'   => $t['description'],
				'public'        => true,
				'show_in_rest'  => true,
				'has_archive'   => $t['slug'],
				'rewrite'       => [ 'slug' => $t['slug'], 'with_front' => false ],
				'menu_icon'     => $t['icon'],
				'menu_position' => 6,
				'supports'      => [ 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'custom-fields', 'revisions' ],
			]
		);
	}

	foreach ( sfwp_test_taxonomies() as $taxonomy => $t ) {
		register_taxonomy(
			$taxonomy,
			$t['types'],
			[
				'labels'            => [ 'name' => $t['plural'], 'singular_name' => $t['singular'] ],
				'public'            => true,
				'hierarchical'      => $t['hierarchical'],
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'rewrite'           => [ 'slug' => $t['slug'], 'with_front' => false, 'hierarchical' => $t['hierarchical'] ],
			]
		);
	}
}
add_action( 'init', 'sfwp_test_register_content_types', 5 );

/**
 * The terms each taxonomy starts with. A `parent` names another term's slug.
 *
 * @return array<string, array<string, array{name: string, parent?: string, description?: string}>>
 */
function sfwp_test_cpt_terms(): array {
	return [
		'sfwp_edition'      => [
			'morning' => [ 'name' => __( 'Morning', 'sfwp-test' ) ],
			'evening' => [ 'name' => __( 'Evening', 'sfwp-test' ) ],
			'weekend' => [ 'name' => __( 'Weekend', 'sfwp-test' ), 'description' => __( 'The weekend briefing is free to everyone this month.', 'sfwp-test' ) ],
		],
		'sfwp_series'       => [
			'harbor-money' => [ 'name' => __( 'Harbor Money', 'sfwp-test' ), 'description' => __( 'Where the port’s money goes. For Gold + Silver members.', 'sfwp-test' ) ],
			'port-budgets' => [ 'name' => __( 'Port budgets', 'sfwp-test' ), 'parent' => 'harbor-money' ],
			'pensions'     => [ 'name' => __( 'Pensions', 'sfwp-test' ), 'parent' => 'harbor-money', 'description' => __( 'Public pensions, for Silver members.', 'sfwp-test' ) ],
			'city-data'    => [ 'name' => __( 'City data', 'sfwp-test' ) ],
		],
		'sfwp_neighborhood' => [
			'lowmarket'   => [ 'name' => __( 'Lowmarket', 'sfwp-test' ) ],
			'fennel-hill' => [ 'name' => __( 'Fennel Hill', 'sfwp-test' ), 'description' => __( 'Our Fennel Hill guides are for Silver members.', 'sfwp-test' ) ],
			'gull-island' => [ 'name' => __( 'Gull Island', 'sfwp-test' ) ],
		],
		'sfwp_topic'        => [
			'harbor-bridge'  => [ 'name' => __( 'Harbor bridge', 'sfwp-test' ) ],
			'elections-2026' => [ 'name' => __( 'Elections 2026', 'sfwp-test' ), 'description' => __( 'Our election coverage needs a free Ledger account.', 'sfwp-test' ) ],
			'ferries'        => [ 'name' => __( 'Ferries', 'sfwp-test' ) ],
		],
	];
}

/**
 * Create the missing terms, parents first. Existing terms are left alone.
 */
function sfwp_test_seed_cpt_terms(): void {
	foreach ( sfwp_test_cpt_terms() as $taxonomy => $terms ) {
		if ( ! taxonomy_exists( $taxonomy ) ) {
			continue;
		}

		foreach ( $terms as $slug => $term ) {
			if ( term_exists( $slug, $taxonomy ) ) {
				continue;
			}

			$parent = isset( $term['parent'] ) ? get_term_by( 'slug', $term['parent'], $taxonomy ) : null;

			wp_insert_term(
				$term['name'],
				$taxonomy,
				[
					'slug'        => $slug,
					'parent'      => $parent instanceof WP_Term ? $parent->term_id : 0,
					'description' => $term['description'] ?? '',
				]
			);
		}
	}
}

function sfwp_test_cpt_term_id( string $taxonomy, string $slug ): int {
	$term = get_term_by( 'slug', $slug, $taxonomy );

	return $term instanceof WP_Term ? (int) $term->term_id : 0;
}

/**
 * The plugin's post type rows for the demo types (Shopify → Access → Content rules).
 *
 * @return array<string, array<string, mixed>>
 */
function sfwp_test_type_rules(): array {
	return [
		'sfwp_briefing' => [ 'enabled' => true, 'levels' => [ 'signed-in' ], 'teaser' => [ 'mode' => 'words', 'count' => 40 ] ],
		'sfwp_report'   => [ 'enabled' => true, 'levels' => [ 'gold' ], 'teaser' => [ 'mode' => 'words', 'count' => 60 ], 'public_after_days' => 365, 'noindex' => true ],
		'sfwp_guide'    => [ 'enabled' => true, 'levels' => [], 'comments' => 'everyone' ],
		'sfwp_notice'   => [ 'enabled' => false ],
	];
}

/**
 * The plugin's term rules for the demo taxonomies. `opens_for` is a number
 * of days from seeding, because an Opens rule must end on a date.
 *
 * @return array<int, array{taxonomy: string, slug: string, rule: array<string, mixed>}>
 */
function sfwp_test_cpt_term_rules(): array {
	return [
		[ 'taxonomy' => 'sfwp_edition', 'slug' => 'weekend', 'rule' => [ 'kind' => 'opens', 'opens_for' => 30 ] ],
		[ 'taxonomy' => 'sfwp_series', 'slug' => 'harbor-money', 'rule' => [ 'kind' => 'requires', 'levels' => [ 'premium' ] ] ],
		[ 'taxonomy' => 'sfwp_series', 'slug' => 'pensions', 'rule' => [ 'kind' => 'requires', 'levels' => [ 'silver' ] ] ],
		[ 'taxonomy' => 'sfwp_neighborhood', 'slug' => 'fennel-hill', 'rule' => [ 'kind' => 'requires', 'levels' => [ 'silver' ] ] ],
		[ 'taxonomy' => 'sfwp_topic', 'slug' => 'elections-2026', 'rule' => [ 'kind' => 'requires', 'levels' => [ 'signed-in' ] ] ],
	];
}

/**
 * The demo items, newest first within each type. `rule` names the layer the
 * plugin should decide by and `expect` the badge a guest should see; both
 * feed the Test lab's content-type table. `item_rule` times are offsets from
 * seeding (`+7 days`), resolved when the rule is saved. `days_ago` dates the
 * item; `break` false leaves the teaser to the post type's word count.
 *
 * @return array<string, array<string, mixed>>
 */
function sfwp_test_cpt_items(): array {
	return [
		// Briefings: the post type needs Signed in.
		'briefing-monday'           => [
			'type'     => 'sfwp_briefing',
			'title'    => 'Monday briefing: the bridge vote, a ferry warning and a record oyster count',
			'terms'    => [ 'sfwp_edition' => [ 'morning' ], 'sfwp_topic' => [ 'harbor-bridge' ] ],
			'byline'   => 'Leah Brandt',
			'dek'      => 'Five things to know before you leave the house.',
			'public'   => [ 'Good morning. The council’s bridge vote ran past midnight, the islands are bracing for hourly ferries, and volunteers counted more oysters in the south bay than at any time since the 1950s.' ],
			'locked'   => [
				'1. The bridge: design money passed 6–3. The state still has to match it next spring.',
				'2. Ferries: hourly service to Gull and Marrow islands starts in November.',
				'3. Oysters: the restoration group counted 41,000 young oysters on the first finished reef.',
			],
			'rule'     => 'Post type: Briefings need Signed in',
			'expect'   => 'Signed in',
			'days_ago' => 0,
		],
		'briefing-evening-arts'     => [
			'type'      => 'sfwp_briefing',
			'title'     => 'Evening briefing: the arts edition',
			'terms'     => [ 'sfwp_edition' => [ 'evening' ] ],
			'byline'    => 'Priya Raman',
			'dek'       => 'Openings, closings and the gallery audit, for Silver members.',
			'public'    => [ 'Tonight: the Maritime Museum’s new hall, two gallery closings on Canal Street and what the arts council said about its audit.' ],
			'locked'    => [
				'The museum sold out its first weekend of timed tickets in under an hour.',
				'Two Canal Street galleries named in our audit story will close at the end of the month.',
			],
			'item_rule' => [ 'mode' => 'add', 'levels' => [ 'silver' ] ],
			'rule'      => 'Item: Add to inherited (Signed in + Silver)',
			'expect'    => 'Signed in for guests, then Silver once signed in',
			'days_ago'  => 1,
		],
		'briefing-weekend'          => [
			'type'     => 'sfwp_briefing',
			'title'    => 'Weekend briefing: what to do on the harbor this weekend',
			'terms'    => [ 'sfwp_edition' => [ 'weekend' ] ],
			'byline'   => 'Priya Raman',
			'dek'      => 'Free for everyone this month: the night market, a tide-pool walk and a ferry discount.',
			'public'   => [ 'The night market is on Friday, the tide is low enough for the Gull Island pools on Saturday morning, and ferries are half price on Sunday.' ],
			'locked'   => [
				'Night market: Canal Street, 6 p.m. until the last stall packs up.',
				'Tide pools: low tide is at 9:40 a.m. on Saturday. Wear boots.',
			],
			'marker'   => 'open',
			'rule'     => 'Term: Weekend edition opens for 30 days',
			'expect'   => 'Free',
			'days_ago' => 2,
		],
		'briefing-storm-special'    => [
			'type'      => 'sfwp_briefing',
			'title'     => 'Storm briefing: closures, shelters and the ferry schedule',
			'terms'     => [ 'sfwp_edition' => [ 'morning' ], 'sfwp_topic' => [ 'ferries' ] ],
			'byline'    => 'Dana Okafor',
			'dek'       => 'Free to everyone for a week while the storm passes.',
			'public'    => [ 'A winter storm reaches the harbor tonight. Here is what is closed and where to go.' ],
			'locked'    => [
				'Shelters open at 6 p.m. at the Lowmarket community center and Pier Road school.',
				'The first ferry on Thursday will leave at 9 a.m., not 6:20.',
			],
			'item_rule' => [ 'mode' => 'inherit', 'public_until' => '+7 days' ],
			'marker'    => 'open',
			'rule'      => 'Item: free until a date (7 days from seeding)',
			'expect'    => 'Free, then Signed in',
			'days_ago'  => 3,
		],

		// Reports: the post type needs Gold; the Series terms replace it.
		'report-port-budget'        => [
			'type'     => 'sfwp_report',
			'title'    => 'The port’s $90 million maintenance backlog, berth by berth',
			'terms'    => [ 'sfwp_series' => [ 'port-budgets' ], 'sfwp_topic' => [ 'harbor-bridge' ] ],
			'byline'   => 'Marcus Feld',
			'dek'      => 'Ten years of inspection reports, mapped. Part of Harbor Money.',
			'public'   => [
				'Every berth in the Port of Harborview is inspected once a year. The Ledger read ten years of those inspections.',
				'Together they describe a backlog of repairs that the port’s own engineers price at $90 million.',
			],
			'locked'   => [
				'Berth 3 has been rated “poor” for six years running. Its fender system was last replaced in 1998.',
				'The port’s capital plan funds a fifth of the backlog over the next five years.',
			],
			'rule'     => 'Term: Port budgets inherits Harbor Money (Gold + Silver); beats the post type',
			'expect'   => 'Gold + Silver',
			'days_ago' => 0,
		],
		'report-pension-gap'        => [
			'type'     => 'sfwp_report',
			'title'    => 'Harborview’s pension gap is $1.2 billion. Here is how it grew.',
			'terms'    => [ 'sfwp_series' => [ 'pensions' ] ],
			'byline'   => 'Marcus Feld',
			'dek'      => 'Twenty years of actuarial reports in one chart. Part of Harbor Money.',
			'public'   => [ 'In 2006 the city’s pension fund held 94 cents for every dollar it owed. Today it holds 61.' ],
			'locked'   => [
				'Most of the gap opened in three years: 2009, 2020 and 2022, when returns fell short of the fund’s 7.5 percent assumption.',
				'The fund lowered that assumption to 6.75 percent last year, which added $140 million to the gap on paper overnight.',
			],
			'rule'     => 'Term: Pensions’ own rule (Silver) beats Harbor Money and the post type',
			'expect'   => 'Silver',
			'days_ago' => 2,
		],
		'report-housing-permits'    => [
			'type'     => 'sfwp_report',
			'title'    => 'Every housing permit Harborview issued since 2015',
			'terms'    => [ 'sfwp_series' => [ 'city-data' ] ],
			'byline'   => 'Leah Brandt',
			'dek'      => 'Where the city built, where it did not, and how long the wait was.',
			'public'   => [
				'Harborview issued 14,212 residential building permits between 2015 and 2025. Half of them went to four of the city’s twenty-two neighborhoods. The median wait from application to permit grew from 41 days to 133.',
				'We mapped every permit and timed every application, to see which parts of the city are building, which are not, and whether the new online system the city launched in 2023 made the wait any shorter for small builders.',
			],
			'locked'   => [
				'Lowmarket, Pier Road, the Cannery district and Fennel Hill account for 51 percent of new homes. The islands account for under one percent.',
				'Permits for buildings with fewer than five homes now take longer than permits for large towers, reversing the pattern of a decade ago.',
			],
			'break'    => false,
			'rule'     => 'Post type: Reports need Gold (teaser: first 60 words, no break block)',
			'expect'   => 'Gold',
			'days_ago' => 4,
		],
		'report-tidewater-storm'    => [
			'type'      => 'sfwp_report',
			'title'     => 'What last winter’s storms really cost Tidewater Power',
			'terms'     => [ 'sfwp_series' => [ 'city-data' ] ],
			'byline'    => 'Marcus Feld',
			'dek'       => 'Under embargo for two weeks; open to everyone after that.',
			'public'    => [ 'Tidewater says last winter’s storms cost it $212 million. We rebuilt the number from its own filings.' ],
			'locked'    => [
				'Our total is $164 million. The difference is mostly overtime billed to the storm account for work done months later.',
			],
			'item_rule' => [ 'mode' => 'inherit', 'locked_until' => '+14 days' ],
			'rule'      => 'Item: embargo for 14 days from seeding, then open',
			'expect'    => 'Gold, then Free',
			'days_ago'  => 5,
		],
		'report-ferry-ridership'    => [
			'type'      => 'sfwp_report',
			'title'     => 'Ferry ridership, ten years on: a free Ledger Report',
			'terms'     => [ 'sfwp_series' => [ 'city-data' ], 'sfwp_topic' => [ 'ferries' ] ],
			'byline'    => 'Dana Okafor',
			'dek'       => 'A sample of what Gold members get, free to everyone.',
			'public'    => [ 'Island ferries carried 3.1 million passengers last year, 14 percent more than in 2015.' ],
			'locked'    => [ 'Most of the growth is on the 7:20 a.m. boat, which now leaves full four days out of five.' ],
			'item_rule' => [ 'mode' => 'public' ],
			'marker'    => 'open',
			'rule'      => 'Item: Public beats the post type',
			'expect'    => 'Free',
			'days_ago'  => 6,
		],
		'report-2024-flood-review'  => [
			'type'     => 'sfwp_report',
			'title'    => 'The 2024 flood review: what the city promised, and what it built',
			'terms'    => [ 'sfwp_series' => [ 'city-data' ] ],
			'byline'   => 'Tomasz Wierzbicki',
			'dek'      => 'From our archive: more than a year old, so free to everyone.',
			'public'   => [ 'After the 2024 floods the city promised forty projects. We tracked every one.' ],
			'locked'   => [ 'Eleven were finished, nineteen had started and ten had not begun.' ],
			'marker'   => 'open',
			'rule'     => 'Post type archive window: open after 365 days',
			'expect'   => 'Free',
			'days_ago' => 400,
		],

		// City guides: no post type rule.
		'guide-lowmarket-eats'      => [
			'type'     => 'sfwp_guide',
			'title'    => 'Where to eat in Lowmarket: a dumpling crawl',
			'terms'    => [ 'sfwp_neighborhood' => [ 'lowmarket' ] ],
			'byline'   => 'Priya Raman',
			'dek'      => 'Six stops, all within ten minutes of the night market.',
			'public'   => [
				'Start at Pier 9 Dumpling, where the same family has folded pork and chive dumplings since 1987.',
				'Walk two blocks east to the hot sauce counter on Fennel Lane, then finish at the ice house for the city’s best sesame buns.',
			],
			'locked'   => [],
			'rule'     => 'No rule anywhere: open',
			'expect'   => 'Free',
			'days_ago' => 1,
		],
		'guide-fennel-hill-walks'   => [
			'type'     => 'sfwp_guide',
			'title'    => 'Three walks on Fennel Hill, with the best views of the harbor',
			'terms'    => [ 'sfwp_neighborhood' => [ 'fennel-hill' ] ],
			'byline'   => 'Tomasz Wierzbicki',
			'dek'      => 'Stairs, gardens and a lookout most residents have never found.',
			'public'   => [ 'Fennel Hill has more public stairways than any other neighborhood in the city: forty-one, by our count.' ],
			'locked'   => [
				'The best of them climbs from Canal Street to Ridge Road through three community gardens.',
				'At the top, turn left behind the water tower for the only view that takes in both bridges and the islands.',
			],
			'rule'     => 'Term: Fennel Hill needs Silver (no post type rule)',
			'expect'   => 'Silver',
			'days_ago' => 3,
		],
		'guide-fennel-hill-market'  => [
			'type'      => 'sfwp_guide',
			'title'     => 'The Fennel Hill Saturday market, stall by stall',
			'terms'     => [ 'sfwp_neighborhood' => [ 'fennel-hill' ] ],
			'byline'    => 'Leah Brandt',
			'dek'       => 'Free to read: the item’s own rule beats the neighborhood’s.',
			'public'    => [ 'Twenty-two stalls, one rule: everything is grown or made on the hill.' ],
			'locked'    => [ 'Come early for the bread. The bakery stall sells out by ten.' ],
			'item_rule' => [ 'mode' => 'public' ],
			'marker'    => 'open',
			'rule'      => 'Item: Public beats the Fennel Hill term',
			'expect'    => 'Free',
			'days_ago'  => 5,
		],
		'guide-island-ferries'      => [
			'type'      => 'sfwp_guide',
			'title'     => 'The insider’s guide to Gull Island by ferry',
			'terms'     => [ 'sfwp_neighborhood' => [ 'gull-island' ], 'sfwp_topic' => [ 'ferries' ] ],
			'byline'    => 'Dana Okafor',
			'dek'       => 'Which boat to take, where to sit and what to skip. For Gold members.',
			'public'    => [ 'Gull Island is forty minutes from Pier 1 and feels much further.' ],
			'locked'    => [
				'Take the 8:40, sit on the left side of the upper deck, and get off at the second stop, not the first.',
				'Skip the lighthouse tour; walk the north beach instead.',
			],
			'item_rule' => [ 'mode' => 'level', 'levels' => [ 'gold' ] ],
			'rule'      => 'Item: its own level (Gold) with no type or term rule',
			'expect'    => 'Gold',
			'days_ago'  => 7,
		],

		// Corrections: gating is off for the post type.
		'notice-bridge-vote'        => [
			'type'     => 'sfwp_notice',
			'title'    => 'Correction: the bridge vote count',
			'terms'    => [ 'sfwp_topic' => [ 'elections-2026', 'harbor-bridge' ] ],
			'byline'   => 'The Editors',
			'dek'      => 'An earlier version of our story had the vote wrong.',
			'public'   => [ 'An earlier version of our story on the harbor bridge said the council voted 7–2. The vote was 6–3. Councilmember Ruth Abernethy voted in favor.' ],
			'locked'   => [ 'We regret the error.' ],
			'marker'   => 'open',
			'rule'     => 'Gating off for Corrections: the Elections 2026 term rule does not apply',
			'expect'   => 'Free',
			'days_ago' => 0,
		],

		// A core post with a custom taxonomy rule.
		'news-council-candidates'   => [
			'type'       => 'post',
			'title'      => 'Who is running for council in 2026: every candidate, district by district',
			'categories' => [ 'city-hall' ],
			'terms'      => [ 'sfwp_topic' => [ 'elections-2026' ] ],
			'byline'     => 'Dana Okafor',
			'dek'        => 'Twenty-three candidates for nine seats. Free with a Ledger account.',
			'public'     => [ 'Filing closed on Friday with twenty-three candidates for the nine council seats on the ballot in November.' ],
			'locked'     => [
				'District 4, which includes Lowmarket, has the most crowded race, with six candidates. Two incumbents are running unopposed.',
				'We asked every candidate the same five questions about the bridge, ferries and housing. Their answers are below.',
			],
			'rule'       => 'Term on a core post: Topics → Elections 2026 needs Signed in',
			'expect'     => 'Signed in',
			'days_ago'   => 0,
		],
	];
}

/**
 * Seed items for the demo content, in the shape sfwp_test_seed_items() returns.
 *
 * @return array<string, array<string, mixed>>
 */
function sfwp_test_cpt_seed_items(): array {
	$items = [];
	// Never in the future: an item dated later today would be scheduled, not published.
	$base = min( strtotime( 'today 07:00' ), time() - 2 * HOUR_IN_SECONDS );

	foreach ( sfwp_test_cpt_items() as $key => $item ) {
		$items[ 'cpt-' . $key ] = [
			'type'       => $item['type'],
			'title'      => $item['title'],
			'slug'       => $key,
			'content'    => static fn() => sfwp_test_cpt_markup( $key, $item ),
			'excerpt'    => $item['dek'],
			'categories' => $item['categories'] ?? [],
			'terms'      => $item['terms'] ?? [],
			'date'       => gmdate( 'Y-m-d H:i:s', $base - (int) $item['days_ago'] * DAY_IN_SECONDS - HOUR_IN_SECONDS * count( $items ) ),
			'meta'       => [ SFWP_TEST_BYLINE_META => $item['byline'] ],
		];
	}

	return $items;
}

/**
 * An item's body: the public paragraphs, a paywall break (unless `break` is
 * false), then the rest and the leak marker. Locking is left to the rules.
 */
function sfwp_test_cpt_markup( string $slug, array $item ): string {
	$markup = sfwp_test_paragraphs( $item['public'] );

	if ( ! $item['locked'] ) {
		return trim( $markup );
	}

	$marker = ( 'open' === ( $item['marker'] ?? '' ) ? 'SFWP-OPEN-CPT-' : 'SFWP-SECRET-CPT-' ) . strtoupper( $slug );

	if ( false !== ( $item['break'] ?? true ) ) {
		$markup .= "<!-- wp:wp2-shopify/paywall-break /-->\n\n";
	}

	$markup .= sfwp_test_paragraphs( $item['locked'] );
	$markup .= "<!-- wp:paragraph {\"className\":\"sfwp-marker\"} -->\n<p class=\"sfwp-marker\">Leak marker: " . esc_html( $marker ) . "</p>\n<!-- /wp:paragraph -->";

	return trim( $markup );
}

/**
 * An item rule with its relative times (`+7 days`) turned into timestamps.
 */
function sfwp_test_item_rule( array $rule ): array {
	foreach ( [ 'locked_until', 'public_until' ] as $key ) {
		if ( isset( $rule[ $key ] ) && is_string( $rule[ $key ] ) ) {
			$rule[ $key ] = (int) strtotime( $rule[ $key ] );
		}
	}

	return $rule;
}

/**
 * The demo types' rules in the plugin: post type rows, term rules and item
 * rules. Without $restore, a post type row, term rule or item rule that
 * already exists is left alone.
 */
function sfwp_test_seed_cpt_rules( bool $restore ): void {
	if ( ! class_exists( \WP2_Shopify\Access\RuleStore::class ) || ! class_exists( \WP2_Shopify\Access\Rules::class ) ) {
		return;
	}

	$config  = \WP2_Shopify\Access\RuleStore::saved();
	$changed = false;

	foreach ( sfwp_test_type_rules() as $type => $row ) {
		if ( $restore || ! isset( $config['types'][ $type ] ) ) {
			$config['types'][ $type ] = $row;
			$changed                  = true;
		}
	}

	if ( $changed ) {
		\WP2_Shopify\Access\RuleStore::save( \WP2_Shopify\Access\Rules::from_array( $config ) );
	}

	foreach ( sfwp_test_cpt_term_rules() as $set ) {
		$term_id = sfwp_test_cpt_term_id( $set['taxonomy'], $set['slug'] );
		$rule    = $set['rule'];

		if ( isset( $rule['opens_for'] ) ) {
			$rule['opens_until'] = time() + (int) $rule['opens_for'] * DAY_IN_SECONDS;
			unset( $rule['opens_for'] );
		}

		if ( $term_id && ( $restore || '' === \WP2_Shopify\Access\TermRules::get( $term_id )['kind'] ) ) {
			\WP2_Shopify\Access\TermRules::save( $term_id, $rule );
		}
	}

	foreach ( sfwp_test_cpt_items() as $key => $item ) {
		$post = isset( $item['item_rule'] ) ? sfwp_test_find( 'cpt-' . $key ) : null;

		if ( $post && ( $restore || '' === get_post_meta( $post->ID, '_wp2_access', true ) ) ) {
			\WP2_Shopify\Access\ItemRule::save( $post->ID, sfwp_test_item_rule( $item['item_rule'] ) );
		}
	}
}

/**
 * New post types and taxonomies need fresh rewrite rules. Flushed once per
 * content version, after every plugin has registered its own on init.
 */
add_action(
	'wp_loaded',
	static function (): void {
		if ( (int) get_option( 'sfwp_test_rewrite_version', 0 ) < SFWP_TEST_CONTENT_VERSION && (int) get_option( SFWP_TEST_CONTENT_OPTION, 0 ) >= SFWP_TEST_CONTENT_VERSION ) {
			flush_rewrite_rules( false ); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules -- once per demo content version.
			update_option( 'sfwp_test_rewrite_version', SFWP_TEST_CONTENT_VERSION, false );
		}
	}
);

add_action( 'after_switch_theme', static fn() => delete_option( 'sfwp_test_rewrite_version' ) );
