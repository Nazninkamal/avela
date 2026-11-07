# Avela 1.1.0

A standalone WordPress retail theme with an editable block homepage, guided setup, optional original demo imagery and native WooCommerce shopping.

- [Download the installable theme ZIP](dist/avela-1.1.0.zip)
- [Customer setup and editing guide](docs/CUSTOMER-GUIDE.md)
- [Dependencies and licensing](docs/LICENSING.md)
- [Factual verification report](docs/TEST-REPORT.md)
- [Desktop preview](previews/desktop.jpg) · [Mobile preview](previews/mobile.jpg)

Upload **dist/avela-1.1.0.zip** through WordPress → Appearance → Themes → Add Theme → Upload Theme. Activate, install WooCommerce through the standard WordPress plugin flow, then open Appearance → Avela setup. Use the block editor for homepage/page content, Customize for shared store details and style, and WooCommerce for the catalog.

The optional sample products are published for staging tests. Review the draft policies, remove or replace samples, configure your own payments/shipping/taxes, and test your store before launch. External gateways are not configured or verified by this theme.

## Development

The repository root is the theme source. Build the customer ZIP with:

```sh
python3 tools/package.py /path/to/avela-1.1.0.zip
```

This excludes tests, tools, previews, release artifacts and repository files from the customer package. No frontend build step or dependency installation is needed.

On a disposable WordPress site with this theme active, WooCommerce active and the optional demo already imported:

```sh
wp eval-file tests/setup-integration.php
```

The integration test edits and restores local test content, tests sample cleanup, and must not be run against production. See [tests/README.md](tests/README.md) for the browser suite.

GPL-2.0-or-later. Copyright 2026 Naznin Kamal. Based on the supplied Avela 1.0.1 theme. Original AI-generated demo images are bundled locally; see licensing information.
