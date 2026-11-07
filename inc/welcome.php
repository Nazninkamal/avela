<?php
/** Opt-in, repeatable, non-destructive store setup. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
function avela_welcome_menu() { add_theme_page( __( 'Avela setup', 'avela' ), __( 'Avela setup', 'avela' ), 'manage_options', 'avela-setup', 'avela_welcome_page' ); }
add_action( 'admin_menu', 'avela_welcome_menu' );
function avela_setup_notice() {
 if ( current_user_can( 'manage_options' ) && ! get_option( 'avela_setup_complete' ) ) {
  echo '<div class="notice notice-info"><p>' . esc_html__( 'Welcome to Avela. Install WooCommerce, then prepare your pages and navigation with the optional guided setup.', 'avela' ) . ' <a href="' . esc_url( admin_url( 'themes.php?page=avela-setup' ) ) . '">' . esc_html__( 'Set up my store', 'avela' ) . '</a></p></div>';
 }
}
add_action( 'admin_notices', 'avela_setup_notice' );
function avela_existing_page( $slug, $definition ) {
 if ( ! empty( $definition['wc'] ) ) {
  $configured = absint( get_option( 'woocommerce_' . $definition['wc'] . '_page_id' ) );
  if ( $configured ) { return get_post( $configured ); }
 }
 if ( 'privacy-policy' === $slug && get_option( 'wp_page_for_privacy_policy' ) ) { return get_post( get_option( 'wp_page_for_privacy_policy' ) ); }
 $owned = get_posts( array( 'post_type' => 'page', 'post_status' => array( 'publish', 'draft', 'private', 'pending', 'future', 'trash' ), 'meta_key' => '_avela_setup_page', 'meta_value' => $slug, 'numberposts' => 1 ) );
 return $owned ? $owned[0] : get_page_by_path( $slug );
}
function avela_can_assign_home() {
 if ( get_option( 'page_on_front' ) || get_option( 'page_for_posts' ) || 'posts' !== get_option( 'show_on_front' ) ) { return false; }
 $posts = get_posts( array( 'numberposts' => 2, 'post_status' => 'publish' ) );
 if ( ! $posts ) { return true; }
 if ( 1 !== count( $posts ) || 1 !== (int) $posts[0]->ID || 'hello-world' !== $posts[0]->post_name ) { return false; }
 $default_text = __( 'Welcome to WordPress. This is your first post. Edit or delete it, then start writing!' );
 return __( 'Hello world!' ) === $posts[0]->post_title && $default_text === trim( wp_strip_all_tags( $posts[0]->post_content ) );
}
function avela_run_setup( $demo = false ) {
 if ( ! current_user_can( 'manage_options' ) ) { return array( __( 'You do not have permission to set up this store.', 'avela' ) ); }
 if ( ! class_exists( 'WooCommerce' ) ) { return array( __( 'Install and activate WooCommerce first. No pages or settings were changed.', 'avela' ) ); }
 if ( ! add_option( 'avela_setup_lock', time(), '', false ) ) { return array( __( 'Another setup is in progress. If interrupted, try again after five minutes.', 'avela' ) ); }
 $report = array(); $ids = array();
 try {
  if ( $demo ) { $report = array_merge( $report, avela_import_demo() ); }
  $definitions = avela_page_definitions();
  $home_definition = $definitions['home']; unset( $definitions['home'] ); $definitions['home'] = $home_definition;
  foreach ( $definitions as $slug => $definition ) {
   if ( ! empty( $definition['wc'] ) && get_option( 'woocommerce_' . $definition['wc'] . '_page_id' ) && ! get_post( absint( get_option( 'woocommerce_' . $definition['wc'] . '_page_id' ) ) ) ) {
    $report[] = sprintf( __( '%s: its configured WooCommerce page is missing. Select a valid page in WooCommerce settings; the existing assignment was preserved.', 'avela' ), $definition['title'] ); continue;
   }
   $page = avela_existing_page( $slug, $definition );
   if ( $page ) {
    if ( 'page' !== $page->post_type || in_array( $page->post_status, array( 'trash', 'private', 'pending', 'future' ), true ) ) {
     $report[] = sprintf( __( '%s: conflict with an existing non-public or unavailable page. Review Pages and WooCommerce → Settings → Advanced; nothing was overwritten.', 'avela' ), $definition['title'] ); continue;
    }
    $ids[ $slug ] = $page->ID;
    $report[] = sprintf( __( '%s: reused existing page, content and status preserved.', 'avela' ), $definition['title'] );
   } else {
    $content = 'home' === $slug ? avela_home_content() : $definition['content'];
    $id = wp_insert_post( array( 'post_type' => 'page', 'post_title' => $definition['title'], 'post_name' => $slug, 'post_status' => $definition['status'], 'post_content' => $content, 'comment_status' => 'closed' ), true );
    if ( is_wp_error( $id ) ) { $report[] = $id->get_error_message(); continue; }
    update_post_meta( $id, '_avela_setup_page', $slug ); $ids[ $slug ] = $id;
    $page = get_post( $id );
    $report[] = sprintf( __( '%1$s: created (%2$s).', 'avela' ), $definition['title'], $definition['status'] );
   }
   if ( ! empty( $definition['wc'] ) && ! get_option( 'woocommerce_' . $definition['wc'] . '_page_id' ) && 'publish' === $page->post_status ) { update_option( 'woocommerce_' . $definition['wc'] . '_page_id', $page->ID ); }
  }
  if ( avela_can_assign_home() && ! empty( $ids['home'] ) ) {
   update_option( 'show_on_front', 'page' ); update_option( 'page_on_front', $ids['home'] ); $report[] = __( 'Assigned Home as the homepage.', 'avela' );
  } else { $report[] = __( 'Existing homepage or blog settings preserved. Change the homepage in Settings → Reading if needed.', 'avela' ); }
  $report = array_merge( $report, avela_setup_menus( $ids ) );
  update_option( 'avela_setup_complete', AVELA_VERSION );
  $report[] = __( 'Legal pages remain drafts. Review, publish, and select your policies in WordPress and WooCommerce settings before launch.', 'avela' );
 } finally { delete_option( 'avela_setup_lock' ); }
 return $report;
}
function avela_setup_menus( $ids ) {
 $report = array(); $locations = get_theme_mod( 'nav_menu_locations', array() );
 $sets = array( 'primary' => array( 'home', 'shop', 'about', 'contact', 'faq' ), 'footer' => array( 'shop', 'about', 'contact', 'faq', 'order-tracking', 'my-account' ), 'categories' => array() );
 foreach ( $sets as $location => $slugs ) {
  if ( ! empty( $locations[ $location ] ) ) { $report[] = sprintf( __( '%s navigation: existing assignment preserved.', 'avela' ), $location ); continue; }
  $name = 'Avela ' . ucfirst( $location ); $existing = wp_get_nav_menu_object( $name );
  if ( $existing ) { $locations[ $location ] = $existing->term_id; $report[] = sprintf( __( '%s: reused existing menu without adding items.', 'avela' ), $name ); continue; }
  if ( 'categories' === $location && ! get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'exclude' => array( (int) get_option( 'default_product_cat' ) ), 'number' => 1 ) ) ) { continue; }
  $menu = wp_create_nav_menu( $name ); if ( is_wp_error( $menu ) ) { $report[] = $menu->get_error_message(); continue; }
  foreach ( $slugs as $slug ) {
   if ( ! empty( $ids[ $slug ] ) && 'publish' === get_post_status( $ids[ $slug ] ) ) {
    $labels = array( 'home' => __( 'Home', 'avela' ), 'about' => __( 'Our story', 'avela' ), 'contact' => __( 'Contact', 'avela' ), 'faq' => __( 'FAQ', 'avela' ), 'order-tracking' => __( 'Order tracking', 'avela' ) );
    wp_update_nav_menu_item( $menu, 0, array( 'menu-item-object-id' => $ids[ $slug ], 'menu-item-object' => 'page', 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish', 'menu-item-title' => $labels[ $slug ] ?? get_the_title( $ids[ $slug ] ) ) );
   }
  }
  if ( 'categories' === $location ) {
   $terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'number' => 6, 'exclude' => array( (int) get_option( 'default_product_cat' ) ) ) );
   if ( ! is_wp_error( $terms ) ) { foreach ( $terms as $term ) { wp_update_nav_menu_item( $menu, 0, array( 'menu-item-object-id' => $term->term_id, 'menu-item-object' => 'product_cat', 'menu-item-type' => 'taxonomy', 'menu-item-status' => 'publish' ) ); } }
  }
  $locations[ $location ] = $menu; $report[] = sprintf( __( '%s: created and assigned.', 'avela' ), $name );
 }
 set_theme_mod( 'nav_menu_locations', $locations ); return $report;
}
function avela_fingerprint_meta( $id ) {
 $metadata = get_post_meta( $id );
 foreach ( array_keys( $metadata ) as $key ) {
  if ( 0 === strpos( $key, '_avela_demo_' ) || 0 === strpos( $key, '_edit_' ) ) { unset( $metadata[ $key ] ); }
 }
 ksort( $metadata ); return $metadata;
}
function avela_demo_fingerprint( $id ) {
 $product = wc_get_product( $id ); if ( ! $product ) { return ''; }
 $data = $product->get_data(); $data['merchant_meta'] = avela_fingerprint_meta( $id ); unset( $data['date_created'], $data['date_modified'], $data['meta_data'] );
 if ( $product->is_type( 'variable' ) ) { $data['children_data'] = array_map( function ( $child ) { $p = wc_get_product( $child ); $d = $p ? $p->get_data() : array(); $d['merchant_meta'] = avela_fingerprint_meta( $child ); unset( $d['date_created'], $d['date_modified'], $d['meta_data'] ); return $d; }, $product->get_children() ); }
 return md5( wp_json_encode( $data ) );
}
function avela_import_demo() {
 $report = array();
 require_once ABSPATH . 'wp-admin/includes/file.php'; require_once ABSPATH . 'wp-admin/includes/media.php'; require_once ABSPATH . 'wp-admin/includes/image.php';
 $images = get_option( 'avela_demo_images', array() );
 foreach ( array( 'hero', 'cleanser', 'serum', 'cream', 'linen', 'candle', 'dish' ) as $name ) {
  if ( ! empty( $images[ $name ] ) && get_post( $images[ $name ] ) ) { continue; }
  $source = get_theme_file_path( 'assets/demo/' . $name . '.jpg' );
  if ( ! is_readable( $source ) ) { $report[] = __( 'A bundled image was unavailable. You can upload your own in Media.', 'avela' ); continue; }
  $tmp = wp_tempnam( $source ); copy( $source, $tmp );
  $id = media_handle_sideload( array( 'name' => 'avela-sample-' . $name . '.jpg', 'tmp_name' => $tmp ), 0, sprintf( __( 'Avela sample image: %s', 'avela' ), $name ) );
  if ( is_wp_error( $id ) ) { wp_delete_file( $tmp ); $report[] = $id->get_error_message(); continue; }
  $images[ $name ] = $id; update_post_meta( $id, '_avela_demo_asset', $name ); update_post_meta( $id, '_wp_attachment_image_alt', sprintf( __( 'Sample %s product still life', 'avela' ), $name ) );
 }
 update_option( 'avela_demo_images', $images );
 $categories = array();
 foreach ( array( 'daily-care' => __( 'Daily care', 'avela' ), 'body-and-bath' => __( 'Body & bath', 'avela' ), 'home-and-living' => __( 'Home & living', 'avela' ), 'little-extras' => __( 'Little extras', 'avela' ) ) as $slug => $label ) {
  $term = get_term_by( 'slug', $slug, 'product_cat' );
  if ( $term ) { $categories[ $slug ] = $term->term_id; continue; }
  $created = wp_insert_term( $label, 'product_cat', array( 'slug' => $slug ) );
  if ( is_wp_error( $created ) ) { continue; }
  $categories[ $slug ] = $created['term_id']; update_term_meta( $created['term_id'], '_avela_demo_category', 1 );
  $asset = array( 'daily-care' => 'cleanser', 'body-and-bath' => 'linen', 'home-and-living' => 'candle', 'little-extras' => 'dish' );
  if ( ! empty( $images[ $asset[ $slug ] ] ) ) { update_term_meta( $created['term_id'], 'thumbnail_id', $images[ $asset[ $slug ] ] ); }
 }
 $products = array(
  array( 'cleanser', __( 'Everyday gentle cleanser', 'avela' ), '28', 'daily-care' ),
  array( 'serum', __( 'The daily botanical serum', 'avela' ), '38', 'daily-care' ),
  array( 'cream', __( 'Comfort face cream', 'avela' ), '32', 'daily-care' ),
  array( 'linen', __( 'Soft linen bath set', 'avela' ), '44', 'body-and-bath' ),
  array( 'candle', __( 'Quiet moments candle', 'avela' ), '26', 'home-and-living' ),
  array( 'dish', __( 'Everyday ceramic dish', 'avela' ), '22', 'little-extras' ),
 );
 foreach ( $products as $index => $data ) {
  $existing = get_posts( array( 'post_type' => 'product', 'post_status' => 'any', 'meta_key' => '_avela_demo_product', 'meta_value' => $data[0], 'numberposts' => 1 ) );
  $trashed = get_posts( array( 'post_type' => 'product', 'post_status' => 'trash', 'meta_key' => '_avela_demo_product', 'meta_value' => $data[0], 'numberposts' => 1 ) );
  if ( $existing || $trashed ) { $report[] = sprintf( __( '%s: existing or trashed sample preserved.', 'avela' ), $data[1] ); continue; }
  if ( wc_get_product_id_by_sku( 'AVELA-SAMPLE-' . strtoupper( $data[0] ) ) ) { $report[] = __( 'A sample SKU already exists; skipped to avoid a conflict.', 'avela' ); continue; }
  $product = 3 === $index ? new WC_Product_Variable() : new WC_Product_Simple();
  $product->set_name( $data[1] ); $product->set_slug( 'sample-' . $data[0] ); $product->set_status( 'publish' ); $product->set_sku( 'AVELA-SAMPLE-' . strtoupper( $data[0] ) );
  $product->set_description( __( 'Sample product for demonstration. Replace this description, photography, materials, dimensions, care details and price with verified information about your own product before selling.', 'avela' ) );
  $product->set_short_description( __( 'A considered addition to your everyday routine. Sample product — replace with your own product details before launch.', 'avela' ) );
  $product->set_featured( $index < 4 ); $product->set_reviews_allowed( false ); $product->set_catalog_visibility( 'visible' );
  if ( ! empty( $categories[ $data[3] ] ) ) { $product->set_category_ids( array( $categories[ $data[3] ] ) ); }
  if ( ! empty( $images[ $data[0] ] ) ) { $product->set_image_id( $images[ $data[0] ] ); }
  if ( ! empty( $images['hero'] ) ) { $product->set_gallery_image_ids( array( $images['hero'] ) ); }
  if ( 3 === $index ) {
   $attribute = new WC_Product_Attribute(); $attribute->set_name( __( 'Size', 'avela' ) ); $attribute->set_options( array( __( 'Hand towel', 'avela' ), __( 'Bath towel', 'avela' ) ) ); $attribute->set_visible( true ); $attribute->set_variation( true ); $product->set_attributes( array( $attribute ) );
  } else { $product->set_regular_price( $data[2] ); if ( $index > 3 ) { $product->set_sale_price( (string) ( (float) $data[2] - 4 ) ); } }
  $id = $product->save(); update_post_meta( $id, '_avela_demo_product', $data[0] );
  if ( 3 === $index ) {
   foreach ( array( __( 'Hand towel', 'avela' ) => '24', __( 'Bath towel', 'avela' ) => '44' ) as $size => $price ) {
    $variation = new WC_Product_Variation(); $variation->set_parent_id( $id ); $variation->set_attributes( array( sanitize_title( __( 'Size', 'avela' ) ) => $size ) ); $variation->set_regular_price( $price ); $variation->set_status( 'publish' ); $variation->save();
   }
   WC_Product_Variable::sync( $id );
  }
  update_post_meta( $id, '_avela_demo_fingerprint', avela_demo_fingerprint( $id ) );
  $report[] = sprintf( __( '%s: created sample product. Prices use your existing store currency.', 'avela' ), $data[1] );
 }
 return $report;
}
function avela_cleanup_demo() {
 if ( ! current_user_can( 'manage_options' ) || ! class_exists( 'WooCommerce' ) ) { return array( __( 'WooCommerce and administrator access are required.', 'avela' ) ); }
 $report = array();
 $products = get_posts( array( 'post_type' => 'product', 'post_status' => array( 'publish', 'draft', 'private', 'pending' ), 'meta_key' => '_avela_demo_product', 'posts_per_page' => -1 ) );
 foreach ( $products as $product ) {
  if ( get_post_meta( $product->ID, '_avela_demo_fingerprint', true ) !== avela_demo_fingerprint( $product->ID ) ) { $report[] = sprintf( __( '%s: edited sample preserved. Remove manually if desired.', 'avela' ), $product->post_title ); continue; }
  wp_trash_post( $product->ID ); $report[] = sprintf( __( '%s: moved sample product to Trash.', 'avela' ), $product->post_title );
 }
 $report[] = __( 'Pages, categories, menus and images are retained because they may be in use. Sample images are labelled “Avela sample image” in Media; delete them manually only after replacing all uses. Restore products from Trash if needed.', 'avela' );
 return $report;
}
function avela_setup_action() {
 if ( ! current_user_can( 'manage_options' ) ) { wp_die( esc_html__( 'Administrator access is required.', 'avela' ) ); }
 check_admin_referer( 'avela_store_setup' );
 if ( get_option( 'avela_setup_lock' ) && (int) get_option( 'avela_setup_lock' ) < time() - 300 ) { delete_option( 'avela_setup_lock' ); }
 $cleanup = isset( $_POST['avela_cleanup'] );
 $report = $cleanup ? avela_cleanup_demo() : avela_run_setup( ! empty( $_POST['avela_demo'] ) );
 set_transient( 'avela_setup_report_' . get_current_user_id(), $report, HOUR_IN_SECONDS );
 wp_safe_redirect( admin_url( 'themes.php?page=avela-setup' ) ); exit;
}
add_action( 'admin_post_avela_store_setup', 'avela_setup_action' );
function avela_welcome_page() {
 if ( ! current_user_can( 'manage_options' ) ) { return; }
 $report = get_transient( 'avela_setup_report_' . get_current_user_id() );
 ?>
 <div class="wrap" style="max-width:1040px"><h1><?php esc_html_e( 'Welcome to Avela', 'avela' ); ?> <small>1.1.0</small></h1>
 <p><?php esc_html_e( 'Your store, thoughtfully put together. No parent theme, importer or paid builder required.', 'avela' ); ?></p>
 <?php if ( $report ) : ?><div class="notice notice-success inline"><h2><?php esc_html_e( 'Setup report', 'avela' ); ?></h2><ul><?php foreach ( $report as $line ) { echo '<li>' . esc_html( $line ) . '</li>'; } ?></ul></div><?php endif; ?>
 <h2><?php esc_html_e( '1. Install WooCommerce', 'avela' ); ?></h2>
 <p><?php esc_html_e( 'WooCommerce provides products, accounts and checkout. Avela activates safely without it. No plugin is downloaded or activated by this theme.', 'avela' ); ?></p>
 <?php if ( ! class_exists( 'WooCommerce' ) ) : ?><a class="button button-primary" href="<?php echo esc_url( admin_url( 'plugin-install.php?s=woocommerce&tab=search&type=term' ) ); ?>"><?php esc_html_e( 'Install WooCommerce through WordPress', 'avela' ); ?></a><?php else : ?><p>✓ <?php esc_html_e( 'WooCommerce is active.', 'avela' ); ?></p><?php endif; ?>
 <h2><?php esc_html_e( '2. Prepare your pages and navigation', 'avela' ); ?></h2>
 <p><?php esc_html_e( 'Setup creates only missing pages and unassigned menus. Existing content, menu assignments, products, store settings and static homepages are preserved. Existing pages are reused without changing their content. Review reused pages to confirm they have the right WooCommerce functionality.', 'avela' ); ?></p>
 <details><summary><?php esc_html_e( 'Review existing pages and conflicts', 'avela' ); ?></summary><table class="widefat striped"><thead><tr><th><?php esc_html_e( 'Page', 'avela' ); ?></th><th><?php esc_html_e( 'Planned action', 'avela' ); ?></th></tr></thead><tbody><?php foreach ( avela_page_definitions() as $slug => $definition ) : $page = avela_existing_page( $slug, $definition ); ?><tr><td><?php echo esc_html( $definition['title'] ); ?></td><td><?php echo esc_html( $page ? sprintf( __( 'Preserve existing page #%1$d (%2$s); review its content.', 'avela' ), $page->ID, $page->post_status ) : sprintf( __( 'Create missing page (%s).', 'avela' ), $definition['status'] ) ); ?></td></tr><?php endforeach; ?></tbody></table></details>
 <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><?php wp_nonce_field( 'avela_store_setup' ); ?><input type="hidden" name="action" value="avela_store_setup"><p><label><input type="checkbox" name="avela_demo" value="1"> <?php esc_html_e( 'Add optional sample content: six sample products, four categories and seven original images. Includes a variable product. No reviews, orders, customer accounts or store configuration are imported.', 'avela' ); ?></label></p><p><?php esc_html_e( 'Sample products are published and purchasable for testing. Use a private staging site, then remove or replace them before launch. Sample prices use your existing currency.', 'avela' ); ?></p><button class="button button-primary" <?php disabled( ! class_exists( 'WooCommerce' ) ); ?>><?php esc_html_e( 'Set up my store', 'avela' ); ?></button></form>
 <h2><?php esc_html_e( '3. Make it yours', 'avela' ); ?></h2><ul>
 <li><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=page' ) ); ?>"><?php esc_html_e( 'Edit pages and homepage', 'avela' ); ?></a> — <?php esc_html_e( 'Use the block editor. Open List View to select, rename, reorder or remove a section. Select an image to replace it. Select a product collection to choose featured, newest or sale products.', 'avela' ); ?></li>
 <li><a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>"><?php esc_html_e( 'Edit logo, store name, colors, heading style, announcement and shared contact details', 'avela' ); ?></a></li>
 <li><a href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>"><?php esc_html_e( 'Edit navigation menus', 'avela' ); ?></a> · <a href="<?php echo esc_url( admin_url( 'options-reading.php' ) ); ?>"><?php esc_html_e( 'Choose a different homepage', 'avela' ); ?></a></li>
 <li><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=product' ) ); ?>"><?php esc_html_e( 'Manage products, images and featured selections', 'avela' ); ?></a></li></ul>
 <h2><?php esc_html_e( '4. Configure your business before launch', 'avela' ); ?></h2><p><?php esc_html_e( 'Review all starter text. Publish your reviewed Shipping and Returns, Privacy Policy and Terms and Conditions drafts, then add them to navigation. Choose the privacy page in Settings → Privacy and the terms page in WooCommerce → Settings → Advanced. Configure your own country, currency, taxes, shipping, accounts and payment provider in WooCommerce. Test checkout and your payment provider; no gateway is configured by Avela.', 'avela' ); ?></p><p><a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings' ) ); ?>"><?php esc_html_e( 'WooCommerce settings', 'avela' ); ?></a></p>
 <details><summary><?php esc_html_e( 'Remove unedited sample products', 'avela' ); ?></summary><p><?php esc_html_e( 'Moves only unedited products created by this setup to Trash. Edited samples and all other products are preserved. Images, categories, pages and menus remain for safety; see the customer guide for manual cleanup.', 'avela' ); ?></p><form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><?php wp_nonce_field( 'avela_store_setup' ); ?><input type="hidden" name="action" value="avela_store_setup"><button class="button" name="avela_cleanup" value="1"><?php esc_html_e( 'Move unedited sample products to Trash', 'avela' ); ?></button></form></details></div>
 <?php
}
