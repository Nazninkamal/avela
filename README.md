# Avela 1.2.0 — Elementor-ready boutique storefront

A ready-made WordPress/WooCommerce website with **Elementor Free** editing for Home, About, Contact, FAQ, header and footer. Includes editorial promotion cards, live product collections, local imagery and an openly licensed local display font.

- [Download Avela 1.2.0 — installable theme ZIP](dist/avela-1.2.0.zip)
- [Setup and editing guide](docs/CUSTOMER-GUIDE.md)
- [Dependencies and licensing](docs/LICENSING.md)
- [Verification report](docs/TEST-REPORT.md)
- [Current desktop preview](previews/desktop.jpg)

## Start editing

Install the theme, WooCommerce and Elementor Free. Open **Appearance → Avela setup**, prepare the store pages, then click **Create my Elementor website**. Select the homepage, menu and header/footer switches to use the complete Elementor design. The setup screen provides direct Edit with Elementor links to all six layouts.

Existing pages and menus are retained. Repeating setup preserves edits and avoids duplicates. Use Avela Products in Elementor to change merchandising without Pro; manage product information and shopping behavior in WooCommerce.

**Scope:** marketing pages and header/footer are Elementor-editable. Shop, product, cart, checkout, accounts and tracking retain native WooCommerce templates and functionality. Elementor Pro Theme Builder is not required or bundled. Configure your own gateways and reviewed policies before launch.

## Development

The repository root is theme source. No frontend build step is required:

```sh
python3 tools/package.py /path/to/avela-1.2.0.zip
```

Tests, tools, previews, release artifacts and repository files are excluded from the customer ZIP. On a disposable WordPress installation with Elementor Free, WooCommerce and the six layouts already prepared:

```sh
wp eval-file tests/elementor-integration.php
```

See tests/README.md and the verification report. Do not run integration fixtures against production.

## Publication note

The test workspace disconnected after package validation. The runtime source and current desktop viewport were recovered from the same published ZIP and verified against the recorded file hashes. Full new mobile/editor screenshots were captured during testing but their upload was interrupted. Other existing preview files belong to the earlier 1.1.0 release.

GPL-2.0-or-later theme code. Copyright 2026 Naznin Kamal. DM Serif Display is separately licensed under SIL OFL 1.1; its license is bundled. Original generated demo images are bundled locally.
