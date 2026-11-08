# Avela 1.2.0 — Elementor verification

Tested on **8 November 2026** in isolated local sites using WordPress **7.1.4**, WooCommerce **11.2.2**, Elementor Free **4.3.6**, PHP **8.4.24**, MariaDB **11.8.6** and Chromium. No live shop was modified. Elementor Pro was not installed.

## Passed checks

| Area | Evidence |
| --- | --- |
| ZIP installation | Passed WordPress browser upload and active-theme replacement on a second site, including operation and dependency guidance without Elementor. |
| Complete Elementor setup | Six separate layouts prepared: Home, About, Contact, FAQ, Header and Footer. Direct editor links and an explicit homepage/menu/region selection workflow are provided. |
| Fresh setup without demo | Passed on the second installed site: six Elementor layouts created with zero products; existing assignments preserved until switches were explicitly enabled; frontend checked at all three widths. |
| Non-destructive upgrades | Repeated setup preserved every Elementor data document, existing block homepage, old pages, menu assignments and existing edits. No duplicate pages or library templates. Homepage/menu changes occur only when selected. Private/trashed layouts are preserved and reported. |
| Import security | Administrator capability enforced. Valid browser setup form produced a report; invalid nonce returned HTTP 403; unauthorized direct import changed no pages. |
| Elementor heading editing | Changed the hero through the native editor Content field, saved, and verified its new text on the storefront. Changed heading color and font size through Elementor settings; verified the rendered CSS values. Restored demo content after testing. |
| Elementor commerce editing | Changed Avela Products from featured to new, changed product count to three and columns to three in the editor controls, saved and verified three live products in three columns. Restored demo settings. |
| Elementor image editing | Changed the hero using Elementor's native document settings, saved and verified the replacement image on the frontend. Restored the original. |
| Free header/footer editing | Opened both library layouts in Elementor Free. Edited and saved a footer heading and verified it on the storefront, then restored it. Header containers and store widgets were present and editable. No Pro Theme Builder needed. |
| Native widget controls | Avela Shop widgets appear in the Free editor; products, categories, shared store identity, search, account/cart, navigation, footer links, contact information and automatic copyright/policy links are implemented by the theme. |
| Responsive storefront | Home, About, Contact, FAQ, Shop and variable Product pages checked at 1280px, 768px and 390px: HTTP 200, one page H1, one header/footer, and no horizontal overflow. Actual browser screenshots captured and reviewed. |
| Navigation and FAQ | Elementor mobile menu opened and closed with Escape/focus return. Editable FAQ accordion worked. Search and navigation continue to use native WordPress/WooCommerce URLs. |
| Dependency fallbacks | With Elementor deactivated, original header/footer and initial block fallback content rendered safely. With WooCommerce deactivated and Elementor active, product widgets displayed a coming-soon state; no raw shortcodes or frontend JS exceptions on tested pages. |
| Commerce regression | All 23 existing browser flow groups passed with the Elementor header/footer: search and no-results, categories, sorting, price filtering, variable selection, simple/variable add to cart, cart quantity changes, coupons, configured shipping, checkout required-field errors, an offline bank-transfer order, confirmation, tracking, empty cart, customer login and account endpoints, responsive layouts and keyboard focus. |
| Frontend/editor runtime | No browser JavaScript exceptions in the completed Elementor edit/save flows or new page checks. Theme PHP files linted; theme JavaScript syntax checked. |
| Packaging | Single installable avela/ folder. No plugin binaries, test accounts/orders, credentials, database, development tests/tools or temporary fixtures are included. Font and imagery licenses are bundled. |

The Elementor-specific site suite passed eight browser check groups and the editor suite passed six edit/save groups, in addition to the 23 commerce groups and the PHP setup assertions. These groups overlap in coverage; they are not an independent certification count.

## Editing scope

Home, About, Contact, FAQ, header and footer are Elementor layouts using native Free containers/widgets plus theme-supplied store widgets. Product data remains in WooCommerce. Full Shop, single-product, cart, checkout, account and tracking templates remain native WooCommerce layouts with Avela styling; they are not advertised as Elementor Pro Theme Builder templates.

Logo/store name and contact data have shared WordPress controls, avoiding separate conflicting values in each layout. Original block pages remain available. If Elementor is disabled, the initial fallback block copy is used; the theme does not synchronize later Elementor edits into that fallback.

## Remaining limitations

- **External payment gateways and email delivery remain unverified.** Offline orders passed; the local host has no configured external provider or mail transport. Merchants must configure and test both.
- Elementor 4.3.6 emitted a PHP 8.4 implicit-nullability deprecation from its own `modules/global-classes/atomic-global-styles.php:410` during development. It did not prevent the tested editor or storefront flows. No third-party plugin files were patched; production hosting/plugin compatibility should be reviewed normally.
- Browser checks used Chromium. Other browser engines, assistive-technology certification, full RTL visual review, human translations, every plugin combination and the declared minimum WordPress/PHP versions were not exhaustively tested.
- Merchant content, demo products, business information, FAQ wording, legal drafts, taxes, shipping and payment settings need review before launch. Sample products are intentionally purchasable for staging tests.

See CUSTOMER-GUIDE.md for installation, migration and rollback. The earlier 1.1.0 package remains available separately for rollback; its report describes that earlier release.
