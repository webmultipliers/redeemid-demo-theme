<?php
/**
 * The Harborview Ledger: a faux news site seeded next to the Test lab, so the
 * membership gate is exercised on realistic stories as well as test cases.
 *
 * Every story is fiction. Paywalled stories wrap their body in a
 * wp2-shopify/membership-gate with a teaser fallback, and their members-only
 * part carries a leak marker starting with SFWP-SECRET, so the same search,
 * feed and REST leak checks cover them.
 *
 * @package sfwp-test
 */

defined( 'ABSPATH' ) || exit;

const SFWP_TEST_ACCESS_META = '_sfwp_test_access';
const SFWP_TEST_BYLINE_META = '_sfwp_test_byline';

/**
 * Access levels: who may read the full story.
 *
 * @return array<string, array{label: string, tags: string[], match: string, login: bool}>
 */
function sfwp_test_access_levels(): array {
	return [
		'free'    => [ 'label' => __( 'Free', 'sfwp-test' ), 'tags' => [], 'match' => 'any', 'login' => false ],
		'members' => [ 'label' => __( 'Members', 'sfwp-test' ), 'tags' => [], 'match' => 'any', 'login' => true ],
		'silver'  => [ 'label' => __( 'Silver', 'sfwp-test' ), 'tags' => [ 'silver-member' ], 'match' => 'any', 'login' => true ],
		'gold'    => [ 'label' => __( 'Gold', 'sfwp-test' ), 'tags' => [ 'gold-member' ], 'match' => 'any', 'login' => true ],
		'premium' => [ 'label' => __( 'Gold + Silver', 'sfwp-test' ), 'tags' => [ 'gold-member', 'silver-member' ], 'match' => 'all', 'login' => true ],
	];
}

/**
 * @return array<string, string> Category slug => name.
 */
function sfwp_test_news_categories(): array {
	return [
		'city-hall'  => __( 'City Hall', 'sfwp-test' ),
		'business'   => __( 'Business', 'sfwp-test' ),
		'technology' => __( 'Technology', 'sfwp-test' ),
		'science'    => __( 'Science', 'sfwp-test' ),
		'culture'    => __( 'Culture', 'sfwp-test' ),
		'opinion'    => __( 'Opinion', 'sfwp-test' ),
	];
}

function sfwp_test_term_id( string $slug ): int {
	$term = get_term_by( 'slug', $slug, 'category' );

	return $term instanceof WP_Term ? (int) $term->term_id : 0;
}

/**
 * Create the news categories that are missing.
 */
function sfwp_test_seed_categories(): void {
	foreach ( sfwp_test_news_categories() as $slug => $name ) {
		if ( ! term_exists( $slug, 'category' ) ) {
			wp_insert_term( $name, 'category', [ 'slug' => $slug ] );
		}
	}
}

/**
 * The stories, newest first. `public` paragraphs are open to everyone;
 * `locked` paragraphs sit inside the gate for anything but free stories.
 *
 * @return array<string, array{title: string, category: string, access: string, byline: string, dek: string, public: string[], locked: string[]}>
 */
