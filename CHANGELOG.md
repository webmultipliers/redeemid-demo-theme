# Changelog

## 0.0.12

- Defines `REDEEMID_ALLOW_FIXTURES` as true unless `wp-config.php` sets it. RedeemID 0.0.9 ignores its fixture filters on a production site without it, and the demo's test accounts are fixtures on purpose. Define it as false to turn them off.

## 0.0.11

- The Account page's log-out note no longer says WordPress's own logout link ends only the WordPress session: with RedeemID's matching change it now ends the Shopify session too. Restore the Account page (RedeemID → Test lab) to pick up the new wording.

## 0.0.10

- **Reader contributions.** A new **Support the Ledger** page (`/support/`, in the footer) and a compact ask at the end of every story take pay-what-you-want contributions through Shopify checkout. The store sells a $1 **Contribution** product (handle `contribution`) and the amount is the quantity: the `redeemid-demo/contribute` form posts it to the store's `/cart/add` and goes straight to checkout, with no JavaScript. The form shows only when that product exists at exactly $1.
- **Supporter**, a third demo feature: the `supporter` tag, set in code. A Shopify Flow workflow on Order paid adds the tag for a contribution, and the form then thanks the reader instead of asking. **Sam Supporter**, a new test account, holds it. The Test lab has a **Contribute** step; restore the Test lab page (RedeemID → Test lab) to pick it up.

## 0.0.9

- Dropped styles nothing uses: the `.ledger-band` callout and the account-bar rules for Shopify's own cart toggle, which the header no longer shows (it uses RedeemID's Cart link, and the navigation ignores the hooked toggle). No visible change.

## 0.0.8

- A metered story's "Free story 1 of 2 this month" note no longer takes the drop cap; the story's first paragraph keeps it, and the note is set small.

## 0.0.7

- **The meter is on.** Signed-in readers without access open 2 locked stories a month free (RedeemID → Access → Content rules), so the Ledger shows the whole publisher journey: guest, sign in, free stories, wall, join. It's switched on once by the content upgrade; turn it off there and it stays off until **Restore test content**.
- **Mia Meter**, a new test account: linked, no tags, 1 free story left this month. Every other test account starts the month with the meter used up, so their expectations in the Test lab don't change. Use **Save and reset test accounts** at the start of a month to put the meters back.
- The Test lab has a **Try the meter** step. Restore the Test lab page (RedeemID → Test lab) to pick it up.

## 0.0.6

- **Fathom events.** `blockstudio/analytics/global-fathom.view.js`, a Blockstudio global asset, sends each interaction to Fathom as `<component>: <target>`, named from where it sits and where it goes (`Sections: category/investigations`, `Account bar: account`), plus the plugin's own events from its browser client: `Wall: member`, `Meter: view`, `Sign in: wall`, `Checkout: subscription from wall`, `Search: submit`. It subscribes to the plugin's `redeemid` stream, so no user, email, tag or post ID reaches Fathom. Fathom's WordPress plugin loads Fathom.

## 0.0.5

- **Privacy requests.** The Account page's Member Account card offers Download my data and Delete my data (RedeemID 0.0.3), and the Test lab walks through handling a request in Tools → Export / Erase Personal Data, including the Shopify column and why the Shopify erasure is refused for simulated accounts. Restore the Account and Test lab pages (RedeemID → Test lab) to pick up the changes.

## 0.0.4

- **The shop runs on RedeemID.** `/shop/` uses RedeemID's Shopify Collection block, and the header's Cart link is RedeemID's Shopify Cart Link (icon and count on phones). The Shop page is always created now, not only while Shopify's WordPress plugin is active. RedeemID's `/cart/` and `/products/{handle}/` pages come with the plugin.
- **Tag gates case 6** gates RedeemID's Shopify Product block instead of Shopify's own. Restore the Tag gates and Shop pages (RedeemID → Test lab) to pick up the changes.

## 0.0.3

- **A cleaner, more modern Ledger.** Harbor navy for the footer and the Ledger Reports band, softer rounded cards and panels, tighter headline type, a single hairline under the header and section titles with a rule that runs to the edge.
- **A drawn cover on the lead story** in its section's tone (the Ledger has no photos), and a tone dot before each card's section name.
- **Stories:** larger headline and dek, a drop cap, card-style previous and next links, and a rounded paywall panel with a slim colour band.
- **Phones:** the sections are a swipeable strip instead of a menu button, each section row and the reports are swipeable card rows instead of one long column, and the level tiles pair up.
- **Join page:** the page title is the eyebrow over the headline (new `page-join` template), and the plan cards sit side by side at equal height. Restore the Join and Home pages (RedeemID → Test lab) to pick up their pattern changes.
- **Accessibility:** one focus ring everywhere, and the plugin's account and feature panels are muted by colour rather than opacity on top of colour, so their labels pass AA.

## 0.0.2

- **Tidier top bar.** The account actions (Refresh, Account, Cart, Log in or Log out) sit on one line at the bar's small size. On phones the date shortens and Refresh and Cart become icons, so the date and actions share one row with the briefing links below. Log out is a quiet outlined button, Log in a small accent one, the refresh result drops below the bar, and feature chips take their own line.
- **Darker accent green** (#00775a) so accent text passes WCAG AA on its tints.

## 0.0.1

First public release, alongside RedeemID 0.0.1.

- **The Harborview Ledger**: a fictional news site with sixteen free and paywalled stories across seven sections, gated by plugin access levels, section rules and a paywall break.
- **Demo post types and taxonomies** (briefings, reports, city guides, corrections) that exercise every layer of the plugin's content rules.
- **A Test lab** with one page per plugin feature, leak-check marker phrases, and **Restore test content** under RedeemID → Test lab.
- **Eight test accounts** in every membership state, with a User Switching toolbar menu. Their Shopify state is simulated, so Shopify is never asked about them.
- **Two demo features** (Ad-free reading, Early briefing) registered with the plugin's feature registry.
- Works with or without Shopify's own WordPress plugin.
