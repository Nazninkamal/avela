# Avela 1.2.0 — ready-made Elementor shop

## Install or upgrade
1. Upload **avela-1.2.0.zip** in WordPress → Appearance → Themes → Add Theme → Upload Theme. For an upgrade, choose to replace the existing Avela theme. Back up your site first.
2. Install and activate **WooCommerce** and **Elementor Website Builder (Free)** through Plugins → Add New. Appearance → Avela setup includes links to the normal installation screens. Avela never installs or activates a plugin silently. Elementor Pro is **not required**.
3. Open **Appearance → Avela setup**. Run **Set up my store** to prepare missing shop/support pages. Optional sample products and original local images can be selected here; use a staging site while sample products are published.
4. In **Build the ready-made Elementor website**, select:
   - Use Elementor Home as my homepage.
   - Assign new menus linking to the Elementor pages.
   - Use the editable Elementor header and footer.
5. Click **Create my Elementor website**. Open any of the six **Edit with Elementor** links on the same screen.

Your previous homepage, pages and menus are retained. Leave the switches off to create and preview the new pages before changing your live navigation. Repeating setup reuses the layouts and preserves every edit. Private or trashed layouts are reported rather than duplicated. Choosing the homepage/menu switches explicitly changes those assignments; no existing page content is overwritten.

## Edit in Elementor Free

| What to change | Where |
| --- | --- |
| Hero title, descriptions and all marketing copy | Select the Heading or Text Editor widget. Type in the Content panel; use Style for colors and typography. |
| Photographs | Select an Image widget → Choose Image/Replace. Select an existing Media image or upload your own; edit its alternative text in Media. |
| Buttons | Select the Button widget. Change its label, destination and Style settings. |
| Featured, new and sale products | Select **Avela Products**. Choose the collection, product count, responsive columns and image proportions. Style its product titles and buttons in the widget's Style tab. |
| Categories | **Avela Categories** displays nonempty top-level WooCommerce categories. Change category names/images in Products → Categories. |
| Optional sections | Open Elementor's **Structure/Navigator**. Sections have descriptive names. Move, duplicate or delete the entire section; the seasonal edit and editorial cards are optional. |
| About, Contact and FAQ layouts | Use their Edit with Elementor links in Avela setup. The FAQ uses Elementor's editable Accordion widget. |
| Header and footer | Use the dedicated Header/Footer edit links in **Avela setup**. These work with Elementor Free; do not use Pro-only Theme Builder. |
| Site name and logo | Appearance → Customize → Site Identity. Avela Store Identity widgets use the shared identity automatically. |
| Email, telephone, address and hours | Appearance → Customize → Avela store details & style. The Contact Details widget updates the Contact page and footer together. |
| Navigation links | Appearance → Menus. The Elementor navigation widgets read the selected WordPress menu locations. |

Click **Publish/Update** in Elementor to save. Use the desktop/tablet/mobile toolbar to review each layout. You can add other Elementor Free widgets normally. Use the **Avela Shop** widget category for store functionality; Elementor's separate Pro WooCommerce widgets are not needed.

The theme includes a locally bundled editorial font, **Avela Editorial** (DM Serif Display), selectable in Elementor's typography control. You can choose another font in Elementor. Review Elementor's own font/privacy settings if you enable externally hosted fonts.

## What remains in WooCommerce

Edit product titles, prices, photographs, variations, stock and descriptions in **Products**. The Shop archive, single-product template, cart, checkout, customer account, confirmation and tracking use native WooCommerce and the supplied Avela styling. The full WooCommerce single-product/archive/checkout templates are **not** Elementor Free Theme Builder templates. This preserves native payment, account and variation behavior.

The prepared Home, About, Contact and FAQ marketing pages and the global header/footer are editable in Elementor. Original block-editor pages remain editable in the WordPress editor. Use the editor belonging to each page; do not convert an Elementor page back to the block editor merely to change its text.

## Before launch

Replace sample products and verify all starter copy. Samples are published and purchasable for staging tests; no reviews or sales figures are invented. Optional demo import never includes orders, customers, passwords, API keys or business settings.

Review and publish your **Shipping and Returns, Privacy Policy and Terms and Conditions** drafts. Select the privacy page in Settings → Privacy and terms in WooCommerce → Settings → Advanced. The Elementor copyright/policy widget links only published policies and keeps the year and store name current. Add other links through your footer menu.

Configure your own country, currency, taxes, shipping, account preferences and payment provider in WooCommerce. Test your provider and transactional email on your hosting. Offline test orders do not verify an external gateway. No country, currency, external seller payment link or gateway is forced by the theme.

## Restore or clean up

- **Avela setup → Restore original theme header and footer** disables the Elementor regions while retaining their layouts and edits.
- Choose your former homepage in **Settings → Reading** and old menus in Appearance → Menus to restore those assignments.
- If Elementor is deactivated, the original header/footer return and imported pages display their initial block-content fallback. **The theme does not synchronize later Elementor edits into that fallback; its content is not guaranteed to match the latest design.** Reactivate Elementor to restore the edited designs.
- Remove unedited samples through Avela setup. Edited samples are preserved. Images, categories and pages remain because they may be reused; replace their uses before removing them manually. Samples already in Trash are not reimported.

Requirements: WordPress 6.6+ and PHP 8.0+ for the theme; installed plugin versions may require newer versions. See TEST-REPORT.md for the versions actually exercised and remaining limitations. No parent theme, paid builder or separate importer is required.
