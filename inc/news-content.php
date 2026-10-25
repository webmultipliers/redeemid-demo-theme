<?php
/**
 * The Harborview Ledger: a faux news site seeded next to the Test lab, so the
 * membership gate is exercised on realistic stories as well as test cases.
 *
 * Every story is fiction. Most paywalled stories wrap their body in a
 * redeemid/membership-gate set to one of the plugin's access levels, with
 * a teaser fallback, so the plugin's Access Badge can name the level. The
 * Investigations
 * stories have no gate at all: the plugin's content rules lock them by
 * category (redeemid_demo_seed_access_rules()), and a paywall break block marks
 * where their teaser ends. Every members-only part carries a leak marker
 * starting with LEDGER-SECRET, so the same search, feed and REST leak checks
 * cover both kinds.
 *
 * @package redeemid-demo
 */

defined( 'ABSPATH' ) || exit;

const REDEEMID_DEMO_BYLINE_META = '_redeemid_demo_byline';

/**
 * A story's access: which plugin access level its gate asks for, and how the
 * paywall copy names the readers. `free` stories have no gate and `rule`
 * stories are locked by the plugin's section rules instead.
 *
 * @return array<string, array{level: string, who: string}>
 */
function redeemid_demo_story_access(): array {
	return [
		'free'    => [ 'level' => '', 'who' => '' ],
		// The plugin's built-in Signed in level: any signed-in WordPress user.
		'members' => [ 'level' => 'signed-in', 'who' => __( 'signed-in members', 'redeemid-demo' ) ],
		'silver'  => [ 'level' => 'silver', 'who' => __( 'Silver members', 'redeemid-demo' ) ],
		'gold'    => [ 'level' => 'gold', 'who' => __( 'Gold members', 'redeemid-demo' ) ],
		'premium' => [ 'level' => 'premium', 'who' => __( 'Gold + Silver members', 'redeemid-demo' ) ],
		'rule'    => [ 'level' => '', 'who' => '' ],
	];
}

/**
 * The access levels the Ledger needs in the plugin (RedeemID → Access). Each
 * is added only if the site has no level with that slug.
 *
 * @return array<string, array{label: string, groups: array<int, array{tags: string[], match: string}>}>
 */
function redeemid_demo_plugin_levels(): array {
	return [
		'gold'    => [
			'label'     => 'Gold',
			'groups'    => [ [ 'tags' => [ 'gold-member' ], 'match' => 'any' ] ],
			'message'   => __( 'This is a Gold story. Gold members read every investigation and Ledger Report in full.', 'redeemid-demo' ),
			'cta_label' => __( 'Join Gold', 'redeemid-demo' ),
			'cta_url'   => '/join/',
		],
		'silver'  => [
			'label'     => 'Silver',
			'groups'    => [ [ 'tags' => [ 'silver-member' ], 'match' => 'any' ] ],
			'message'   => __( 'This is a Silver story. Silver members read our culture desk, features and neighborhood guides.', 'redeemid-demo' ),
			'cta_label' => __( 'Join Silver', 'redeemid-demo' ),
			'cta_url'   => '/join/',
		],
		'premium' => [
			'label'     => 'Gold + Silver',
			'groups'    => [ [ 'tags' => [ 'gold-member', 'silver-member' ], 'match' => 'all' ] ],
			'message'   => __( 'This story is for members with both Gold and Silver: everything the Ledger publishes.', 'redeemid-demo' ),
			'cta_label' => __( 'See membership', 'redeemid-demo' ),
			'cta_url'   => '/join/',
		],
	];
}

/**
 * @return array<string, string> Category slug => name.
 */
function redeemid_demo_news_categories(): array {
	return [
		'investigations' => __( 'Investigations', 'redeemid-demo' ),
		'city-hall'  => __( 'City Hall', 'redeemid-demo' ),
		'business'   => __( 'Business', 'redeemid-demo' ),
		'technology' => __( 'Technology', 'redeemid-demo' ),
		'science'    => __( 'Science', 'redeemid-demo' ),
		'culture'    => __( 'Culture', 'redeemid-demo' ),
		'opinion'    => __( 'Opinion', 'redeemid-demo' ),
	];
}