function sfwp_test_news_stories(): array {
	return [
		'harbor-bridge-vote'        => [
			'title'    => 'Council clears the way for a second harbor bridge after a nine-hour session',
			'category' => 'city-hall',
			'access'   => 'free',
			'byline'   => 'Dana Okafor',
			'dek'      => 'The 6–3 vote ends a decade of studies. Construction could start in 2028 if the state matches the city’s share.',
			'public'   => [
				'Harborview’s city council voted 6–3 just after midnight to fund design work on a second bridge across the inner harbor, ending a debate that has outlasted three mayors and two traffic studies.',
				'Supporters said the single span on Pier Road can no longer carry the morning commute and the freight trucks bound for the container terminal. Opponents asked for the money to go to ferries and the long-delayed east-side bus lanes instead.',
				'The $41 million design contract is only the first step. Councilmember Ruth Abernethy, who wrote the measure, said construction depends on the state legislature matching the city’s share next spring.',
				'Residents of the Lowmarket neighborhood, where the new span would land, packed the chamber for most of the night. Many said they were not against a bridge, only against losing the waterfront park the current plan runs through.',
			],
			'locked'   => [],
		],
		'tidewater-rate-plan'       => [
			'title'    => 'Inside Tidewater Power’s plan to raise rates three times by 2029',
			'category' => 'business',
			'access'   => 'gold',
			'byline'   => 'Marcus Feld',
			'dek'      => 'A filing obtained by the Ledger shows the utility wants staged increases to pay for grid hardening along the coast.',
			'public'   => [
				'Tidewater Power, the utility that serves most of the Harborview region, is preparing to ask regulators for three rate increases over the next four years, according to a draft filing reviewed by the Ledger.',
				'The company says the money would pay for burying lines in the flood zone and replacing substations damaged in last winter’s storms.',
			],
			'locked'   => [
				'The draft puts the first increase at 6.5 percent for households, with smaller steps in 2027 and 2029. Commercial customers would see a different schedule, weighted toward demand charges.',
				'Two people familiar with the filing said the company has discussed a discount for low-income customers but has not settled on its size. A Tidewater spokesperson declined to comment on a draft.',
				'Consumer advocates said they would push for an independent audit of the storm costs before any increase is approved.',
			],
		],
		'lowmarket-night-market'    => [
			'title'    => 'The Lowmarket night market is back, and it is bigger than ever',
			'category' => 'culture',
			'access'   => 'free',
			'byline'   => 'Priya Raman',
			'dek'      => 'Sixty stalls, two stages and a rule that every vendor must live within ten miles.',
			'public'   => [
				'After two summers off, the Lowmarket night market returned to Canal Street on Friday with sixty stalls, two music stages and lines that stretched past the old ice house.',
				'Organizers kept the rule that made the market a local favorite: every vendor must live or work within ten miles of the harbor. That meant dumplings from a family that has fished out of Pier 9 for three generations, and hot sauce bottled in a garage on Fennel Hill.',
				'The market runs every Friday through September, from 6 p.m. until the last stall packs up.',
			],
			'locked'   => [],
		],
		'quantum-lab-funding'       => [
			'title'    => 'A small university lab just won the region’s biggest research grant. Here is what they are building.',
			'category' => 'science',
			'access'   => 'members',
			'byline'   => 'Tomasz Wierzbicki',
			'dek'      => 'Eleven researchers in a converted boathouse are trying to make quantum sensors cheap enough for ships.',
			'public'   => [
				'The Coastal Physics Group at Harborview State occupies a converted boathouse with a leaking skylight. On Tuesday it became the recipient of a $28 million federal grant, the largest research award in the region’s history.',
				'The group builds quantum sensors, instruments that measure tiny changes in magnetic fields. Their goal is to make the devices cheap and rugged enough to bolt onto cargo ships.',
			],
			'locked'   => [
				'Lab director Imani Castellanos said the grant will pay for a clean room and four new postdoctoral positions, and for a two-year trial on the ferry fleet starting next year.',
				'The sensors could let ships navigate when satellite positioning is jammed or unavailable, a problem that has grown more common in busy shipping lanes.',
				'“We are not trying to win a physics prize,” Castellanos said. “We are trying to make something a deckhand can fix with a screwdriver.”',
			],
		],
		'transit-app-review'        => [
			'title'    => 'We rode every bus line for a week to test the city’s new transit app',
			'category' => 'technology',
			'access'   => 'free',
			'byline'   => 'Leah Brandt',
			'dek'      => 'Arrival times were right four times out of five. Transfers are where it falls apart.',
			'public'   => [
				'The city’s new transit app promises live arrival times for all 23 bus lines. Over seven days and 61 rides, we checked its predictions against a stopwatch.',
				'It was within two minutes 79 percent of the time, a big improvement on the old printed schedules. Morning rides on the crosstown routes were the most accurate.',
				'Transfers were the weak spot. The app often suggested connections that left forty seconds to cross a four-lane street, and twice it routed us to a stop that had moved in March.',
				'The transit authority said a fix for the moved stops ships next month.',
			],
			'locked'   => [],
		],
		'port-automation-deal'      => [
			'title'    => 'Port automation deal would cut 300 jobs over five years, union says',
			'category' => 'business',
			'access'   => 'premium',
			'byline'   => 'Marcus Feld',
			'dek'      => 'The terms of the tentative agreement have not been made public. The Ledger has read them.',
			'public'   => [
				'The Port of Harborview and the dockworkers’ union reached a tentative agreement this week on automating two of the terminal’s six berths. Neither side has released its terms.',
				'The union’s local president told members in a recorded meeting that the deal would cut roughly 300 positions over five years, mostly through retirements.',
			],
			'locked'   => [
				'The agreement, which the Ledger has read in full, guarantees retraining for workers under 55 and pays a lump sum to those who leave early, scaled by years of service.',
				'Automated cranes would arrive on Berth 4 first, in 2027, followed by Berth 5 eighteen months later. The port keeps the right to automate a third berth if volumes rise by more than a fifth.',
				'Members vote on the deal on the 14th. Several stewards said they expect it to pass narrowly.',
			],
		],
		'ferry-schedule-cuts'       => [
			'title'    => 'Ferry service to the islands will drop to hourly this winter',
			'category' => 'city-hall',
			'access'   => 'members',
			'byline'   => 'Dana Okafor',
			'dek'      => 'Island residents say the cut makes commuting to the mainland nearly impossible.',
			'public'   => [
				'The regional ferry authority will run boats to Gull and Marrow islands once an hour from November through March, down from every forty minutes, citing a shortage of licensed captains.',
				'About 1,900 people live on the two islands year-round, and many commute to jobs in Harborview.',
			],
			'locked'   => [
				'In a letter to the authority, the islands’ joint council said the hourly schedule would push the first mainland arrival past 7 a.m., too late for hospital shift changes.',
				'The authority said it is training six new captains, who should be licensed by spring, and that it will add an early boat on weekdays if it can borrow a crew from the north route.',
			],
		],
		'oyster-reef-restoration'   => [
			'title'    => 'Volunteers have rebuilt a mile of oyster reef. The water is already clearer.',
			'category' => 'science',
			'access'   => 'free',
			'byline'   => 'Tomasz Wierzbicki',
			'dek'      => 'Recycled shells from forty restaurants are turning into habitat in the south bay.',
			'public'   => [
				'Every Saturday morning since April, a few dozen volunteers have hauled mesh bags of oyster shells into the south bay. The shells come from forty local restaurants that set them aside instead of throwing them away.',
				'The bags become the base of a new reef. Young oysters settle on them, and each adult filters up to fifty gallons of water a day.',
				'Measurements taken by the restoration group show the water above the first finished section is noticeably clearer than it was a year ago.',
			],
			'locked'   => [],
		],
		'startup-layoffs-analysis'  => [
			'title'    => 'Why Harborview’s startup boom is quietly turning into a bust',
			'category' => 'technology',
			'access'   => 'gold',
			'byline'   => 'Leah Brandt',
			'dek'      => 'Layoff notices, empty offices and a funding drought: an analysis of two years of filings.',
			'public'   => [
				'Two years ago Harborview had more venture-backed startups per resident than any city on the coast. Since January, state layoff notices from local tech companies have outnumbered new office leases three to one.',
				'The Ledger analyzed every notice and lease filing since 2024 to understand what changed.',
			],
			'locked'   => [
				'Most of the cuts came from eleven companies that raised large rounds in 2023 and have not raised since. Seven of them sell software to logistics firms, a market that shrank when freight volumes fell.',
				'Founders we spoke to described a funding market that now asks for profits within eighteen months, rather than growth at any cost.',
				'Not every company is shrinking. Three local firms that sell to hospitals have doubled their staff, and one is moving into the old cannery offices on Pier 2.',
			],
		],
		'museum-reopening'          => [
			'title'    => 'The Maritime Museum reopens with a hall built around a whale skeleton',
			'category' => 'culture',
			'access'   => 'silver',
			'byline'   => 'Priya Raman',
			'dek'      => 'Three years and $60 million later, the renovated museum keeps its oddest exhibit at the center.',
			'public'   => [
				'The Harborview Maritime Museum reopens on Saturday after a three-year renovation, with a new central hall built around the 52-foot fin whale skeleton that has hung in the building since 1931.',
				'Curators say visitors begged them not to move it.',
			],
			'locked'   => [
				'The new hall adds a ramp that spirals up and around the skeleton, so visitors can see it from below, beside and above. Each level tells part of the story of the harbor’s whaling era, and of its end.',
				'Admission will be free on the first Sunday of every month, paid for by a gift from the Oduya family, whose shipyard built many of the boats in the collection.',
			],
		],
		'opinion-bridge'            => [
			'title'    => 'Opinion: A second bridge is the right call, but not on that route',
			'category' => 'opinion',
			'access'   => 'members',
			'byline'   => 'The Editorial Board',
			'dek'      => 'The council should keep the money and move the landing away from Lowmarket’s only park.',
			'public'   => [
				'The council was right to fund a second harbor bridge. The current span is a single point of failure for the whole east side, and the cost of doing nothing rises every year.',
				'But the route it chose would land the bridge on top of Lowmarket Green, the only park in a neighborhood that has already given up more than its share for the region’s roads.',
			],
			'locked'   => [
				'The design contract should require a full study of the northern alignment through the rail yard. It is longer and more expensive, but it would not take a single home or a single tree from Lowmarket.',
				'A bridge is a hundred-year decision. Spending an extra year to land it in the right place is worth it.',
			],
		],
		'opinion-local-news'        => [
			'title'    => 'Opinion: Why we are asking you to become a member',
			'category' => 'opinion',
			'access'   => 'free',
			'byline'   => 'The Editorial Board',
			'dek'      => 'Most of what we publish stays free. Membership pays for the stories that take months.',
			'public'   => [
				'Most of the Ledger stays free to read, and it always will: council votes, transit, weather, the night market. Those stories belong to everyone in the city.',
				'Some stories take months, a records request and a lawyer’s review. The port deal and the utility filing are two of them. Members pay for that work, and members read it first.',
				'Silver membership opens our culture and features desk. Gold opens investigations. Members with both get everything we publish. You can join on the Become a member page.',
			],
			'locked'   => [],
		],
	];
}

