# Avela 1.1.0 — setup and editing

## Install
1. In WordPress, open Appearance → Themes → Add Theme → Upload Theme. Choose **avela-1.1.0.zip**, install, and activate.
2. Open **Appearance → Avela setup**. Install and activate the free **WooCommerce** plugin through the provided WordPress link if needed. The theme never installs plugins silently.
3. Review the existing-page list. Choose whether to add the optional sample content, then click **Set up my store**. Read its report.

Use a staging site during setup. Demo products are real published WooCommerce products, clearly identified in setup, and can be purchased for testing. Remove or replace them before opening your shop.

## What setup does
Creates missing Home, Shop, Cart, Checkout, My Account, About, Contact, FAQ, Order Tracking, Shipping and Returns, Privacy Policy, and Terms and Conditions pages. Uses existing configured WooCommerce pages or matching page slugs without changing their content or status. Existing menus and static homepage assignments stay intact. Review any reused page to confirm its content provides the correct page function.

Legal starter pages are **draft checklists**, requiring your own reviewed wording. WordPress's existing Privacy Policy is reused if present. Nothing is automatically published over an existing page. Setup can be run again without adding duplicate pages, products or menu items. Trashed or edited sample products are not recreated. Errors and conflicts are reported; resolve them in Pages, Settings → Reading, or WooCommerce → Settings → Advanced.

A fresh site gets Home assigned as its homepage. An established blog or static homepage is preserved; choose Home in **Settings → Reading** when you are ready. If a reused Home page already has content, setup preserves it. To start with the supplied homepage instead, use a fresh page/slug on a staging copy, or copy the sections you need from a fresh demo installation.

## Edit your store
- **Pages → Home → Edit:** all hero text, buttons, images, headings, and sections use the native WordPress block editor. Open **List View** to select, move or remove an entire section. Delete the seasonal group if you do not run sales. Select an Avela product collection and use its settings to choose featured, new or sale products and the number displayed. Update button destinations if you change your page URLs.
- **Images:** select an Image block and use Replace. Demo images are bundled locally. Optional demo setup also imports them into Media. Add descriptive alternative text to your own meaningful images.
- **Products:** change titles, descriptions, prices, categories, photos, gallery, stock, attributes and variations in normal WooCommerce screens. Mark products as featured using the star in Products. Product collections respect WooCommerce catalog visibility and stock visibility.
- **Category cards:** edit Products → Categories. Set each category's image. The homepage displays up to four nonempty top-level categories, alphabetically, excluding Uncategorized. Category navigation is a normal menu and can contain more categories.
- **Appearance → Customize → Site Identity:** change the logo and store name.
- **Appearance → Customize → Avela store details & style:** change colors, heading style, the header announcement, footer introduction, contact details and social URLs. Contact details are shared with the Contact page. Blank announcement/social fields hide them. Use dark primary/secondary colors and a light background; check contrast after changes.
- **Appearance → Menus:** edit Main, Category and Footer navigation. Add reviewed policy pages after publishing them.
- **Pages:** edit the About, FAQ and other starter text. Cart, Checkout, My Account and Order Tracking use native WooCommerce functionality. Existing WooCommerce Cart and Checkout blocks are preserved. Avela's store-page block is a safe wrapper for the corresponding standard WooCommerce page, with no custom checkout or authentication logic.
- **Blog:** create a Journal page, select it as the Posts page in Settings → Reading, and add posts normally. Blog archive, article, comments and 404 templates are included.

There is one editing location for each piece of content: page content in the editor; shared header, footer and site style in Customize; catalog in WooCommerce. No PHP, CSS, JSON or shortcode editing is required.

## Before accepting orders
Replace all starter business text and sample products. Publish your reviewed policies, select your Privacy Policy in Settings → Privacy, and select Terms and Conditions in WooCommerce → Settings → Advanced. Draft policies are not linked in the footer. Configure countries, currency, tax, shipping zones, accounts and registration through WooCommerce. Configure your own payment provider and test it in its sandbox, then production as appropriate. Avela does not enable a payment gateway, force an address format or currency, or include the theme seller's payment links.

Test on your hosting with your plugins, currency, addresses, shipping and gateway. A successful offline order does not verify an external payment provider. Email delivery depends on the host/mail service. Contact links use the email/telephone you supply; there is no nonfunctional newsletter or contact form.

## Remove sample content
Appearance → Avela setup → Remove unedited sample products moves only unchanged setup-created products to Trash. Edited samples and unrelated products are preserved. You can restore products from Products → Trash.

Images, categories, menus and pages remain because your edited pages may use them. Sample images are labelled **Avela sample image** in Media. Replace all page/product/category uses, then remove unused sample images in Media. Delete unwanted sample categories through Products → Categories and edit navigation in Appearance → Menus. Trashed sample products prevent accidental re-import; permanently deleting a sample allows a later optional demo run to recreate it.

## Requirements
WordPress 6.6+, PHP 8.0+, and a supported WooCommerce version for shopping. Current tested versions and remaining limitations are in TEST-REPORT.md. No parent theme, separate importer, paid builder, external font service or premium plugin license is required. Keep WordPress and WooCommerce current and back up before upgrades.
