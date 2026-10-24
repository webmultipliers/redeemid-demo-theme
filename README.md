# Shopify for WP Test theme

A block theme for the WP2 Shopify sandbox (`https://shopify-for-wp.instawp.site`). It gives the plugin ([webmultipliers/shopify-for-wp](https://github.com/webmultipliers/shopify-for-wp)) a test experience: **The Harborview Ledger**, a faux news site with free and paywalled stories, a Test lab with one page per feature, a paywalled post for leak checks, and test accounts to switch between with [User Switching](https://wordpress.org/plugins/user-switching/).

## Deploy

InstaWP syncs this repository like the plugin and runs `composer build-prod` after each sync. `build-prod` runs `composer install --no-dev`, exactly like the plugin. The theme has no Composer dependencies, so it only writes an empty `vendor/` autoloader (ignored by git).

Activate it once: Appearance → Themes → Shopify for WP Test (or `wp theme activate <folder>`).

## What happens on activation

On the first request with the theme active, it creates the test content from its `patterns/` files and `inc/news-content.php`, creates the test accounts, and makes the Ledger home page the front page (also when upgrading a site whose front page was the Test lab). It never overwrites existing pages on its own.

| Address | What it tests |
|---|---|
| `/` (Home) | The Ledger front page: lead story, latest list, one row per section |
| `/category/<section>/` | Investigations, City Hall, Business, Technology, Science, Culture, Opinion |
| `/test-lab/` | Sandbox health, visitor state, the test-account matrix and the list of things to try |
| `/auth-callback/` | The plugin's sign-in callback page (follows the `wp2_shopify_auth_callback_slug` filter) |
| `/sign-in-controls/` | `wp2-shopify/auth-action` in every mode and style |
| `/members-area/` | A gate that admits any signed-in visitor |
| `/tag-gates/` | Tag rules: any, all, teaser fallback, a tag nobody has, an open control, and Shopify's own product block inside a gold gate (marker `SFWP-SECRET-PRODUCT`) |
| `/gate-redirect/` | Redirect fallback mode |
| `/join/` | Where calls to action point: Gold and Silver plan cards (handles `gold-membership`, `silver-membership`) and how a test customer gets a tag |
| `/shop/` | Shopify's own collection block. Created only while Shopify's WordPress plugin is active |
| `/account/` | Connect and log out, until the plugin's account card ships |
| `/members-only-article/` | A paywalled post with the phrase `SFWP-SECRET-MEMBERS-ONLY` for leak checks |

The tag gates use `gold-member` and `silver-member`. Add those tags to a test customer in Shopify, or edit the gates.

## The Harborview Ledger

Fifteen fictional stories across seven sections. Each has an access level, shown as a badge by the `sfwp-test/story-meta` block:

| Badge | Gate on the story body |
|---|---|
| Free | none |
| Members | any signed-in visitor (no tags) |
| Silver | any of `silver-member` |
| Gold | any of `gold-member` |
| Gold + Silver | all of `gold-member`, `silver-member` |

Paywalled stories keep their public paragraphs outside a `wp2-shopify/membership-gate` with a teaser fallback (sign in, Become a member, Refresh membership). The members-only part ends with a leak marker `SFWP-SECRET-NEWS-<SLUG>`, so the existing `/?s=SFWP-SECRET`, feed and REST leak checks cover them. Each story has an explicit excerpt, so listings never draw on the gated body.

### Section rules

The three Investigations stories have no gate block. The plugin's content rules (Shopify → Access) lock them by section, and a paywall break block marks where the teaser ends. Seeding sets the rules up when the plugin has them:

| Rule | Effect |
|---|---|
| Levels `gold` (`gold-member`) and `silver` (`silver-member`) | Added only if the site has no level with that slug |
| Investigations needs Gold | *Harbor pilots billed 14,000 overtime hours*: Gold readers only |
| Culture needs Silver | Culture stories need Silver even without a gate |
| A story in Investigations and Culture | *The arts council's grant audit* needs both (most restrictive), so only Gold + Silver readers |
| Tag "Free to read", free for 30 days | *How a ferry fuel contract went to a company with no ships* is open to everyone; its marker is `SFWP-OPEN-NEWS-…` |
| The night market story's own rule: Public | Stays free inside the Silver Culture section |

Their badge reads the plugin's rule, so it changes when the rule does: the ferry story's badge turns to Gold when its 30 days end. Restore test content starts the 30 days again and puts every rule back; plain seeding leaves a term rule someone changed alone.

## Test accounts

The theme seeds these subscribers (`inc/fixtures.php`), marked with user meta `_sfwp_test_fixture`:

| Login | State |
|---|---|
| `sfwp-reader` | WordPress only, not linked |
| `sfwp-member` | linked, no tags |
| `sfwp-silver` | `silver-member` |
| `sfwp-gold` | `gold-member` |
| `sfwp-premium` | `gold-member` + `silver-member` |
| `sfwp-lapsed` | `gold-member`, Shopify account disabled |
| `sfwp-stale` | `gold-member`, last synced 30 days ago |

Linked accounts get a made-up customer ID (990000000001 and up) and a synced mirror written into the plugin's user meta, so the gate treats them like real customers. Because they are not real customers, the theme returns true from the plugin's `wp2_shopify_customer_is_fixture` filter (the reconciler skips them), adds a "Test fixture" badge through `wp2_shopify_customer_badges`, and puts their state back on User Switching's `switch_to_user` and when the front-end resync runs for them. Shopify → Test lab can point any linked account at a real dev-store customer; it is then synced from Shopify like any other user.

With User Switching active, administrators get a **Test accounts** toolbar menu on every page, and the menu offers **Switch back** while switched in.

## Refresh membership

`sfwp-test/resync` is in the header, on the Join page and in every paywall teaser. It renders a hidden, cacheable shell; after load it asks the plugin's status endpoint who the visitor is and, for a signed-in linked visitor, shows the last sync time and a button that calls `POST /wp-json/wp2-shopify/v1/account/sync` with the REST nonce from the status call, then reloads so the gates re-render.

## Shopify's WordPress plugin

The theme works with or without Shopify's own plugin (Sell on WordPress). When it is active:

- Its cart toggle sits in the header's account group, not on the extra row the plugin adds after the navigation (`ignoredHookedBlocks` on the navigation block). The theme gives the 1.0.0 toggle a button role and a tab stop, so Enter or Space opens the cart.
- Sandbox health shows its state, whether it uses the same store as WP2, who owns `/cart`, and whether WP2's cart sign-in bridge is active.
- `/shop/` is created on the next request.

## Admin

Shopify → Test lab (under the plugin's top-level Shopify menu; Appearance → Test lab with an older plugin) lists the items and has **Restore test content**, which rewrites them from the theme's current patterns (edits are lost). To add a new seeded item, add a pattern, list it in `sfwp_test_seed_items()` and bump `SFWP_TEST_CONTENT_VERSION`; sites pick it up on their next request.

## Theme blocks

Blockstudio blocks in `blockstudio/`, registered through the Blockstudio copy the plugin bundles (Blockstudio's default theme folder, which the plugin leaves intact):

- **Visitor state** (`sfwp-test/visitor-state`): a cacheable shell that fills itself from the plugin's status endpoint (`admin-ajax.php?action=wp2_shopify_status`), the same call the sign-in control makes. Nonces in the raw view are redacted.
- **Refresh membership** (`sfwp-test/resync`): see above.
- **Story access and byline** (`sfwp-test/story-meta`): the access badge and byline of the current story, in query loops and on single posts.
- **Sandbox health** (`sfwp-test/sandbox-health`): plugin and Blockstudio versions, block registration, pretty permalinks, the callback page and Shopify discovery. Administrators also see which Shopify constants are set, never their values.

The theme stylesheet labels which gate area reached the page ("Rendered: members only area", "public teaser area" or "locked message"), so a tester can see the server's decision at a glance.

## Requirements

WordPress 6.7+, PHP 8.2+, and the WP2 Shopify plugin. Without the plugin the theme still renders; plugin and theme blocks are simply missing.

```sh
composer lint        # php -l on every PHP file
composer build-prod  # deploy hook (composer install --no-dev)
```