/**
 * Seed items for the stories, in the shape sfwp_test_seed_items() returns.
 * Stories are dated a day apart, newest first, so the home page is stable.
 *
 * @return array<string, array<string, mixed>>
 */
function sfwp_test_news_seed_items(): array {
	$items = [];
	$day   = 0;

	foreach ( sfwp_test_news_stories() as $slug => $story ) {
		$items[ 'news-' . $slug ] = [
			'type'     => 'post',
			'title'    => $story['title'],
			'slug'     => $slug,
			'content'  => static fn() => sfwp_test_story_markup( $slug, $story ),
			'excerpt'  => $story['dek'],
			'category' => $story['category'],
			'date'     => gmdate( 'Y-m-d H:i:s', strtotime( 'today 08:00' ) - ( $day++ * DAY_IN_SECONDS ) ),
			'meta'     => [
				SFWP_TEST_ACCESS_META => $story['access'],
				SFWP_TEST_BYLINE_META => $story['byline'],
			],
		];
	}

	return $items;
}

function sfwp_test_paragraphs( array $paragraphs ): string {
	$out = '';

	foreach ( $paragraphs as $text ) {
		$out .= "<!-- wp:paragraph -->\n<p>" . esc_html( $text ) . "</p>\n<!-- /wp:paragraph -->\n\n";
	}

	return $out;
}

