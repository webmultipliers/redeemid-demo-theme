# Changelog

## Unreleased

- README rewritten for a public audience: what the theme is for, install steps, contributing and license. Added the GPL-2.0 LICENSE file.
- Copy no longer refers to internal plugin phases (Account and Test lab pages, the search leak note).
- Join page: buying a membership is now described with Shopify Flow tagging, since the plugin no longer tags purchases itself. Seeded sites keep their existing page text.
- `composer phpcs` checks the theme against the WordPress VIP coding standards (`phpcs.xml.dist`, dev dependency only).

## 0.8.0

Demo features for the plugin's feature registry (needs the plugin's feature registry release). The theme only registers them and places the plugin's blocks; who holds each one is the plugin's decision.

- **Ad-free reading** (`ad-free`): Gold, or the standalone `ad-free-addon` tag. Mapped on Shopify → Access → Features and seeded once (Restore puts it back). The sponsor slots on stories and the home page use the block **Access → Feature: does not hold it**; holders also get the body class and the JS signal.
- **Early briefing** (`early-briefing`): Silver or Gold, set in code. The Briefings archive shows tomorrow's briefing to holders and a prompt to everyone else.
- The plugin's **Membership Features** block in the header (menu mode) and on the Account page.
- New test account Ada Add-on (`silver-member` + `ad-free-addon`) and a feature table on the Test lab page.
- Content version 7: rewrites the Account and Test lab pages once from the theme (edits to those two pages are lost).

## 0.7.0

A more complete news site, and content types to stress the plugin's content rules (needs the plugin's Access Badge free label; without it open stories simply show no badge).

- Front page: a lead with four seconds, a Latest rail, a "How the Ledger works" band with one tile per access level, a Ledger Reports band, every section, briefings, city guides and corrections.
- Masthead with today's date, a quieter sandbox link, Investigations and the three new types in the navigation, and a Become a member button. A four-column footer with the tester links set apart.
- Every badge has a colour per level, open stories read **Free**, and cards of stories that lock the visitor out get a small lock before the title.
- Paywalls look like a news site's: the teaser fades into the box, and the plugin's levels get wall copy and a call to action (filled in only where a level has none).
- The gate debugging labels show on Test lab pages only.
- Become a member is a reader-facing membership page (what each level opens, plan cards) with the tester instructions below it. New About the Ledger page.
- Demo post types Briefings, Reports, City guides and Corrections, and taxonomies Editions, Series (hierarchical), Neighborhoods and Topics (shared with posts), with sixteen items and rules on every layer. The Test lab lists which layer decides each item.
- Single and archive templates for each type, and a "Latest" row under every story.
- Content version 6: creates the new items, terms and rules, and rewrites the Home, Become a member and Test lab pages once from the theme (edits to those three pages are lost).

## 0.6.0

The theme only consumes the plugin now. Features it used to build itself moved into the WP2 Shopify plugin (needs the matching plugin release):

- Refresh membership, the visitor panel and the sandbox health checklist are the plugin's **Refresh Membership**, **Shopify Account Status** and **Shopify Health** blocks. The theme's `resync`, `visitor-state` and `sandbox-health` blocks are gone; content still using their names renders the plugin blocks (`inc/compat.php`).
- Sandbox health no longer keeps its own copy of the plugin's config rules. The plugin's block reads `Config::definitions()`; the theme adds only its own rows through `wp2_shopify_health_checks`. The checklist now shows to administrators only.
- Story badges are the plugin's Access Badge. Gated stories ask for plugin access levels (`signed-in`, `silver`, `gold`, and a new `premium` for Gold + Silver) instead of listing tags, so the badge can name the level. The theme's own access labels, `_sfwp_test_access` meta and rule-to-badge mapping are gone. Free stories have no badge, and Members stories read "Signed in".
- Test accounts answer the plugin's new `wp2_shopify_pre_sync_now` filter instead of intercepting `POST /account/sync` with `rest_pre_dispatch`.
- The keyboard fix for Shopify's cart toggle moved into the plugin.
- Content version 5: on the next request, seeded pages get the new block names and the gated news stories are rewritten once from the theme (edits to those stories are lost). Section rules are left as they are.

