# Shopify for WP Test theme

A block theme for the WP2 Shopify sandbox (`https://shopify-for-wp.instawp.site`). It gives the plugin ([webmultipliers/shopify-for-wp](https://github.com/webmultipliers/shopify-for-wp)) a test experience: a Test lab front page, one page per feature, and a paywalled post for leak checks.

## Deploy

InstaWP syncs this repository like the plugin and runs `composer build-prod` after each sync. `build-prod` runs `composer install --no-dev`, exactly like the plugin. The theme has no Composer dependencies, so it only writes an empty `vendor/` autoloader (ignored by git).

Activate it once: Appearance → Themes → Shopify for WP Test (or `wp theme activate <folder>`).

## What happens on activation

On the first request with the theme active, it creates the test content from its `patterns/` files and, if the site still shows latest posts on the front page, makes the Test lab the front page. It never overwrites existing pages on its own.

| Address | What it tests |
|---|---|
| `/` (Test lab) | Sandbox health, visitor state, and the list of things to try |
| `/auth-callback/` | The plugin's sign-in callback page (follows the `wp2_shopify_auth_callback_slug` filter) |
| `/sign-in-controls/` | `wp2-shopify/auth-action` in every mode and style |
| `/members-area/` | A gate that admits any signed-in visitor |
| `/tag-gates/` | Tag rules: any, all, teaser fallback, a tag nobody has, an open control |
| `/gate-redirect/` | Redirect fallback mode |
| `/join/` | Where calls to action point; how a test customer gets a tag |
| `/account/` | Connect and log out, until the plugin's account card ships |
| `/members-only-article/` | A paywalled post with the phrase `SFWP-SECRET-MEMBERS-ONLY` for leak checks |

The tag gates use `gold-member` and `silver-member`. Add those tags to a test customer in Shopify, or edit the gates.

Appearance → Test lab lists the items and has **Restore test content**, which rewrites them from the theme's current patterns (edits are lost). To add a new seeded item, add a pattern, list it in `sfwp_test_seed_items()` and bump `SFWP_TEST_CONTENT_VERSION`; sites pick it up on their next request.

## Theme blocks

Two Blockstudio blocks in `blockstudio/`, registered through the Blockstudio copy the plugin bundles (Blockstudio's default theme folder, which the plugin leaves intact):

- **Visitor state** (`sfwp-test/visitor-state`): a cacheable shell that fills itself from the plugin's status endpoint (`admin-ajax.php?action=wp2_shopify_status`), the same call the sign-in control makes. Nonces in the raw view are redacted.
- **Sandbox health** (`sfwp-test/sandbox-health`): plugin and Blockstudio versions, block registration, pretty permalinks, the callback page and Shopify discovery. Administrators also see which Shopify constants are set, never their values.

The theme stylesheet labels which gate area reached the page ("Rendered: members only area", "public teaser area" or "locked message"), so a tester can see the server's decision at a glance.

## Requirements

WordPress 6.7+, PHP 8.2+, and the WP2 Shopify plugin. Without the plugin the theme still renders; plugin and theme blocks are simply missing.

```sh
composer lint        # php -l on every PHP file
composer build-prod  # deploy hook (composer install --no-dev)
```
