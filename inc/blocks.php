<?php
/** Small server-rendered catalog blocks; all merchandising is native WooCommerce. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
function avela_product_search() {
 $id = wp_unique_id( 'avela-products-' ); ?>
 <form role="search" method="get" class="avela-product-search" action="<?php echo esc_url( home_url( '/' ) ); ?>"><label class="screen-reader-text" for="<?php echo esc_attr( $id ); ?>"><?php esc_html_e( 'Search products', 'avela' ); ?></label><input id="<?php echo esc_attr( $id ); ?>" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php echo class_exists( 'WooCommerce' ) ? esc_attr__( 'Search your everyday essentials…', 'avela' ) : esc_attr__( 'Search the site…', 'avela' ); ?>" required><?php if ( class_exists( 'WooCommerce' ) ) : ?><input type="hidden" name="post_type" value="product"><?php endif; ?><button type="submit" aria-label="<?php esc_attr_e( 'Search', 'avela' ); ?>"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.5"/><path d="m15 15 6 6" stroke="currentColor" stroke-width="1.5"/></svg></button></form>
 <?php
}
function avela_page_url( $slug ) {
 $page = get_page_by_path( $slug );
 return $page && 'publish' === $page->post_status ? get_permalink( $page ) : home_url( '/' );
}
function avela_register_blocks() {
 wp_register_script( 'avela-editor', get_theme_file_uri( 'assets/blocks.js' ), array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-server-side-render' ), AVELA_VERSION, true );
 wp_set_script_translations( 'avela-editor', 'avela', get_theme_file_path( 'languages' ) );
 register_block_type( 'avela/catalog', array(
  'api_version' => 3, 'editor_script' => 'avela-editor',
  'attributes' => array( 'mode' => array( 'type' => 'string', 'default' => 'featured' ), 'count' => array( 'type' => 'number', 'default' => 4 ) ),
  'render_callback' => 'avela_catalog_block',
  'supports' => array( 'html' => false, 'className' => false ),
 ) );
 register_block_type( 'avela/categories', array( 'api_version' => 3, 'editor_script' => 'avela-editor', 'render_callback' => 'avela_categories_block', 'supports' => array( 'html' => false, 'className' => false ) ) );
 register_block_type( 'avela/contact', array( 'api_version' => 3, 'editor_script' => 'avela-editor', 'render_callback' => 'avela_contact_block', 'supports' => array( 'html' => false ) ) );
 register_block_type( 'avela/store-page', array( 'api_version' => 3, 'editor_script' => 'avela-editor', 'attributes' => array( 'kind' => array( 'type' => 'string', 'default' => 'tracking' ) ), 'render_callback' => 'avela_store_page_block', 'supports' => array( 'html' => false ) ) );
}
add_action( 'init', 'avela_register_blocks' );
function avela_catalog_block( $attributes ) {
 if ( ! class_exists( 'WooCommerce' ) ) { return avela_store_unavailable(); }
 $mode = isset( $attributes['mode'] ) && in_array( $attributes['mode'], array( 'featured', 'new', 'sale' ), true ) ? $attributes['mode'] : 'featured';
 $count = min( 12, max( 1, absint( $attributes['count'] ?? 4 ) ) );
 $extra = 'featured' === $mode ? ' visibility="featured"' : ( 'sale' === $mode ? ' on_sale="true"' : '' );
 $html = do_shortcode( '[products limit="' . $count . '" columns="4" orderby="date" order="DESC"' . $extra . ']' );
 if ( false === strpos( $html, 'class="product ' ) && false === strpos( $html, 'class="product type-' ) ) {
  // WooCommerce controls catalog visibility and stock visibility itself.
  return '<div class="avela-empty"><p>' . esc_html__( 'This collection is being prepared. Explore the shop for more.', 'avela' ) . '</p><a href="' . esc_url( avela_shop_url() ) . '">' . esc_html__( 'Browse the shop', 'avela' ) . '</a></div>';
 }
 return '<div ' . get_block_wrapper_attributes( array( 'class' => 'avela-catalog' ) ) . '>' . $html . '</div>';
}
function avela_categories_block() {
 if ( ! taxonomy_exists( 'product_cat' ) ) { return ''; }
 $categories = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'parent' => 0, 'number' => 4, 'exclude' => array( (int) get_option( 'default_product_cat' ) ) ) );
 if ( is_wp_error( $categories ) || ! $categories ) { return '<p>' . esc_html__( 'Product categories will appear here as your collection grows.', 'avela' ) . '</p>'; }
 $html = '<div ' . get_block_wrapper_attributes( array( 'class' => 'avela-category-grid' ) ) . '>';
 foreach ( $categories as $category ) {
  $url = get_term_link( $category ); if ( is_wp_error( $url ) ) { continue; }
  $image = absint( get_term_meta( $category->term_id, 'thumbnail_id', true ) );
  $html .= '<a href="' . esc_url( $url ) . '">' . ( $image ? wp_get_attachment_image( $image, 'medium_large', false, array( 'alt' => '', 'loading' => 'lazy' ) ) : '<div class="avela-category-placeholder" aria-hidden="true">' . esc_html( wp_html_excerpt( $category->name, 1, '' ) ) . '</div>' ) . '<span>' . esc_html( $category->name ) . '<span class="avela-arrow" aria-hidden="true"></span></span></a>';
 }
 return $html . '</div>';
}
function avela_store_unavailable() {
 $html = '<div class="avela-empty"><h2>' . esc_html__( 'Our collection is coming soon.', 'avela' ) . '</h2><p>' . esc_html__( 'Please check back soon or visit our contact page.', 'avela' ) . '</p>';
 if ( current_user_can( 'edit_theme_options' ) ) { $html .= '<a href="' . esc_url( admin_url( 'themes.php?page=avela-setup' ) ) . '">' . esc_html__( 'Set up your store with WooCommerce', 'avela' ) . '</a>'; }
 return $html . '</div>';
}
function avela_store_page_block( $attributes ) {
 if ( ! class_exists( 'WooCommerce' ) ) { return avela_store_unavailable(); }
 $map = array( 'tracking' => '[woocommerce_order_tracking]', 'cart' => '[woocommerce_cart]', 'checkout' => '[woocommerce_checkout]', 'account' => '[woocommerce_my_account]' );
 $kind = $attributes['kind'] ?? 'tracking';
 return do_shortcode( $map[ $kind ] ?? $map['tracking'] );
}
function avela_contact_block() {
 $html = '<div ' . get_block_wrapper_attributes( array( 'class' => 'avela-contact-details' ) ) . '>';
 $email = sanitize_email( get_theme_mod( 'avela_contact_email' ) );
 if ( $email ) { $html .= '<p><a href="' . esc_url( 'mailto:' . $email ) . '">' . esc_html( $email ) . '</a></p>'; }
 $phone = get_theme_mod( 'avela_contact_phone' );
 if ( $phone ) { $html .= '<p><a href="' . esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $phone ) ) . '">' . esc_html( $phone ) . '</a></p>'; }
 $html .= '<p>' . nl2br( esc_html( get_theme_mod( 'avela_contact_address', '' ) ) ) . '</p>';
 if ( ! $email && ! $phone ) { $html .= '<p>' . esc_html__( 'Contact information will be available soon.', 'avela' ) . '</p>'; }
 return $html . '</div>';
}
