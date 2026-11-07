# Avela 1.1.0 — verification report

Tested on 6 November 2026 in isolated local WordPress installations. No live shop was modified. Tests used WordPress **7.1.4**, WooCommerce **11.2.2**, PHP **8.4.24**, MariaDB **11.8.6**, and headless Chromium. The initial no-WooCommerce smoke test also ran on a disposable SQLite-backed installation; the commerce and final checks used MariaDB.

## Results

| Check | Result and evidence |
| --- | --- |
| Theme ZIP upload, installation, activation | Passed through WordPress's actual Upload Theme screen on a second clean site. Activation completed through Appearance → Themes. |
| Without WooCommerce | Passed: storefront renders, no raw WooCommerce shortcodes or browser JS exceptions; setup explains the dependency, links to normal installation and disables store setup. |
| Fresh setup with demo | Passed: essential pages and menus created, native WooCommerce pages reused, Home assigned on a fresh site, six products including two variable-product variations and seven local images imported. |
| Fresh setup without demo | Passed on the second clean site: zero sample products; pages and menus created; repeat preserved page count. |
| Repeated setup | Passed: no duplicate pages, products or menus; full existing page content unchanged; store currency/address and menu/homepage settings preserved. |
| Existing merchant content | Passed: existing static homepage and edited Home content preserved. An edited version of the initial WordPress post is treated as an established blog. |
| Legal pages | Passed: Shipping and Returns and Terms remain drafts; WordPress's existing draft Privacy Policy reused. No draft policy link rendered in footer. |
| Sample cleanup | Passed: five unedited samples moved to Trash, edited sample preserved. Other pages, assets and business settings retained. Fixtures restored after the test. Change detection includes product data, variations and merchant metadata; setup bookkeeping/editor locks are excluded. |
| Setup permissions/security | Passed: browser submission with valid nonce displays report; invalid nonce returns HTTP 403; unauthorized direct setup call changes no pages. |
| Native editing | Passed: all 36 supplied homepage blocks opened as valid in the WordPress editor. Saved a heading edit through the editor, verified it on the frontend, then restored it. No admin JS exceptions in this flow. |
| Search and no-results | Passed: header product search, matching catalog result, and no-results recovery link. Single matches remain on the results page. |
| Catalog | Passed: category archive, price sorting, price range filtering, and pagination. Variable products follow native WooCommerce range-overlap behavior. Pagination was verified with a temporary four-products-per-page test filter, removed afterward. |
| Products and gallery | Passed: simple product form, variable-product selection and quantity, two-image gallery, thumbnail navigation, lightbox and Escape close. Related products rendered on the simple-product page. |
| Add to cart | Passed: simple and variable products added; AJAX catalog add-to-cart updated the header count. |
| Cart | Passed: native WooCommerce Cart block quantity update, percentage coupon, configured shipping rate, and empty-cart state after order. |
| Checkout | Passed: native Checkout block required-field errors; populated contact/address fields; configured shipping and offline bank-transfer order. Automation waits for native address/rate calculations to finish before submitting. |
| Confirmation and tracking | Passed: order-received page and tracking using matching order number and billing email. No money was transferred. |
| Account | Passed: customer login and dashboard, Orders, Downloads, Addresses, and account/password editor pages. Password handling is WooCommerce's own implementation. |
| Other pages | Passed: About, Contact (functional supplied mail link), expandable FAQ, blog archive, article and HTTP 404 template. |
| Responsive rendering | Passed at 390px, 768px and 1280px: Home, Shop, Product, Account and 404 had no horizontal overflow. Additional 390px cart and checkout checks passed. Actual full-page browser screenshots captured, loaded images checked, and desktop/mobile screenshots visually inspected. |
| Keyboard | Passed: skip link, visible keyboard focus, mobile menu open/close, Escape focus return, gallery Escape close. |
| Runtime and syntax | PHP files linted; both theme JS files syntax-checked; no frontend JS exceptions during the full commerce suite. No theme PHP notices or fatal errors observed in the tested frontend/admin flows. WooCommerce emitted translation-timing notices during WP-CLI rewrite commands, outside those flows; the local host also has no mail transport. |
| Customer-package hygiene | ZIP contains a single avela/ theme folder, local assets and customer documentation. Development tests/tools, runtime, database, test orders/accounts, credentials and temporary fixtures excluded. |

## Screenshots

Delivered screenshots show the actual running theme, with optional sample products and local test contact information. They include desktop/mobile home, shop, product, account, cart and checkout views, plus setup and order confirmation. No static HTML mockups are used. The theme's screenshot.jpg is also a real browser capture.

## Remaining limitations

- **External payment gateways: unverified.** No provider credentials/configuration were available. An offline bank-transfer test does not verify card, wallet or external payment processing. Merchants must configure and test their own provider.
- **Email delivery: unverified.** The local test host has no sendmail/SMTP service. Order confirmation pages and order tracking passed; delivery of transactional email requires hosting/mail configuration.
- Compatibility was exercised on the versions above, with native WooCommerce behavior. The declared minimum WordPress/PHP versions, other WooCommerce releases, third-party plugins, live tax services, alternative shipping integrations, and every country/currency combination were not exhaustively tested.
- Browser automation and visual review used Chromium. Safari, Firefox, assistive-technology certification, full RTL visual review and human translations were not performed. Strings are translation-ready and an updated POT is included.
- Legal starter pages require merchant review. Demo descriptions, prices, stock, imagery, FAQ answers and contact/business details must be replaced or confirmed before launch. Optional sample products are purchasable on a staging site.

The source repository includes the setup integration test and the packaging script. Their test fixtures are development-only and are never included in the installable customer ZIP.
