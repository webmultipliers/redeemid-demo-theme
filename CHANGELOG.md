# Changelog

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
