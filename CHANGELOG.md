# Changelog

## 0.0.18

- **The front-end Test lab page is gone; its guide moved to RedeemID → Demo → Test guide.** Everything it showed is now somewhere that already had a home: the visitor's own state is on the Account page (its Visitor state panel), sandbox health is the Demo screen's **Sandbox health** tab, what each test account should see is the **Test accounts** tab, and the features table, the content-type table and the list of things to try are the new **Test guide** tab, which opens first. The theme no longer seeds the page, so trash the old Test lab page under Pages; `/test-lab/` then stops working, and nothing on redeemid.com links to it.
- The header's **Sandbox** badge and the footer's **Test guide** link open the Demo screen. About and Join point testers there too, the sign-in callback page links to the Account page, and the **Sign out only** case on Sign-in controls returns to the Account page (it went to the home page).
- Restore About, Join, the callback page and Sign-in controls (RedeemID → Demo → Test content) to pick up the changes. The header and footer change on deploy.

## 0.0.17

- **RedeemID → Test lab is now RedeemID → Demo**, with a tab each for **Test accounts**, **Test content** (the seeded items and **Restore test content**) and **Sandbox health**. It was one long page. Its address is now `admin.php?page=redeemid-demo`; the toolbar's **Test accounts** link follows it. The front-end Test lab page keeps its name and address.
- Restore the Test lab page (RedeemID → Demo → Test content) to pick up its new admin wording.

## 0.0.16

- **The contribution form posts to the store's own domain.** It used the myshopify.com domain, which put the reader's cart on a different domain from the one checkout runs on once the store has a custom domain. It now asks RedeemID for the store's address (`redeemid_store_url()`), so the cart and checkout stay on shop-demo.redeemid.com. No page needs restoring.
- Needs RedeemID 0.0.11 (`redeemid_store_url()`, developer API 1.7.0). With an older RedeemID the form stays hidden, as it does when no store is set.

## 0.0.15

- **Sign-in is just sign-in.** Reader-facing copy no longer names Shopify: the header button says **Sign in**, the home page's free-account row says "Sign in with your email", and the members area asks readers to "Sign in to open the members area." The Account page's link step is **Confirm your email**.
- Test lab, Sign-in controls and Account notes follow RedeemID's new flow: confirming an email links the account with no extra step, and a returning reader whose email matches an existing account is signed in to it. A new Test lab step covers that.
- Restore Home, Account, Members area, Test lab and Sign-in controls (RedeemID → Test lab) to pick up the pattern changes. The header changes on deploy.
- Works with the next RedeemID release (the one after 0.0.10).

## 0.0.14

- Checks on every change: accessibility (axe-core, WCAG 2.2 AA) and Lighthouse on every page, category, archive and test account, PHP lint and PHPCS VIP, ESLint, composer and npm audit, Psalm taint analysis and CodeQL. The analytics script drops an unused catch binding. No visible change.
- Works with RedeemID 0.0.10.

## 0.0.13

- **Screen readers.** The header and footer are one banner and one content-info landmark each (the template part already was the `<header>`/`<footer>`; the group inside it was a second one). The section navigation is named **Sections**. Each story's **Latest from the Ledger** is a complementary landmark, so no content sits outside one. The home page has a visually hidden `h1`.
- The Join page's comparison table has row headers, a named corner cell, and says **Yes** or **No** instead of reading out a tick or a dash. The Test lab's wide tables take keyboard focus so they can be scrolled on a phone. Case headings on Tag gates, Sign-in controls and the Members area are `h2`s straight under the title (they were `h3`s, skipping a level), at the same size.
- Restore Home, Join, Test lab, Tag gates, Sign-in controls and Members area (RedeemID → Test lab) to pick up the pattern changes.

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