function redeemid_demo_term_id( string $slug ): int {
	$term = get_term_by( 'slug', $slug, 'category' );

	return $term instanceof WP_Term ? (int) $term->term_id : 0;
}

/**
 * Create the news categories that are missing.
 */
function redeemid_demo_seed_categories(): void {
	foreach ( redeemid_demo_news_categories() as $slug => $name ) {
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
function redeemid_demo_news_stories(): array {
	return [
		'harbor-pilot-overtime'     => [
			'title'    => 'Harbor pilots billed 14,000 overtime hours last year. The port never checked.',
			'category' => 'investigations',
			'access'   => 'rule',
			'byline'   => 'Marcus Feld',
			'dek'      => 'Timesheets obtained through a records request show the same shifts claimed twice.',
			'public'   => [
				'The Harborview Port Authority paid its harbor pilots for more than 14,000 hours of overtime last year, according to timesheets the Ledger obtained through a public records request.',
				'Nobody at the authority compared those hours with the ship logs that record when a pilot actually boarded a vessel.',
			],
			'locked'   => [
				'The Ledger matched the two sets of records for all of 2025. In 312 cases, the same pilot claimed overtime for two ships whose arrival windows overlapped.',
				'The authority’s finance director said the timesheets are approved by the pilots’ own association under a contract signed in 2011, and that the port has no right to audit them.',
				'Two council members said they would ask for the contract to be reopened before it renews next year.',
			],
		],
		'ferry-fuel-contract'       => [
			'title'    => 'How a ferry fuel contract went to a company with no ships',
			'category' => 'investigations',
			'tags'     => [ 'Free to read' ],
			'access'   => 'rule',
			'marker'   => 'LEDGER-OPEN-NEWS-FERRY-FUEL-CONTRACT',
			'byline'   => 'Dana Okafor',
			'dek'      => 'Free to read this month: our investigation into the city’s largest no-bid deal.',
			'public'   => [
				'In March the city’s ferry division signed a five-year, $19 million fuel contract without asking for bids. The supplier, Keel Line Energy, had been incorporated eleven weeks earlier.',
			],
			'locked'   => [
				'Keel Line owns no tankers and no storage. It buys fuel from the same distributor the city used before, and resells it to the city at a markup of nine cents a gallon.',
				'The division’s former director, who approved the deal as an emergency purchase, now works for Keel Line as a consultant.',
				'We are making this investigation free to read for a month because every ferry rider pays for it.',
			],
		],
		'arts-grant-audit'          => [
			'title'      => 'The arts council’s grant audit, and the galleries it never visited',
			'category'   => 'investigations',
			'categories' => [ 'investigations', 'culture' ],
			'access'     => 'rule',
			'byline'     => 'Priya Raman',
			'dek'        => 'An investigation for our culture desk: half the audited venues were empty storefronts.',
			'public'     => [
				'The Harborview Arts Council says it audits every gallery that receives a public grant. Its 2025 report lists forty-two site visits.',
				'The Ledger went to all forty-two addresses.',
			],
			'locked'     => [
				'Twenty-one were open galleries. Nine were empty storefronts, six were private homes and six did not exist.',
				'The council’s director said the audits were done from paperwork and the report should not have called them site visits.',
			],
		],
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
			'category'  => 'culture',
			'access'    => 'free',
			// Culture needs Silver by section rule; this story opens itself with the item's own rule.
			'item_rule' => [ 'mode' => 'public' ],
			'byline'    => 'Priya Raman',
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
 * Seed items for the stories, in the shape redeemid_demo_seed_items() returns.
 * Stories are dated a day apart, newest first, so the home page is stable.
 *
 * @return array<string, array<string, mixed>>
 */
function redeemid_demo_news_seed_items(): array {
	$items = [];
	$day   = 0;
	// Never in the future: a story dated later today would be scheduled, not published.
	$base = min( strtotime( 'today 08:00' ), time() - HOUR_IN_SECONDS );

	foreach ( redeemid_demo_news_stories() as $slug => $story ) {
		$items[ 'news-' . $slug ] = [
			'type'       => 'post',
			'title'      => $story['title'],
			'slug'       => $slug,
			'content'    => static fn() => redeemid_demo_story_markup( $slug, $story ),
			'excerpt'    => $story['dek'],
			'categories' => $story['categories'] ?? [ $story['category'] ],
			'tags'       => $story['tags'] ?? [],
			'date'       => gmdate( 'Y-m-d H:i:s', $base - ( $day++ * DAY_IN_SECONDS ) ),
			'meta'       => [
				REDEEMID_DEMO_BYLINE_META => $story['byline'],
			] + ( isset( $story['item_rule'] ) ? [ '_redeemid_access' => $story['item_rule'] ] : [] ),
		];
	}

	return $items;
}

function redeemid_demo_paragraphs( array $paragraphs ): string {
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
function redeemid_demo_story_markup( string $slug, array $story ): string {
	$markup = redeemid_demo_paragraphs( $story['public'] );

	if ( 'rule' === $story['access'] ) {
		// No gate: the plugin's content rules decide, and the break marks where the teaser ends.
		$marker  = $story['marker'] ?? 'LEDGER-SECRET-NEWS-' . strtoupper( $slug );
		$markup .= "<!-- wp:redeemid/paywall-break /-->\n\n";
		$markup .= redeemid_demo_paragraphs( $story['locked'] );
		$markup .= "<!-- wp:paragraph {\"className\":\"ledger-marker\"} -->\n<p class=\"ledger-marker\">Leak marker: " . esc_html( $marker ) . "</p>\n<!-- /wp:paragraph -->";

		return trim( $markup );
	}

	if ( 'free' === $story['access'] || ! $story['locked'] ) {
		return trim( $markup );
	}

	$access = redeemid_demo_story_access()[ $story['access'] ];
	$attrs  = [
		'ruleSource'   => [ 'value' => 'level', 'label' => 'An access level' ],
		'level'        => [ 'value' => $access['level'], 'label' => redeemid_demo_plugin_levels()[ $access['level'] ]['label'] ?? 'Signed in' ],
		'fallbackMode' => [ 'value' => 'teaser-blocks', 'label' => 'The Public teaser area' ],
	];
	$who    = $access['who'];

	$marker = 'LEDGER-SECRET-NEWS-' . strtoupper( $slug );

	$markup .= '<!-- wp:redeemid/membership-gate ' . wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES ) . " -->\n";
	$markup .= "<!-- wp:redeemid/membership-gate-content -->\n";
	$markup .= redeemid_demo_paragraphs( $story['locked'] );
	$markup .= "<!-- wp:paragraph {\"className\":\"ledger-marker\"} -->\n<p class=\"ledger-marker\">Leak marker: " . esc_html( $marker ) . "</p>\n<!-- /wp:paragraph -->\n";
	$markup .= "<!-- /wp:redeemid/membership-gate-content -->\n";
	$markup .= "<!-- wp:redeemid/membership-gate-fallback -->\n";
	$markup .= "<!-- wp:group {\"className\":\"ledger-paywall\",\"layout\":{\"type\":\"constrained\"}} -->\n<div class=\"wp-block-group ledger-paywall\">\n";
	$markup .= "<!-- wp:paragraph {\"className\":\"ledger-paywall__title\"} -->\n<p class=\"ledger-paywall__title\">" . esc_html__( 'Keep reading with a membership', 'redeemid-demo' ) . "</p>\n<!-- /wp:paragraph -->\n";
	/* translators: %s: who can read the story, e.g. "Gold members" */
	$markup .= "<!-- wp:paragraph -->\n<p>" . esc_html( sprintf( __( 'The rest of this story is for %s. Sign in if you already are one, or see what membership includes.', 'redeemid-demo' ), $who ) ) . "</p>\n<!-- /wp:paragraph -->\n";
	$markup .= "<!-- wp:group {\"layout\":{\"type\":\"flex\",\"flexWrap\":\"wrap\"}} -->\n<div class=\"wp-block-group\">\n";
	$markup .= "<!-- wp:redeemid/auth-action {\"mode\":{\"value\":\"login\",\"label\":\"Log in only (hidden when signed in)\"}} /-->\n";
	$markup .= "<!-- wp:paragraph -->\n<p><a href=\"/join/\">" . esc_html__( 'Become a member', 'redeemid-demo' ) . "</a></p>\n<!-- /wp:paragraph -->\n";
	$markup .= "</div>\n<!-- /wp:group -->\n";
	$markup .= "<!-- wp:redeemid/membership-refresh {\"compact\":true} /-->\n";
	$markup .= "</div>\n<!-- /wp:group -->\n";
	$markup .= "<!-- /wp:redeemid/membership-gate-fallback -->\n";
	$markup .= "<!-- /wp:redeemid/membership-gate -->";

	return trim( $markup );
}

/**
 * The plugin's content rules for the Ledger, when the plugin has them:
 *
 * - Levels gold (gold-member), silver (silver-member) and premium (both),
 *   added only if missing (redeemid_demo_plugin_levels()). The gated stories ask
 *   for these levels too.
 * - Investigations needs Gold. Culture needs Silver. A story in both needs
 *   both, because terms combine most-restrictively by default.
 * - The "Free to read" tag opens its stories to everyone for 30 days from
 *   seeding. Restore test content starts the 30 days again.
 * - The night market story stays free through its own Public rule, also on
 *   sites that seeded it before the rules existed.
 *
 * Without $restore, a term that already has a rule keeps it.
 */
function redeemid_demo_seed_access_rules( bool $restore ): void {
	if ( ! class_exists( \RedeemID\Access\TermRules::class ) || ! class_exists( \RedeemID\Access\LevelStore::class ) ) {
		return;
	}

	$saved = \RedeemID\Access\LevelStore::saved();

	foreach ( redeemid_demo_plugin_levels() as $slug => $level ) {
		if ( ! isset( $saved[ $slug ] ) ) {
			\RedeemID\Access\LevelStore::save( $slug, $level );
			continue;
		}

		// A level seeded before the wall copy existed gets it, without touching anything an administrator set.
		$fill = array_filter( array_intersect_key( $level, array_flip( [ 'message', 'cta_label', 'cta_url' ] ) ), static fn( $v, $k ) => '' === trim( (string) ( $saved[ $slug ][ $k ] ?? '' ) ), ARRAY_FILTER_USE_BOTH );

		if ( $fill ) {
			\RedeemID\Access\LevelStore::save( $slug, $fill + $saved[ $slug ] );
		}
	}

	$free = term_exists( 'free-to-read', 'post_tag' ) ?: wp_insert_term( __( 'Free to read', 'redeemid-demo' ), 'post_tag', [ 'slug' => 'free-to-read' ] );
	$free = is_array( $free ) ? (int) $free['term_id'] : 0;
	$set  = [
		redeemid_demo_term_id( 'investigations' ) => [ 'kind' => 'requires', 'levels' => [ 'gold' ] ],
		redeemid_demo_term_id( 'culture' )        => [ 'kind' => 'requires', 'levels' => [ 'silver' ] ],
		$free                                 => [ 'kind' => 'opens', 'opens_until' => time() + 30 * DAY_IN_SECONDS ],
	];

	foreach ( $set as $term_id => $rule ) {
		if ( $term_id && ( $restore || '' === \RedeemID\Access\TermRules::get( $term_id )['kind'] ) ) {
			\RedeemID\Access\TermRules::save( $term_id, $rule );
		}
	}

	// Stories seeded before the rules existed get their own rule too, or Culture's would lock the night market.
	foreach ( redeemid_demo_news_stories() as $slug => $story ) {
		$post = isset( $story['item_rule'] ) ? redeemid_demo_find( 'news-' . $slug ) : null;

		if ( $post && ( $restore || '' === get_post_meta( $post->ID, '_redeemid_access', true ) ) ) {
			\RedeemID\Access\ItemRule::save( $post->ID, $story['item_rule'] );
		}
	}

	redeemid_demo_seed_cpt_rules( $restore );
}
