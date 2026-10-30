# RedeemID Demo theme

A demo and test theme for the [RedeemID](https://github.com/webmultipliers/redeemid-core) plugin. Install it on a development or staging site to see every plugin feature working and to test changes against it:

- **The Harborview Ledger**, a fictional news site with free and paywalled stories, demo post types and taxonomies, and section rules.
- **A Test lab** with one page per plugin feature, and paywalled content with marker phrases for leak checks.
- **Test accounts** in every membership state, to switch between with [User Switching](https://wordpress.org/plugins/user-switching/).

It is a block theme that only consumes the plugin: every access decision, block and setting is the plugin's. It is not meant for a production site, because it creates pages, posts and user accounts on activation.

## Install

1. Install and configure the RedeemID plugin (see its README).
2. Copy this repository into `wp-content/themes/`, or have your host sync it from Git and run `composer build-prod` after each sync. `build-prod` runs `composer install --no-dev`, like the plugin. The theme has no Composer dependencies, so it only writes an empty `vendor/` autoloader (ignored by git).
3. Activate it: Appearance → Themes → RedeemID Demo (or `wp theme activate <folder>`).
4. Optional: install [User Switching](https://wordpress.org/plugins/user-switching/) to sign in as the test accounts.

## What happens on activation

On the first request with the theme active, it creates the test content from its `patterns/` files and `inc/news-content.php`, creates the test accounts, and makes the Ledger home page the front page. It never overwrites existing pages on its own.

| Address | What it tests |
|---|---|
| `/` (Home) | The Ledger front page: lead story and four seconds, the latest list, how membership works (one tile per access level), Ledger Reports, one row per section, briefings, city guides and corrections |
| `/about/` | About the Ledger: what is free and what is for members, and how corrections work |
| `/briefings/`, `/reports/`, `/guides/`, `/corrections/` | The demo post types (see [Content types](#content-types)), each with its own archive and single template |
| `/category/<section>/` | Investigations, City Hall, Business, Technology, Science, Culture, Opinion |
| `/test-lab/` | Site health, account status, the test-account matrix, the feature table and the list of things to try |
| `/auth-callback/` | The plugin's sign-in callback page (follows the `redeemid_auth_callback_slug` filter) |
| `/sign-in-controls/` | `redeemid/auth-action` in every mode and style |
| `/members-area/` | A gate that admits any signed-in visitor |
| `/tag-gates/` | Tag rules: any, all, teaser fallback, a tag nobody has, an open control, and a Shopify Product block inside a gold gate (marker `LEDGER-SECRET-PRODUCT`) |
| `/gate-redirect/` | Redirect fallback mode |
| `/join/` | Where calls to action point: a reader-facing membership page (what each level opens, Gold and Silver plan cards with handles `gold-membership`, `silver-membership`; Gold members see a thank-you, their account link and the Silver add-on instead), then for testers how a test customer gets a tag |
| `/shop/` | RedeemID's storefront: the `frontpage` collection with Add to cart; the header's Cart link, `/cart/` and `/products/{handle}/` come from the plugin |
| `/account/` | The plugin's Member Account card (levels and dates, renewal, past levels, features, last sync, Shopify account links, Download my data and Delete my data), then the Membership Features list, connect and log out |
| `/members-only-article/` | A paywalled post with the phrase `LEDGER-SECRET-MEMBERS-ONLY` for leak checks |

The tag gates use `gold-member` and `silver-member`. Add those tags to a test customer in Shopify, or edit the gates.

## The Harborview Ledger

Sixteen fictional stories across seven sections. Each has an access level. The badge on cards and stories is the plugin's **Access Badge** (`redeemid/access-badge`), laid out with the byline by the theme's `redeemid-demo/story-meta` block. The block sets the badge's free label, so open stories read **Free** and every list shows free and paid stories side by side; cards of stories that lock the visitor out also get a small lock before the title (the plugin's `redeemid-gated is-locked` post classes):

| Badge | Gate on the story body (a plugin access level) |
|---|---|
| Free | none |
| Signed in | the plugin's built-in `signed-in` level: any signed-in visitor |
| Silver | level `silver` (any of `silver-member`) |
| Gold | level `gold` (any of `gold-member`) |
| Gold + Silver | level `premium` (all of `gold-member`, `silver-member`) |

Paywalled stories keep their public paragraphs outside a `redeemid/membership-gate` set to that level, with a teaser fallback (sign in, Become a member, Refresh membership). The gate names a level rather than tags so the plugin's badge can name it too. The members-only part ends with a leak marker `LEDGER-SECRET-NEWS-<SLUG>`, so the existing `/?s=LEDGER-SECRET`, feed and REST leak checks cover them. Each story has an explicit excerpt, so listings never draw on the gated body.

### Section rules

The three Investigations stories have no gate block. The plugin's content rules (RedeemID → Access) lock them by section, and a paywall break block marks where the teaser ends. Seeding sets the rules up when the plugin has them:

| Rule | Effect |
|---|---|
| Levels `gold` (`gold-member`), `silver` (`silver-member`) and `premium` (both) | Added only if the site has no level with that slug |
| Investigations needs Gold | *Harbor pilots billed 14,000 overtime hours*: Gold readers only |
| Culture needs Silver | Culture stories need Silver even without a gate |
| A story in Investigations and Culture | *The arts council's grant audit* needs both (most restrictive), so only Gold + Silver readers |
| Tag "Free to read", free for 30 days | *How a ferry fuel contract went to a company with no ships* is open to everyone; its marker is `LEDGER-OPEN-NEWS-…` |
| The night market story's own rule: Public | Stays free inside the Silver Culture section |

The plugin's badge reads their rule, so it changes when the rule does: the ferry story's badge turns to Gold when its 30 days end. Restore test content starts the 30 days again and puts every rule back; plain seeding leaves a term rule someone changed alone.

## Content types

The plugin gates every public post type, so the theme registers demo types and taxonomies to stress the content rules beyond posts and categories (`inc/content-types.php`). They are fixtures, registered on `init` at priority 5 so the plugin sees them; every access decision is the plugin's.

| Post type | Archive | Plugin post type row (RedeemID → Access → Content rules) |
|---|---|---|
| Briefings `ledger_briefing` | `/briefings/` | needs Signed in; teaser 40 words |
| Reports `ledger_report` | `/reports/` | needs Gold; teaser 60 words; open after 365 days; locked reports kept out of search engines |
| City guides `ledger_guide` | `/guides/` | no levels (open unless a term or item says otherwise); comments shown to everyone |
| Corrections `ledger_notice` | `/corrections/` | gating switched off: no rule of any kind applies |

| Taxonomy | On | Term rules |
|---|---|---|
| Editions `ledger_edition` (flat) | Briefings | Weekend opens its items for 30 days |
| Series `ledger_series` (hierarchical, inherits) | Reports | Harbor Money needs Gold + Silver and passes it to Port budgets; Pensions has its own rule, Silver |
| Neighborhoods `ledger_neighborhood` (flat) | City guides | Fennel Hill needs Silver |
| Topics `ledger_topic` (flat, shared) | posts and all four types | Elections 2026 needs Signed in |

Sixteen items exercise each layer: the post type rule, an Opens term, a hierarchical term inherited from its parent, a child's own rule beating its parent's, a flat term on a type with no rule, a custom taxonomy on core posts, item Public, item Level with no other rule, item Add to inherited, an item free until a date, an embargo, the post type's archive window, a teaser cut by word count instead of a break block, and a type with gating off. The Test lab page lists each item with the layer that should decide it and the badge a guest should see. Locked text ends with a `LEDGER-SECRET-CPT-<SLUG>` marker; text that should be open to everyone carries `LEDGER-OPEN-CPT-<SLUG>`.

Seeding adds the post type rows, term rules and item rules only where none exist; Restore test content puts them all back and restarts the dated windows. New types need fresh rewrite rules, which the theme flushes once per content version on `wp_loaded`.

## Test accounts

The theme seeds these subscribers (`inc/fixtures.php`), marked with user meta `_redeemid_demo_fixture`:

| Login | State |
|---|---|
| `ledger-reader` | WordPress only, not linked |
| `ledger-member` | linked, no tags |
| `ledger-silver` | `silver-member` |
| `ledger-gold` | `gold-member` |
| `ledger-premium` | `gold-member` + `silver-member` |
| `ledger-lapsed` | `gold-member`, Shopify account disabled |
| `ledger-stale` | `gold-member`, last synced 30 days ago (still reads Gold: synced tags never expire) |
| `ledger-addon` | `silver-member` + `ad-free-addon` (a standalone add-on tag) |

Linked accounts get a made-up customer ID (990000000001 and up) and a synced mirror written into the plugin's user meta, so the gate treats them like real customers. Because they are not real customers, the theme returns true from the plugin's `redeemid_customer_is_fixture` filter (resync skips them), adds a "Test fixture" badge through `redeemid_customer_badges`, and puts their state back on User Switching's `switch_to_user` and when the plugin's membership refresh runs for them (it answers the plugin's `redeemid_pre_sync_now` filter, so Shopify is never asked about a made-up customer). RedeemID → Test lab can point any linked account at a real dev-store customer; it is then synced from Shopify like any other user.

With User Switching active, administrators get a **Test accounts** toolbar menu on every page, and the menu offers **Switch back** while switched in.

## Features

The theme registers two demo features with the plugin's `redeemid_features` filter (`inc/features.php`). It only registers them and places blocks; the plugin decides who holds each one, server-side, from the synced mirror:

| Feature | Who holds it | Where it shows |
|---|---|---|
| **Ad-free reading** (`ad-free`) | Gold, or the `ad-free-addon` tag (stacks with any tier). Mapped on RedeemID → Access → Features and seeded once (Restore puts it back) | The sponsor slot in `templates/single.html` and `page-home.html` uses the block **Access → Feature: does not hold it**, so holders never receive it. Holders also get the `redeemid-feature-ad-free` body class (the masthead says "Ad-free") and the JS signal |
| **Early briefing** (`early-briefing`) | Silver or Gold, set in code (shown as managed in code) | The Briefings archive: tomorrow's briefing (marker `LEDGER-FEATURE-EARLY-BRIEFING`) for holders, a prompt for everyone else |

Both are listed as primary, so the header shows them as chips (the plugin's **Membership Features** block in menu mode), and the Account page lists them with where each comes from. The Test lab has the expected result for every test account.

## Plugin features the theme uses

The theme builds no features of its own; it places the plugin's blocks and answers the plugin's hooks:

| Where | Plugin block or hook |
|---|---|
| Header, Join page, every paywall teaser | **Refresh Membership** (`redeemid/membership-refresh`) |
| Header, Account | **Membership Features** (`redeemid/member-features`), menu mode in the header |
| Account | **Member Account** (`redeemid/member-account`) |
| Join | **Membership Gate** on the Gold level, with the plan cards in its public area |
| Sponsor slot, Briefings | the plugin's per-block **Access → Feature** setting and the feature registry (`redeemid_features`) |
| Test lab, Account, callback, Members area, Join, Sign-in controls | **Shopify Account Status** (`redeemid/account-status`) |
| Test lab | **Shopify Health** (`redeemid/health`, administrators only), with the theme's rows added through `redeemid_health_checks` (theme block registered, seeded blocks match their fields, test content version) |
| Story cards and single stories of every type | **Access Badge** (`redeemid/access-badge`) with its free label, inside `redeemid-demo/story-meta`; the `redeemid-gated`/`is-locked` post classes for the lock on card titles |
| Locked items of every type | the plugin's locked panel and level wall copy (message, call to action), styled as the Ledger's paywall |
| Stories, Test lab pages | Membership Gate, paywall break, Shopify Sign-in, Plan Card |
| Test accounts | `redeemid_customer_is_fixture`, `redeemid_customer_badges`, `redeemid_pre_sync_now` |
| Shopify's cart toggle | keyboard access comes from the plugin (`Integrations\OfficialCartToggle`) |

## Shopify's WordPress plugin

The theme no longer needs Shopify's own plugin (Sell on WordPress): RedeemID's product, collection, cart and cart link blocks replace it, and the demo is meant to run without it. If it is still active:

- By default RedeemID stands down on the paths it claims (`/cart`, `/products`, `/collections`): the header's Cart link hides and the shop's buttons say Buy now. Turn on **RedeemID takes precedence** under RedeemID → Settings → Storefront pages, or deactivate Shopify's plugin and save permalinks.
- The navigation keeps `ignoredHookedBlocks` so its cart toggle doesn't add an extra row after the navigation.
- The plugin's health block shows who answers each storefront path and whether the cart sign-in bridge is active.

## Admin

RedeemID → Test lab (Appearance → Test lab when the plugin is not active) lists the items and has **Restore test content**, which rewrites them from the theme's current patterns (edits are lost). To add a new seeded item, add a pattern, list it in `redeemid_demo_seed_items()` and bump `REDEEMID_DEMO_CONTENT_VERSION`; sites pick it up on their next request.

## Theme blocks

One Blockstudio block in `blockstudio/`, registered through the Blockstudio copy the plugin bundles (Blockstudio's default theme folder, which the plugin leaves intact):

- **Story badge and byline** (`redeemid-demo/story-meta`): lays out the plugin's Access Badge for the current story next to its seeded byline, in query loops and on single posts. Access decisions and labels are the plugin's; the Ledger's badge colours are in `assets/theme.css`, keyed on the badge's `data-redeemid-level`.

## What stays in the theme, and why

Everything in `inc/` is demo content or test tooling, not a feature a real site would want from the plugin:

- `news-content.php`, `content-types.php`, `test-content.php`, `patterns/`: the Ledger and Test lab content, the demo post types and taxonomies, the plugin levels and section rules they need (set up through the plugin's own `LevelStore`, `TermRules` and `ItemRule` APIs), seeding and restore.
- `features.php`: the two demo feature registrations and the seeded Ad-free mapping (through the plugin's `Features\Registry`).
- `fixtures.php`: the eight test accounts, their simulated Shopify state, the User Switching toolbar menu, and the hook answers that keep Shopify out of it.
- `admin.php`: RedeemID → Test lab.
- `health.php`: the theme's own rows for the plugin's health block.

## Check Blockstudio first

The theme's blocks are Blockstudio blocks, like the plugin's. **Before writing code for a block, check whether Blockstudio already does it**: the docs are at [blockstudio.dev/docs](https://blockstudio.dev/docs), and in the plugin's `vendor/blockstudio/blockstudio/docs`. Hand-written versions drift from the plugin's blocks and have to be secured and maintained here. In particular:

- Anything that differs per visitor on a cacheable page is a [dynamic island](https://blockstudio.dev/docs/blocks/islands) (`"island": {"mode": "dynamic"}` in `block.json`, then `$isIslandFragment` in the template). Don't fetch a status endpoint from `script.view.js`.
- Sidebar fields use Blockstudio field types (`select`, `link`, `toggle`) with `conditions` and `populate`, not custom controls.
- Pattern markup stores values in the shape the field returns: links as `{"url": "…"}`, selects as `{"value": "…", "label": "…"}`. The theme's health row flags anything else.
- A template's `return;` tells Blockstudio the block rendered nothing, so its styles and scripts are skipped. End branches that print markup with `if`/`else` instead.
- The plugin's README section "Building on Blockstudio" lists the deliberate exceptions (REST routes rather than RPC, the Access panel).

On Test lab pages (body class `ledger-lab`) the theme stylesheet labels which gate area reached the page ("Rendered: members only area", "public teaser area" or "locked message"), so a tester can see the server's decision at a glance. The Ledger's stories show the reader view instead: the teaser fades into the paywall box.

## Requirements

WordPress 6.7+, PHP 8.2+, and the RedeemID plugin. Without the plugin the theme still renders; the plugin's blocks are simply missing.

```sh
composer lint        # php -l on every PHP file
composer phpcs       # WordPress VIP coding standards (phpcs.xml.dist)
composer build-prod  # deploy hook (composer install --no-dev)
```

## Contributing

Issues and pull requests are welcome. Work branches from `main`, and pull requests go back into it. Keep the theme a consumer of the plugin: a feature that a real site would want belongs in the plugin, and the theme only places its blocks and answers its hooks. Record changes in [CHANGELOG.md](CHANGELOG.md).

## License

GPL-2.0-or-later. See [LICENSE](LICENSE). All people, places, organisations and stories in the demo content are fictional.