## 0.5.0

- Visitor state, Refresh membership and Sandbox health are Blockstudio dynamic islands. The cached page carries a neutral placeholder and Blockstudio renders the block for each visitor in one never-cached request, so the two status-fetching scripts are gone (Refresh membership keeps a small click handler) and administrator-only health rows no longer reach the cached page.
- Sandbox health checks seeded pages with `bs_validate_block()` and warns about blocks stored in an older field shape.
- Patterns store the plugin's new field shapes: link objects for the sign-in and gate redirects and the gate's call-to-action URL, and `{value, label}` for plan-card products. Existing pages keep working through the plugin's upgrade shim; Test lab → Restore rewrites them.
- Inserter previews for all theme blocks, and Story access refreshes its editor preview on save.
- README: "Check Blockstudio first".

## 0.4.0

- Investigations section with three stories locked by the plugin's content rules instead of a gate: one needs Gold by section, one needs Gold and Silver because it is also in Culture, and one is opened for 30 days by the "Free to read" tag. They use the paywall break block for their teaser.
- Seeding sets up the plugin's access levels (`gold`, `silver`, only if missing) and section rules (Investigations needs Gold, Culture needs Silver), and gives the night market story its own Public rule so it stays free.
- Story badges for rule-locked stories read the plugin's effective rule.
- Seeded items can have several categories and tags.
- The newest story is dated at least an hour ago, so seeding before 08:00 UTC no longer schedules it for later instead of publishing it.
- Content version 4.
- Tag gates case 6: the leak check now looks for a product `shopify-context`, because the instructions themselves named the product handle and so always matched.

## 0.3.0

- Join page: Gold and Silver plan cards (`wp2-shopify/plan-card`, handles `gold-membership` and `silver-membership`).
- Tag gates: case 6 puts Shopify's own product block inside a gold gate, with the leak marker `SFWP-SECRET-PRODUCT`.
- Shop page (`/shop/`) with Shopify's collection block, created only while Shopify's WordPress plugin is active.
- Shopify's cart toggle moves into the header's account group and can be reached and opened from the keyboard.
- Sandbox health: Shopify's plugin state and store match, `/cart` owner, the cart sign-in bridge, `WP2_SHOPIFY_PUBLIC_ACCESS_TOKEN`, and the plan-card block.
- Content version 3. Existing Join and Tag gates pages keep their content until Shopify → Test lab → Restore test content.
- Sandbox health no longer reports Admin app credentials as set when only the retired `WP2_SHOPIFY_ADMIN_ACCESS_TOKEN` is defined. The plugin stopped reading that constant in its admin-menu release.
- The sign-in callback page's "Back to the Test lab" link points at `/test-lab/` again; since 0.2.0 `/` is the Ledger home. Existing sites get it from Shopify → Test lab → Restore test content.
- The Test accounts toolbar menu escapes its titles.

## 0.2.0

- The Harborview Ledger: a faux news site with twelve stories in six sections, a news home page (now the front page), section archives, a news article template and a masthead header. Stories are Free, Members, Silver, Gold or Gold + Silver, gated with the plugin's membership gate and a teaser, each with a `SFWP-SECRET-NEWS-…` leak marker.
- Test accounts in seven membership states for User Switching, with a Test accounts toolbar menu, a matrix of expected results on the Test lab page, and an option to point an account at a real Shopify customer.
- Refresh membership block (`sfwp-test/resync`) in the header, on the Join page and in paywalls, using the plugin's `POST /account/sync`.
- Test lab moved under the plugin's top-level Shopify menu (falls back to Appearance with older plugins).
- Content version 2: existing sites pick up the new pages, stories and accounts on their next request.

## 0.1.4

- Version bump to redeploy the theme to the sandbox.

## 0.1.3

- `composer build-prod` now runs `composer install --no-dev`, like the plugin, with a committed `composer.lock`, so the deploy step matches the one that already works on the sandbox.

## 0.1.2

- Redeploy trigger for the sandbox sync.

## 0.1.1

- Set the Composer root version.

## 0.1.0

- First version: Test lab pages, Visitor state and Sandbox health blocks, Appearance → Test lab.
