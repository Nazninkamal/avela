<?php
/** Avela storefront. Copyright 2026 Naznin Kamal. GPL-2.0-or-later. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
define( 'AVELA_VERSION', '1.1.0' );
function avela_setup() {
 load_theme_textdomain( 'avela', get_template_directory() . '/languages' );
 foreach ( array( 'title-tag', 'post-thumbnails', 'automatic-feed-links', 'responsive-embeds', 'align-wide', 'woocommerce', 'wc-product-gallery-zoom', 'wc-product-gallery-lightbox', 'wc-product-gallery-slider', 'editor-styles' ) as $support ) { add_theme_support( $support ); }
 add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
 add_theme_support( 'custom-logo', array( 'height' => 80, 'width' => 240, 'flex-height' => true, 'flex-width' => true ) );
 add_editor_style( array( 'style.css', 'assets/editor.css' ) );
 register_nav_menus( array( 'primary' => __( 'Main navigation', 'avela' ), 'categories' => __( 'Category navigation', 'avela' ), 'footer' => __( 'Footer navigation', 'avela' ) ) );
}
add_action( 'after_setup_theme', 'avela_setup' );
function avela_assets() {
 wp_enqueue_style( 'avela', get_stylesheet_uri(), array(), AVELA_VERSION );
 wp_enqueue_script( 'avela', get_theme_file_uri( 'assets/site.js' ), array(), AVELA_VERSION, true );
 $css = ':root{';
 foreach ( array( 'accent' => '#23493d', 'paper' => '#faf8f3', 'highlight' => '#9b4b34' ) as $key => $default ) {
  $css .= '--avela-' . $key . ':' . ( sanitize_hex_color( get_theme_mod( 'avela_' . $key, $default ) ) ?: $default ) . ';';
 }
 $font = get_theme_mod( 'avela_typography', 'serif' ) === 'sans' ? 'Arial,Helvetica,sans-serif' : 'Georgia,"Times New Roman",serif';
 wp_add_inline_style( 'avela', $css . '--avela-heading-font:' . $font . ';}' );
 if ( class_exists( 'WooCommerce' ) ) { wp_enqueue_script( 'wc-cart-fragments' ); }
}
add_action( 'wp_enqueue_scripts', 'avela_assets' );
function avela_shop_url() { return function_exists( 'wc_get_page_permalink' ) && wc_get_page_id( 'shop' ) > 0 ? wc_get_page_permalink( 'shop' ) : home_url( '/' ); }
function avela_cart_badge() {
 $count = function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
 return '<span class="avela-cart-count" aria-label="' . esc_attr( sprintf( _n( '%s item in cart', '%s items in cart', $count, 'avela' ), number_format_i18n( $count ) ) ) . '">' . esc_html( $count ) . '</span>';
}
function avela_cart_fragments( $fragments ) { $fragments['.avela-cart-count'] = avela_cart_badge(); return $fragments; }
add_filter( 'woocommerce_add_to_cart_fragments', 'avela_cart_fragments' );
function avela_primary_fallback() {
 echo '<ul class="avela-menu"><li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'avela' ) . '</a></li>';
 if ( class_exists( 'WooCommerce' ) ) { echo '<li><a href="' . esc_url( avela_shop_url() ) . '">' . esc_html__( 'Shop', 'avela' ) . '</a></li>'; }
 echo '</ul>';
}
function avela_typography_sanitize( $value ) { return in_array( $value, array( 'serif', 'sans' ), true ) ? $value : 'serif'; }
function avela_customizer( $manager ) {
 $manager->add_section( 'avela_store', array( 'title' => __( 'Avela store details & style', 'avela' ), 'description' => __( 'Edit homepage content in Pages → Home. Remove any optional section in List View. These settings control the shared header and footer.', 'avela' ), 'priority' => 30 ) );
 $fields = array(
  'announcement' => array( __( 'Announcement (blank hides it)', 'avela' ), __( 'Thoughtfully chosen. Made for the everyday.', 'avela' ), 'sanitize_text_field', 'text' ),
  'footer_text' => array( __( 'Footer introduction', 'avela' ), __( 'A considered collection for your everyday rituals.', 'avela' ), 'sanitize_textarea_field', 'textarea' ),
  'contact_email' => array( __( 'Contact email', 'avela' ), '', 'sanitize_email', 'email' ),
  'contact_phone' => array( __( 'Contact telephone', 'avela' ), '', 'sanitize_text_field', 'text' ),
  'contact_address' => array( __( 'Business address / opening hours', 'avela' ), '', 'sanitize_textarea_field', 'textarea' ),
  'instagram' => array( __( 'Instagram URL (blank hides it)', 'avela' ), '', 'esc_url_raw', 'url' ),
  'facebook' => array( __( 'Facebook URL (blank hides it)', 'avela' ), '', 'esc_url_raw', 'url' ),
 );
 foreach ( $fields as $key => $field ) {
  $manager->add_setting( 'avela_' . $key, array( 'default' => $field[1], 'sanitize_callback' => $field[2] ) );
  $manager->add_control( 'avela_' . $key, array( 'label' => $field[0], 'section' => 'avela_store', 'type' => $field[3] ) );
 }
 foreach ( array( 'accent' => array( __( 'Primary color (use a dark shade for contrast)', 'avela' ), '#23493d' ), 'paper' => array( __( 'Page background (use a light shade)', 'avela' ), '#faf8f3' ), 'highlight' => array( __( 'Secondary color (use a dark shade)', 'avela' ), '#9b4b34' ) ) as $key => $field ) {
  $manager->add_setting( 'avela_' . $key, array( 'default' => $field[1], 'sanitize_callback' => 'sanitize_hex_color' ) );
  $manager->add_control( new WP_Customize_Color_Control( $manager, 'avela_' . $key, array( 'label' => $field[0], 'section' => 'avela_store' ) ) );
 }
 $manager->add_setting( 'avela_typography', array( 'default' => 'serif', 'sanitize_callback' => 'avela_typography_sanitize' ) );
 $manager->add_control( 'avela_typography', array( 'label' => __( 'Heading style', 'avela' ), 'section' => 'avela_store', 'type' => 'select', 'choices' => array( 'serif' => __( 'Editorial serif', 'avela' ), 'sans' => __( 'Modern sans serif', 'avela' ) ) ) );
}
add_action( 'customize_register', 'avela_customizer' );
require get_theme_file_path( 'inc/blocks.php' );
require get_theme_file_path( 'inc/content.php' );
require get_theme_file_path( 'inc/welcome.php' );
function avela_shop_filters() {
 if ( ! is_shop() && ! is_product_taxonomy() && ! is_search() ) { return; }
 echo '<details class="avela-filters"><summary>' . esc_html__( 'Filter products', 'avela' ) . '</summary><div class="avela-filter-grid">';
 the_widget( 'WC_Widget_Product_Categories', array( 'title' => __( 'Categories', 'avela' ), 'count' => 1, 'hide_empty' => 1 ), array( 'before_widget' => '<div>', 'after_widget' => '</div>', 'before_title' => '<h2>', 'after_title' => '</h2>' ) );
 the_widget( 'WC_Widget_Price_Filter', array( 'title' => __( 'Price', 'avela' ) ), array( 'before_widget' => '<div>', 'after_widget' => '</div>', 'before_title' => '<h2>', 'after_title' => '</h2>' ) );
 the_widget( 'WC_Widget_Layered_Nav_Filters', array( 'title' => __( 'Active filters', 'avela' ) ) );
 echo '</div></details>';
}
add_action( 'woocommerce_before_shop_loop', 'avela_shop_filters', 15 );
add_filter( 'loop_shop_columns', function () { return 4; } );
add_filter( 'loop_shop_per_page', function () { return 12; } );
function avela_no_products_help() {
 echo '<div class="avela-search-empty"><p>' . esc_html__( 'Try another product name, choose a category, or clear your filters.', 'avela' ) . '</p>';
 avela_product_search();
 echo '<p><a href="' . esc_url( avela_shop_url() ) . '">' . esc_html__( 'View all products and clear filters', 'avela' ) . '</a></p></div>';
}
add_action( 'woocommerce_no_products_found', 'avela_no_products_help', 20 );
function avela_missing_woocommerce_shortcodes() {
 if ( class_exists( 'WooCommerce' ) ) { return; }
 foreach ( array( 'woocommerce_cart', 'woocommerce_checkout', 'woocommerce_my_account', 'woocommerce_order_tracking', 'products', 'product_categories' ) as $tag ) {
  if ( ! shortcode_exists( $tag ) ) { add_shortcode( $tag, 'avela_store_unavailable' ); }
 }
}
add_action( 'init', 'avela_missing_woocommerce_shortcodes', 20 );
// Keep a single search match on the results page so shoppers can refine their query.
add_filter( 'woocommerce_redirect_single_search_result', '__return_false' );
