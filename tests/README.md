# Development tests — disposable sites only

These files are excluded from the installable ZIP. Never run them on a live shop.

`setup-integration.php` assumes an administrator with ID 1 and a fresh Avela demo catalog. It checks idempotency, preserved merchant content/settings, draft policies, cleanup and capability protection. It mutates then restores local fixtures; use a disposable database.

`browser-commerce.py` uses Python Playwright and system Chromium at `/usr/bin/chromium`. It expects the optional demo and a disposable test configuration: store country US/California, USD, a $5 shipping rate titled Test delivery, offline bank transfer enabled, coupons enabled, a 10% coupon AVELA-TEST, and a customer login `test_customer`. Set `AVELA_BASE_URL` and `AVELA_TEST_PASSWORD` in the process environment. The password must match that test customer. No password is committed.

The suite submits an offline order, changes cart quantities, and writes screenshots/results to ignored `tests/artifacts/`. It covers search, category, sorting, filtering, variations, cart, coupons, shipping, checkout validation, confirmation, tracking, accounts, mobile navigation, keyboard focus and responsive overflow. Broader verification results, including clean WordPress ZIP upload and native editor saves, are documented in docs/TEST-REPORT.md.

## Elementor 1.2.0 integration

With Elementor Free active and the six layouts already created on a disposable site, run `wp eval-file tests/elementor-integration.php`. It checks preserved Elementor data, idempotency, original block-page retention, explicit assignment behavior, legal drafts and capability protection. It temporarily edits and restores one local document.

The Elementor editor/browser verification results are in docs/TEST-REPORT.md. Keep test credentials in environment variables or local ignored files; none are bundled in the theme ZIP.
