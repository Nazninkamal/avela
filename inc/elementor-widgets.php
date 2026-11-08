<?php
/** Elementor Free widgets backed by normal WordPress and WooCommerce data. */
if ( ! defined( 'ABSPATH' ) || ! class_exists( '\Elementor\Widget_Base' ) ) { exit; }
class Avela_Elementor_Widget extends \Elementor\Widget_Base {
 private $avela_kind;
 public function __construct( $data = array(), $args = null, $kind = 'products' ) {
  $this->avela_kind = ! empty( $data['widgetType'] ) ? str_replace( 'avela-', '', $data['widgetType'] ) : $kind;
  parent::__construct( $data, $args );
 }
 public function get_name() { return 'avela-' . $this->avela_kind; }
 public function get_title() {
  $names = array( 'products' => __( 'Avela Products', 'avela' ), 'categories' => __( 'Avela Categories', 'avela' ), 'brand' => __( 'Avela Store Identity', 'avela' ), 'search' => __( 'Avela Product Search', 'avela' ), 'actions' => __( 'Avela Account & Cart', 'avela' ), 'navigation' => __( 'Avela Store Navigation', 'avela' ), 'footer-menu' => __( 'Avela Footer Links', 'avela' ), 'contact' => __( 'Avela Contact Details', 'avela' ), 'copyright' => __( 'Avela Copyright & Policies', 'avela' ) );
  return $names[ $this->avela_kind ] ?? __( 'Avela Store', 'avela' );
 }
 public function get_icon() { return 'eicon-woocommerce'; }
 public function get_categories() { return array( 'avela-store' ); }
 public function get_keywords() { return array( 'avela', 'shop', 'store', 'woocommerce' ); }
 protected function register_controls() {
  $this->start_controls_section( 'avela_content', array( 'label' => __( 'Store content', 'avela' ) ) );
  if ( 'products' === $this->avela_kind ) {
   $this->add_control( 'mode', array( 'label' => __( 'Collection', 'avela' ), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => 'featured', 'options' => array( 'featured' => __( 'Featured', 'avela' ), 'new' => __( 'New arrivals', 'avela' ), 'sale' => __( 'On sale', 'avela' ) ) ) );
   $this->add_control( 'count', array( 'label' => __( 'Number of products', 'avela' ), 'type' => \Elementor\Controls_Manager::NUMBER, 'min' => 1, 'max' => 12, 'default' => 4 ) );
   $this->add_responsive_control( 'columns', array( 'label' => __( 'Columns', 'avela' ), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => '4', 'tablet_default' => '2', 'mobile_default' => '2', 'options' => array( '1' => '1', '2' => '2', '3' => '3', '4' => '4' ), 'selectors' => array( '{{WRAPPER}} ul.products' => 'grid-template-columns:repeat({{VALUE}},minmax(0,1fr));' ) ) );
   $this->add_control( 'image_ratio', array( 'label' => __( 'Image proportions', 'avela' ), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => '4 / 5', 'options' => array( '4 / 5' => __( 'Portrait', 'avela' ), '1 / 1' => __( 'Square', 'avela' ), '5 / 4' => __( 'Landscape', 'avela' ) ), 'selectors' => array( '{{WRAPPER}} ul.products li.product a img' => 'aspect-ratio:{{VALUE}};' ) ) );
   $this->add_control( 'catalog_help', array( 'type' => \Elementor\Controls_Manager::RAW_HTML, 'raw' => esc_html__( 'Edit product information, images, prices and stock in Products. Mark featured products with the star in the Products list. No Elementor Pro is needed.', 'avela' ) ) );
  } elseif ( 'copyright' === $this->avela_kind ) {
   $this->add_control( 'suffix', array( 'label' => __( 'Copyright suffix', 'avela' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => __( 'All rights reserved.', 'avela' ) ) );
   $this->add_control( 'copyright_help', array( 'type' => \Elementor\Controls_Manager::RAW_HTML, 'raw' => esc_html__( 'Year and store name update automatically. Only published policy pages are linked; review drafts before publishing.', 'avela' ) ) );
  } elseif ( in_array( $this->avela_kind, array( 'navigation', 'footer-menu' ), true ) ) {
   $this->add_control( 'location', array( 'label' => __( 'WordPress menu location', 'avela' ), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => 'footer-menu' === $this->avela_kind ? 'footer' : 'primary', 'options' => array( 'primary' => __( 'Main navigation', 'avela' ), 'categories' => __( 'Categories', 'avela' ), 'footer' => __( 'Footer', 'avela' ) ) ) );
   $this->add_control( 'menu_help', array( 'type' => \Elementor\Controls_Manager::RAW_HTML, 'raw' => esc_html__( 'Manage links in Appearance → Menus. Native WordPress menus keep navigation consistent across the store.', 'avela' ) ) );
  } else {
   $help = 'contact' === $this->avela_kind ? __( 'Edit shared email, telephone and address in Appearance → Customize → Avela store details & style. Changes appear in both the footer and Contact page.', 'avela' ) : ( 'brand' === $this->avela_kind ? __( 'Change your logo and store name in Appearance → Customize → Site Identity. Layout, spacing and colors are editable here in Elementor.', 'avela' ) : __( 'This widget uses the current store data and native WooCommerce links.', 'avela' ) );
   $this->add_control( 'shared_help', array( 'type' => \Elementor\Controls_Manager::RAW_HTML, 'raw' => esc_html( $help ) ) );
  }
  $this->end_controls_section();
  $this->start_controls_section( 'avela_style', array( 'label' => __( 'Appearance', 'avela' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ) );
  $this->add_control( 'text_color', array( 'label' => __( 'Text color', 'avela' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}}' => 'color:{{VALUE}};', '{{WRAPPER}} a, {{WRAPPER}} .woocommerce ul.products li.product .price, {{WRAPPER}} .avela-contact-details p' => 'color:{{VALUE}};' ) ) );
  if ( 'products' === $this->avela_kind ) {
   $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array( 'name' => 'product_title', 'label' => __( 'Product title', 'avela' ), 'selector' => '{{WRAPPER}} .woocommerce ul.products li.product .woocommerce-loop-product__title' ) );
   $this->add_control( 'button_color', array( 'label' => __( 'Button background', 'avela' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} ul.products li.product .button' => 'background:{{VALUE}};border-color:{{VALUE}};' ) ) );
  }
  if ( 'products' !== $this->avela_kind ) {
   $selectors = array( 'brand' => '.avela-brand>a', 'navigation' => '.avela-menu a', 'footer-menu' => '.avela-e-footer-links a', 'contact' => '.avela-contact-details', 'copyright' => '.avela-e-copyright-line', 'search' => 'input', 'actions' => '.avela-e-actions a', 'categories' => '.avela-category-grid>a>span' );
   $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array( 'name' => 'store_typography', 'label' => __( 'Typography', 'avela' ), 'selector' => '{{WRAPPER}} ' . ( $selectors[ $this->avela_kind ] ?? '' ) ) );
  }
  $this->end_controls_section();
 }
 protected function render() {
  $s = $this->get_settings_for_display();
  switch ( $this->avela_kind ) {
   case 'products':
    echo avela_catalog_block( array( 'mode' => $s['mode'] ?? 'featured', 'count' => $s['count'] ?? 4 ) ); // Escaped by the catalog renderer/WooCommerce.
    break;
   case 'categories': echo avela_categories_block(); break;
   case 'brand':
    echo '<div class="avela-brand">';
    if ( has_custom_logo() ) { the_custom_logo(); } else { echo '<a href="' . esc_url( home_url( '/' ) ) . '" rel="home">' . esc_html( get_bloginfo( 'name' ) ) . '<span aria-hidden="true">.</span></a>'; }
    echo '</div>'; break;
   case 'search': avela_product_search(); break;
   case 'contact': echo avela_contact_block(); break;
   case 'actions':
    if ( function_exists( 'wc_get_cart_url' ) ) {
     echo '<div class="avela-e-actions"><a aria-label="' . esc_attr__( 'My account', 'avela' ) . '" href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '"><svg width="20" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="1.5"/><path d="M4 22v-3a8 8 0 0 1 16 0v3" stroke="currentColor" stroke-width="1.5"/></svg><span>' . esc_html__( 'Account', 'avela' ) . '</span></a><a href="' . esc_url( wc_get_cart_url() ) . '"><svg width="20" height="22" viewBox="0 0 20 22" fill="none" aria-hidden="true"><path d="M2 7h16l1 13H1L2 7Z M6 8V5a4 4 0 0 1 8 0v3" stroke="currentColor" stroke-width="1.4"/></svg><span class="screen-reader-text">' . esc_html__( 'View cart', 'avela' ) . '</span>' . avela_cart_badge() . '</a></div>';
    } break;
   case 'navigation':
    $location = in_array( $s['location'] ?? '', array( 'primary', 'categories', 'footer' ), true ) ? $s['location'] : 'primary';
    $id = wp_unique_id( 'avela-navigation-' );
    echo '<div class="avela-e-nav"><button type="button" class="avela-menu-toggle" aria-expanded="false" aria-controls="' . esc_attr( $id ) . '">' . esc_html__( 'Browse the store', 'avela' ) . ' <span aria-hidden="true">☰</span></button><nav id="' . esc_attr( $id ) . '" class="avela-navigation" aria-label="' . esc_attr__( 'Store navigation', 'avela' ) . '">';
    wp_nav_menu( array( 'theme_location' => $location, 'container' => false, 'menu_class' => 'avela-menu', 'fallback_cb' => 'avela_primary_fallback' ) ); echo '</nav></div>'; break;
   case 'copyright':
    echo '<div class="avela-e-copyright-line"><span>© ' . esc_html( wp_date( 'Y' ) . ' ' . get_bloginfo( 'name' ) . '. ' . sanitize_text_field( $s['suffix'] ?? '' ) ) . '</span><div class="avela-e-policy-links">';
    $privacy = absint( get_option( 'wp_page_for_privacy_policy' ) ); $terms = function_exists( 'wc_get_page_id' ) ? wc_get_page_id( 'terms' ) : 0;
    foreach ( array( 'privacy-policy' => $privacy, 'terms-and-conditions' => $terms, 'shipping-and-returns' => 0 ) as $slug => $policy_id ) {
     $page = $policy_id > 0 ? get_post( $policy_id ) : get_page_by_path( $slug );
     if ( $page && 'publish' === $page->post_status ) { echo '<a href="' . esc_url( get_permalink( $page ) ) . '">' . esc_html( get_the_title( $page ) ) . '</a>'; }
    }
    echo '</div></div>'; break;
   case 'footer-menu':
    $location = in_array( $s['location'] ?? '', array( 'primary', 'categories', 'footer' ), true ) ? $s['location'] : 'footer';
    wp_nav_menu( array( 'theme_location' => $location, 'container' => false, 'menu_class' => 'avela-e-footer-links', 'fallback_cb' => 'avela_primary_fallback', 'depth' => 1 ) ); break;
  }
 }
}