/**
 * Block markup for one story. Free stories are plain paragraphs. The others
 * put the locked paragraphs, plus a leak marker, in the gate's members area
 * and a sign-up teaser in its public area.
 */
function sfwp_test_story_markup( string $slug, array $story ): string {
	$markup = sfwp_test_paragraphs( $story['public'] );

	if ( 'free' === $story['access'] || ! $story['locked'] ) {
		return trim( $markup );
	}

	$level = sfwp_test_access_levels()[ $story['access'] ];
	$attrs = [
		'matchMode'    => 'all' === $level['match']
			? [ 'value' => 'all', 'label' => 'All of these tags' ]
			: [ 'value' => 'any', 'label' => 'Any of these tags' ],
		'requireLogin' => true,
		'fallbackMode' => [ 'value' => 'teaser-blocks', 'label' => 'The Public teaser area' ],
	];

	if ( $level['tags'] ) {
		$attrs = [ 'requiredTags' => array_map( static fn( $tag ) => [ 'tag' => $tag ], $level['tags'] ) ] + $attrs;
	}

	$who = $level['tags']
		/* translators: %s: membership level, e.g. Gold */
		? sprintf( __( '%s members', 'sfwp-test' ), $level['label'] )
		: __( 'signed-in members', 'sfwp-test' );

	$marker = 'SFWP-SECRET-NEWS-' . strtoupper( $slug );

	$markup .= '<!-- wp:wp2-shopify/membership-gate ' . wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES ) . " -->\n";
	$markup .= "<!-- wp:wp2-shopify/membership-gate-content -->\n";
	$markup .= sfwp_test_paragraphs( $story['locked'] );
	$markup .= "<!-- wp:paragraph {\"className\":\"sfwp-marker\"} -->\n<p class=\"sfwp-marker\">Leak marker: " . esc_html( $marker ) . "</p>\n<!-- /wp:paragraph -->\n";
	$markup .= "<!-- /wp:wp2-shopify/membership-gate-content -->\n";
	$markup .= "<!-- wp:wp2-shopify/membership-gate-fallback -->\n";
	$markup .= "<!-- wp:group {\"className\":\"sfwp-paywall\",\"layout\":{\"type\":\"constrained\"}} -->\n<div class=\"wp-block-group sfwp-paywall\">\n";
	$markup .= "<!-- wp:paragraph {\"className\":\"sfwp-paywall__title\"} -->\n<p class=\"sfwp-paywall__title\">" . esc_html__( 'Keep reading with a membership', 'sfwp-test' ) . "</p>\n<!-- /wp:paragraph -->\n";
	/* translators: %s: who can read the story, e.g. "Gold members" */
	$markup .= "<!-- wp:paragraph -->\n<p>" . esc_html( sprintf( __( 'The rest of this story is for %s. Sign in if you already are one, or see what membership includes.', 'sfwp-test' ), $who ) ) . "</p>\n<!-- /wp:paragraph -->\n";
	$markup .= "<!-- wp:group {\"layout\":{\"type\":\"flex\",\"flexWrap\":\"wrap\"}} -->\n<div class=\"wp-block-group\">\n";
	$markup .= "<!-- wp:wp2-shopify/auth-action {\"mode\":{\"value\":\"login\",\"label\":\"Log in only (hidden when signed in)\"}} /-->\n";
	$markup .= "<!-- wp:paragraph -->\n<p><a href=\"/join/\">" . esc_html__( 'Become a member', 'sfwp-test' ) . "</a></p>\n<!-- /wp:paragraph -->\n";
	$markup .= "</div>\n<!-- /wp:group -->\n";
	$markup .= "<!-- wp:sfwp-test/resync {\"compact\":true} /-->\n";
	$markup .= "</div>\n<!-- /wp:group -->\n";
	$markup .= "<!-- /wp:wp2-shopify/membership-gate-fallback -->\n";
	$markup .= "<!-- /wp:wp2-shopify/membership-gate -->";

	return trim( $markup );
}
