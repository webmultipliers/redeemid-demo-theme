# Changelog

## Unreleased

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
