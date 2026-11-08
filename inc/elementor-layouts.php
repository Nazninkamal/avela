<?php
/** Native Elementor containers and Free widgets. All marketing copy/images are editable. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
function avela_e_node( $type, $settings, $elements = array(), $label = '' ) {
 static $sequence = 0; ++$sequence;
 $node = array( 'id' => substr( md5( $label . $sequence ), 0, 7 ), 'elType' => 'container' === $type ? 'container' : 'widget', 'settings' => $settings, 'elements' => $elements, 'isInner' => false );
 if ( 'container' !== $type ) { $node['widgetType'] = $type; }
 if ( $label ) { $node['settings']['_title'] = $label; }
 return $node;
}
function avela_e_container( $label, $class, $children, $settings = array() ) {
 $pad = array( 'unit' => 'px', 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'isLinked' => false );
 $mobile = $pad; $gap = 0;
 foreach ( array( 'avela-e-hero-copy' => array( 48, 42, 48, 48, 22 ), 'avela-e-story-copy' => array( 42, 45, 42, 45, 20 ), 'avela-e-editorial-copy' => array( 32, 25, 32, 25, 16 ), 'avela-e-value-card' => array( 30, 30, 30, 30, 15 ), 'avela-e-contact-card' => array( 36, 36, 36, 36, 24 ), 'avela-e-help-card' => array( 36, 36, 36, 36, 24 ), 'avela-e-seasonal-copy' => array( 0, 30, 0, 0, 20 ), 'avela-e-announcement' => array( 8, 18, 8, 18, 0 ), 'avela-e-section-heading' => array( 0, 0, 0, 0, 13 ) ) as $match => $values ) {
  if ( false !== strpos( $class, $match ) ) { $pad = array_merge( $pad, array_combine( array( 'top', 'right', 'bottom', 'left' ), array_slice( $values, 0, 4 ) ) ); $mobile = $pad; if ( max( array_slice( $values, 0, 4 ) ) > 25 ) { $mobile = array_merge( $mobile, array( 'top' => 28, 'right' => 24, 'bottom' => 28, 'left' => 24 ) ); } $gap = $values[4]; }
 }
 foreach ( $children as &$child ) {
  if ( false !== strpos( $class, 'avela-e-story-copy' ) ) { if ( 'heading' === ( $child['widgetType'] ?? '' ) ) { $child['settings']['title_color'] = '#f9f7ef'; } elseif ( 'text-editor' === ( $child['widgetType'] ?? '' ) ) { $child['settings']['text_color'] = '#e0e7dc'; } }
  if ( false !== strpos( $class, 'avela-e-announcement' ) && 'text-editor' === ( $child['widgetType'] ?? '' ) ) { $child['settings']['text_color'] = '#ffffff'; $child['settings']['typography_font_size']['size'] = 10; }
  if ( false !== strpos( $class, 'avela-e-editorial-copy' ) && 'heading' === ( $child['widgetType'] ?? '' ) ) { $child['settings']['typography_font_size']['size'] = 35; $child['settings']['typography_font_size_mobile']['size'] = 29; }
 } unset( $child );
 if ( false !== strpos( $class, 'avela-e-hero-copy' ) || false !== strpos( $class, 'avela-e-story-copy' ) ) { $settings += array( 'flex_justify_content' => 'center' ); }
 if ( false !== strpos( $class, 'avela-e-editorial-copy' ) ) { $settings += array( 'width' => array( 'unit' => '%', 'size' => 65 ), 'width_mobile' => array( 'unit' => '%', 'size' => 66 ) ); }
 return avela_e_node( 'container', array_merge( array( 'content_width' => 'full', 'flex_direction' => 'column', 'padding' => $pad, 'padding_mobile' => $mobile, 'flex_gap' => array( 'unit' => 'px', 'size' => $gap, 'row' => $gap, 'column' => $gap ), 'css_classes' => $class ), $settings ), $children, $label );
}
function avela_e_section( $label, $class, $children, $settings = array() ) {
 if ( in_array( $class, array( 'avela-e-header-row', 'avela-e-navigation-section' ), true ) ) { $y = 'avela-e-header-row' === $class ? 18 : 0; $settings['padding'] = array( 'unit' => 'px', 'top' => $y, 'right' => 48, 'bottom' => $y, 'left' => 48, 'isLinked' => false ); $settings['padding_mobile'] = array( 'unit' => 'px', 'top' => $y ? 12 : 0, 'right' => 18, 'bottom' => $y ? 8 : 0, 'left' => 18, 'isLinked' => false ); }
 return avela_e_container( $label, 'avela-e-section ' . $class, $children, array_merge( array( 'content_width' => 'boxed', 'boxed_width' => array( 'unit' => 'px', 'size' => 1280 ), 'padding' => array( 'unit' => 'px', 'top' => 58, 'right' => 48, 'bottom' => 12, 'left' => 48, 'isLinked' => false ), 'padding_mobile' => array( 'unit' => 'px', 'top' => 36, 'right' => 18, 'bottom' => 8, 'left' => 18, 'isLinked' => false ) ), $settings ) );
}
function avela_e_row( $label, $class, $children, $settings = array() ) {
 if ( in_array( $class, array( 'avela-e-hero', 'avela-e-story-panel', 'avela-e-editorial-row' ), true ) ) { $settings += array( 'flex_align_items' => 'stretch' ); }
 if ( 'avela-e-header-inner' === $class ) { $settings += array( 'flex_direction_mobile' => 'row', 'flex_wrap' => 'nowrap', 'flex_wrap_tablet' => 'wrap', 'flex_wrap_mobile' => 'wrap', 'flex_gap_tablet' => array( 'unit' => 'px', 'size' => 0, 'row' => 0, 'column' => 0 ), 'flex_gap_mobile' => array( 'unit' => 'px', 'size' => 0, 'row' => 0, 'column' => 0 ) ); }
 if ( 'avela-e-story-panel' === $class ) { $settings['flex_gap'] = array( 'unit' => 'px', 'size' => 0, 'row' => 0, 'column' => 0 ); }
 return avela_e_container( $label, $class, $children, array_merge( array( 'flex_direction' => 'row', 'flex_direction_mobile' => 'column', 'flex_gap' => array( 'unit' => 'px', 'size' => 28, 'row' => 28, 'column' => 28 ), 'flex_align_items' => 'center' ), $settings ) );
}
function avela_e_col( $label, $class, $children, $width = 50 ) {
 return avela_e_container( $label, $class, $children, array( 'flex_gap' => array( 'unit' => 'px', 'size' => 20, 'row' => 20, 'column' => 20 ), 'width' => array( 'unit' => '%', 'size' => $width ), 'width_tablet' => array( 'unit' => '%', 'size' => 'avela-e-header-search' === $class ? 100 : ( in_array( $class, array( 'avela-e-header-brand', 'avela-e-header-actions' ), true ) ? 50 : $width ) ), 'width_mobile' => array( 'unit' => '%', 'size' => in_array( $class, array( 'avela-e-header-brand', 'avela-e-header-actions' ), true ) ? 50 : 100 ) ) );
}
function avela_e_heading( $text, $tag = 'h2', $class = '' ) {
 return avela_e_node( 'heading', array( 'title' => $text, 'header_size' => $tag, 'title_color' => '#25372e', 'typography_typography' => 'custom', 'typography_font_family' => false !== strpos( $class, 'footer-title' ) ? 'Arial' : 'Avela Editorial', 'typography_font_size' => array( 'unit' => 'px', 'size' => 'h1' === $tag ? 60 : ( 'h3' === $tag ? ( false !== strpos( $class, 'footer-title' ) ? 12 : 26 ) : 40 ) ), 'typography_font_size_tablet' => array( 'unit' => 'px', 'size' => 'h1' === $tag ? 49 : ( 'h3' === $tag ? ( false !== strpos( $class, 'footer-title' ) ? 12 : 25 ) : 35 ) ), 'typography_font_size_mobile' => array( 'unit' => 'px', 'size' => 'h1' === $tag ? 45 : ( 'h3' === $tag ? ( false !== strpos( $class, 'footer-title' ) ? 12 : 24 ) : 31 ) ), 'typography_font_weight' => '400', 'typography_line_height' => array( 'unit' => 'em', 'size' => 1.1 ), 'typography_letter_spacing' => array( 'unit' => 'em', 'size' => -0.035 ), '__globals__' => array( 'title_color' => '', 'typography_typography' => '' ), '_css_classes' => 'avela-e-heading ' . $class ), array(), wp_strip_all_tags( $text ) );
}
function avela_e_text( $text, $class = '' ) {
 return avela_e_node( 'text-editor', array( 'editor' => '<p>' . esc_html( $text ) . '</p>', 'text_color' => false !== strpos( $class, 'photo-caption' ) ? '#ffffff' : ( false !== strpos( $class, 'eyebrow' ) ? '#23493d' : '#62685e' ), 'typography_typography' => 'custom', 'typography_font_family' => 'Arial', 'typography_font_size' => array( 'unit' => 'px', 'size' => false !== strpos( $class, 'eyebrow' ) ? 10 : 14 ), 'typography_font_size_mobile' => array( 'unit' => 'px', 'size' => false !== strpos( $class, 'eyebrow' ) ? 9 : 13 ), 'typography_font_weight' => false !== strpos( $class, 'eyebrow' ) ? '600' : '400', 'typography_line_height' => array( 'unit' => 'em', 'size' => 1.8 ), '__globals__' => array( 'text_color' => '', 'typography_typography' => '' ), '_css_classes' => $class ), array(), wp_trim_words( $text, 7, '' ) );
}
function avela_e_button( $text, $url, $class = '' ) {
 return avela_e_node( 'button', array( 'text' => $text, 'link' => array( 'url' => $url ), 'size' => 'md', 'button_text_color' => false !== strpos( $class, 'avela-e-link' ) ? '#23493d' : '#ffffff', 'background_background' => 'classic', 'background_color' => false !== strpos( $class, 'avela-e-link' ) || false !== strpos( $class, 'avela-e-light-button' ) ? 'rgba(0,0,0,0)' : '#1e4034', 'typography_typography' => 'custom', 'typography_font_family' => 'Arial', 'typography_font_size' => array( 'unit' => 'px', 'size' => 12 ), 'typography_font_weight' => '500', '__globals__' => array( 'background_color' => '', 'typography_typography' => '' ), '_css_classes' => 'avela-e-button ' . $class ), array(), $text );
}
function avela_e_image( $name, $class = '' ) {
 $ids = get_option( 'avela_demo_images', array() ); $id = absint( $ids[ $name ] ?? 0 );
 return avela_e_node( 'image', array( 'image' => array( 'id' => $id, 'url' => $id ? wp_get_attachment_url( $id ) : get_theme_file_uri( 'assets/demo/' . $name . '.jpg' ), 'alt' => sprintf( __( 'Avela %s collection', 'avela' ), $name ) ), 'image_size' => 'full', 'image_alt' => sprintf( __( 'Avela %s collection', 'avela' ), $name ), '_css_classes' => $class ), array(), sprintf( __( '%s image — click to replace', 'avela' ), ucfirst( $name ) ) );
}
function avela_e_collection( $mode = 'featured', $count = 4, $columns = 4 ) {
 return avela_e_node( 'avela-products', array( 'mode' => $mode, 'count' => $count, 'columns' => (string) $columns, 'columns_tablet' => '2', 'columns_mobile' => '2', 'image_ratio' => '4 / 5' ), array(), ucfirst( $mode ) . ' products — live WooCommerce collection' );
}
function avela_e_intro( $eyebrow, $heading, $text = '', $tag = 'h2' ) {
 $children = array( avela_e_text( $eyebrow, 'avela-e-eyebrow' ), avela_e_heading( $heading, $tag ) );
 if ( $text ) { $children[] = avela_e_text( $text, 'avela-e-section-intro' ); }
 return avela_e_container( $heading, 'avela-e-section-heading', $children );
}
function avela_elementor_layout( $key, $ids ) {
 $shop = avela_shop_url(); $about = ! empty( $ids['about'] ) ? get_permalink( $ids['about'] ) : avela_page_url( 'about' );
 $contact = ! empty( $ids['contact'] ) ? get_permalink( $ids['contact'] ) : avela_page_url( 'contact' );
 $faq = ! empty( $ids['faq'] ) ? get_permalink( $ids['faq'] ) : avela_page_url( 'faq' );
 if ( 'header' === $key ) {
  return array(
   avela_e_container( 'Announcement — edit this text', 'avela-e-announcement', array( avela_e_text( __( 'The everyday edit. A little more considered.', 'avela' ) ) ) ),
   avela_e_section( 'Store header', 'avela-e-header-row', array( avela_e_row( 'Logo, search and shopping links', 'avela-e-header-inner', array(
    avela_e_col( 'Store identity', 'avela-e-header-brand', array( avela_e_node( 'avela-brand', array() ) ), 25 ),
    avela_e_col( 'Product search', 'avela-e-header-search', array( avela_e_node( 'avela-search', array() ) ), 50 ),
    avela_e_col( 'Account and cart', 'avela-e-header-actions', array( avela_e_node( 'avela-actions', array() ) ), 25 ),
   ) ) ) ),
   avela_e_section( 'Main navigation', 'avela-e-navigation-section', array( avela_e_node( 'avela-navigation', array( 'location' => 'primary' ) ) ) ),
  );
 }
 if ( 'footer' === $key ) {
  return array( avela_e_section( 'Footer — edit layout and copy', 'avela-e-footer', array(
   avela_e_row( 'Footer columns', 'avela-e-footer-columns', array(
    avela_e_col( 'About your store', 'avela-e-footer-about', array( avela_e_node( 'avela-brand', array() ), avela_e_text( __( 'Thoughtfully chosen essentials for your shelf, your space, and the moments in between.', 'avela' ) ) ), 38 ),
    avela_e_col( 'Explore', '', array( avela_e_heading( __( 'Explore the store', 'avela' ), 'h3', 'avela-e-footer-title' ), avela_e_node( 'avela-footer-menu', array( 'location' => 'footer' ) ) ), 30 ),
    avela_e_col( 'Contact', '', array( avela_e_heading( __( 'We’re here to help', 'avela' ), 'h3', 'avela-e-footer-title' ), avela_e_node( 'avela-contact', array() ), avela_e_button( __( 'Contact us', 'avela' ), $contact, 'avela-e-link' ) ), 32 ),
   ), array( 'flex_align_items' => 'flex-start' ) ),
   avela_e_node( 'avela-copyright', array( 'suffix' => __( 'All rights reserved.', 'avela' ), '_css_classes' => 'avela-e-copyright' ), array(), 'Automatic copyright and published policy links' ),
  ) ) );
 }
 if ( 'about' === $key ) {
  return array(
   avela_e_section( 'Our story — opening', 'avela-e-about-hero', array( avela_e_row( 'Story introduction', '', array(
    avela_e_col( 'Your story', '', array( avela_e_text( __( 'THE AVELA POINT OF VIEW', 'avela' ), 'avela-e-eyebrow' ), avela_e_heading( __( 'Good things.<br>A more considered everyday.', 'avela' ), 'h1' ), avela_e_text( __( 'We make room for simple pleasures: a familiar texture, something useful, a little time for yourself. This is a place to find pieces you will love reaching for.', 'avela' ) ), avela_e_button( __( 'Explore the collection', 'avela' ), $shop ) ), 46 ),
    avela_e_col( 'Story photograph', '', array( avela_e_image( 'hero', 'avela-e-story-image' ) ), 54 ),
   ) ) ) ),
   avela_e_section( 'Your store values — edit each card', 'avela-e-values', array( avela_e_intro( __( 'THE THINGS THAT MATTER', 'avela' ), __( 'A little thought goes a long way.', 'avela' ) ), avela_e_row( 'Three values', '', array(
    avela_e_col( 'Value one', 'avela-e-value-card', array( avela_e_text( '01', 'avela-e-number' ), avela_e_heading( __( 'Everyday usefulness', 'avela' ), 'h3' ), avela_e_text( __( 'Pieces that fit naturally into your routine, with details you will appreciate day after day.', 'avela' ) ) ), 33 ),
    avela_e_col( 'Value two', 'avela-e-value-card', array( avela_e_text( '02', 'avela-e-number' ), avela_e_heading( __( 'Beautiful simplicity', 'avela' ), 'h3' ), avela_e_text( __( 'A considered collection of textures, colors and forms that feel good in your space.', 'avela' ) ) ), 33 ),
    avela_e_col( 'Value three', 'avela-e-value-card', array( avela_e_text( '03', 'avela-e-number' ), avela_e_heading( __( 'Your own rhythm', 'avela' ), 'h3' ), avela_e_text( __( 'There is no single way to make a day your own. Find the things that work for you.', 'avela' ) ) ), 34 ),
   ), array( 'flex_align_items' => 'stretch' ) ) ) ),
   avela_e_section( 'Brand story feature', 'avela-e-story-section', array( avela_e_row( 'Image and story', 'avela-e-story-panel', array( avela_e_col( 'Linen photograph', '', array( avela_e_image( 'linen', 'avela-e-story-image' ) ) ), avela_e_col( 'Tell your customers about your business', 'avela-e-story-copy', array( avela_e_text( __( 'A LITTLE MORE CONSIDERED', 'avela' ), 'avela-e-eyebrow' ), avela_e_heading( __( 'A little space for what you love.', 'avela' ) ), avela_e_text( __( 'Our collection brings together everyday pieces with simple forms and inviting textures. Discover something for your routine, your space, or someone you care about.', 'avela' ) ), avela_e_button( __( 'Let’s talk', 'avela' ), $contact, 'avela-e-light-button' ) ) ) ) ) ) ),
  );
 }
 if ( 'contact' === $key ) {
  return array(
   avela_e_section( 'Contact introduction', 'avela-e-contact-hero', array( avela_e_intro( __( 'A LITTLE HELP, WHEN YOU NEED IT', 'avela' ), __( 'Let’s make it simple.', 'avela' ), __( 'A question about a product? A little help with an order? Get in touch and include your order number if you have one.', 'avela' ), 'h1' ) ) ),
   avela_e_section( 'Contact information and useful links', 'avela-e-contact-main', array( avela_e_row( 'Contact columns', '', array(
    avela_e_col( 'Shared store contact details', 'avela-e-contact-card', array( avela_e_heading( __( 'Get in touch', 'avela' ), 'h2' ), avela_e_node( 'avela-contact', array() ) ), 42 ),
    avela_e_col( 'Helpful answers', 'avela-e-help-card', array( avela_e_text( __( 'GOOD TO KNOW', 'avela' ), 'avela-e-eyebrow' ), avela_e_heading( __( 'Your answer might be right here.', 'avela' ) ), avela_e_text( __( 'Check an order’s status or find answers to common shopping questions.', 'avela' ) ), avela_e_button( __( 'Read the FAQ', 'avela' ), $faq ), avela_e_button( __( 'Track an order', 'avela' ), avela_page_url( 'order-tracking' ), 'avela-e-link' ) ), 58 ),
   ), array( 'flex_align_items' => 'stretch' ) ) ) ),
  );
 }
 if ( 'faq' === $key ) {
  $questions = array(
   array( 'tab_title' => __( 'Where is my order?', 'avela' ), 'tab_content' => '<p>' . esc_html__( 'Use Order Tracking with your order number and billing email address. You can also see orders in your customer account. Carrier tracking depends on the shipping service used by the store.', 'avela' ) . '</p>' ),
   array( 'tab_title' => __( 'How do I choose an option?', 'avela' ), 'tab_content' => '<p>' . esc_html__( 'Open the product page and choose the available size, color or other option before adding the item to your cart. The selected option and current price will appear before checkout.', 'avela' ) . '</p>' ),
   array( 'tab_title' => __( 'Can I return an item?', 'avela' ), 'tab_content' => '<p>' . esc_html__( 'Please contact our team with your order number for help. Refer to the store’s published shipping and returns policy for the conditions that apply to your order.', 'avela' ) . '</p>' ),
   array( 'tab_title' => __( 'Where do you deliver?', 'avela' ), 'tab_content' => '<p>' . esc_html__( 'Available shipping methods and costs are shown at checkout for your address. Contact us if you need help choosing an available delivery method.', 'avela' ) . '</p>' ),
   array( 'tab_title' => __( 'How can I get in touch?', 'avela' ), 'tab_content' => '<p>' . esc_html__( 'Visit the Contact page for the store’s email, telephone and business hours. Include your order number when asking about an existing order.', 'avela' ) . '</p>' ),
  );
  return array( avela_e_section( 'FAQ heading', 'avela-e-faq-intro', array( avela_e_intro( __( 'HERE TO HELP', 'avela' ), __( 'A few helpful answers.', 'avela' ), __( 'The little details that make shopping simpler.', 'avela' ), 'h1' ) ) ), avela_e_section( 'Questions — edit each answer in Elementor', 'avela-e-faq-list', array( avela_e_node( 'accordion', array( 'tabs' => $questions, 'title_html_tag' => 'h3', 'border_width' => array( 'unit' => 'px', 'size' => 0 ) ), array(), 'Editable questions and answers' ), avela_e_heading( __( 'Still have a question?', 'avela' ) ), avela_e_button( __( 'Contact our team', 'avela' ), $contact ) ) ) );
 }
 // Homepage: lead with live products immediately after the hero.
 $home = array(
  avela_e_section( 'Hero — edit title, photograph and buttons', 'avela-e-hero-section', array( avela_e_row( 'Hero layout', 'avela-e-hero', array(
   avela_e_col( 'Hero content', 'avela-e-hero-copy', array( avela_e_text( __( 'THE EVERYDAY EDIT / AVELA', 'avela' ), 'avela-e-eyebrow' ), avela_e_heading( __( 'Good things.<br>For your everyday.', 'avela' ), 'h1' ), avela_e_text( __( 'Beauty, home, and the little things in between. Discover a considered collection to make your daily rituals feel more like you.', 'avela' ) ), avela_e_button( __( 'Shop the collection', 'avela' ), $shop ), avela_e_button( __( 'Discover our story', 'avela' ), $about, 'avela-e-link' ) ), 43 ),
   avela_e_col( 'Hero photography', 'avela-e-hero-visual', array( avela_e_image( 'hero', 'avela-e-hero-image' ), avela_e_text( __( 'A LITTLE MORE CONSIDERED.', 'avela' ), 'avela-e-photo-caption' ) ), 57 ),
  ), array( 'flex_gap' => array( 'unit' => 'px', 'size' => 0, 'row' => 0, 'column' => 0 ) ) ) ), array( 'padding' => array( 'unit' => 'px', 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'isLinked' => true ), 'padding_mobile' => array( 'unit' => 'px', 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'isLinked' => true ), 'boxed_width' => array( 'unit' => 'px', 'size' => 1600 ) ) ),
  avela_e_section( 'Shop by category — live category images', 'avela-e-categories-section', array( avela_e_row( 'Category introduction', 'avela-e-section-top', array( avela_e_intro( __( 'FIND YOUR EVERYDAY', 'avela' ), __( 'A place for all your favourites.', 'avela' ) ), avela_e_button( __( 'Shop all', 'avela' ), $shop, 'avela-e-link' ) ) ), avela_e_node( 'avela-categories', array() ) ) ),
  avela_e_section( 'Featured collection — change products in widget settings', 'avela-e-featured', array( avela_e_row( 'Featured heading', 'avela-e-section-top', array( avela_e_intro( __( 'THE CONSIDERED COLLECTION', 'avela' ), __( 'Meet your new essentials.', 'avela' ) ), avela_e_text( __( 'Easy to reach for. Lovely to live with.', 'avela' ), 'avela-e-aside-note' ) ) ), avela_e_collection() ) ),
  avela_e_section( 'Editorial feature — optional, remove this whole section', 'avela-e-editorial-section', array( avela_e_row( 'Two editorial cards', 'avela-e-editorial-row', array(
   avela_e_col( 'Daily ritual card', 'avela-e-editorial-card', array( avela_e_image( 'serum', 'avela-e-editorial-image' ), avela_e_container( 'Edit ritual card copy', 'avela-e-editorial-copy', array( avela_e_text( __( 'A MOMENT FOR YOURSELF', 'avela' ), 'avela-e-eyebrow' ), avela_e_heading( __( 'Make room for a little ritual.', 'avela' ) ), avela_e_button( __( 'Explore daily care', 'avela' ), $shop, 'avela-e-link' ) ) ) ) ),
   avela_e_col( 'Home collection card', 'avela-e-editorial-card avela-e-editorial-card-alt', array( avela_e_image( 'candle', 'avela-e-editorial-image' ), avela_e_container( 'Edit home card copy', 'avela-e-editorial-copy', array( avela_e_text( __( 'SETTLE INTO YOUR SPACE', 'avela' ), 'avela-e-eyebrow' ), avela_e_heading( __( 'Little things. Lovely spaces.', 'avela' ) ), avela_e_button( __( 'Discover the collection', 'avela' ), $shop, 'avela-e-link' ) ) ) ) ),
  ) ) ) ),
  avela_e_section( 'New arrivals — automatic live products', 'avela-e-new', array( avela_e_intro( __( 'JUST LANDED', 'avela' ), __( 'Something fresh for your routine.', 'avela' ) ), avela_e_collection( 'new' ) ) ),
  avela_e_section( 'Our story — edit brand introduction', 'avela-e-story-section', array( avela_e_row( 'Brand story panel', 'avela-e-story-panel', array( avela_e_col( 'Story photograph', '', array( avela_e_image( 'linen', 'avela-e-story-image' ) ) ), avela_e_col( 'Brand story copy', 'avela-e-story-copy', array( avela_e_text( __( 'LESS RUSH. MORE RITUAL.', 'avela' ), 'avela-e-eyebrow' ), avela_e_heading( __( 'The everyday,<br>beautifully considered.', 'avela' ) ), avela_e_text( __( 'A familiar texture. A favourite corner. Something beautiful to reach for. We bring together thoughtful pieces for your routine and your space, so the ordinary can feel a little more special.', 'avela' ) ), avela_e_button( __( 'Get to know us', 'avela' ), $about, 'avela-e-light-button' ) ) ) ) ) ) ),
  avela_e_section( 'Seasonal edit — optional sale section', 'avela-e-seasonal', array( avela_e_row( 'Sale introduction and collection', '', array( avela_e_col( 'Seasonal copy', 'avela-e-seasonal-copy', array( avela_e_text( __( 'THE SEASONAL EDIT', 'avela' ), 'avela-e-eyebrow' ), avela_e_heading( __( 'A little find.<br>A lovely price.', 'avela' ) ), avela_e_text( __( 'Discover selected pieces from the collection at their current sale price. Keep an eye on this space for the next edit.', 'avela' ) ), avela_e_button( __( 'Browse the store', 'avela' ), $shop, 'avela-e-link' ) ), 36 ), avela_e_col( 'Live sale products', '', array( avela_e_collection( 'sale', 2, 2 ) ), 64 ) ) ) ) ),
  avela_e_section( 'Helpful links — edit text and destinations', 'avela-e-help-strip', array( avela_e_row( 'Shopping shortcuts', '', array( avela_e_col( 'Browse', '', array( avela_e_heading( __( 'Find your favourites', 'avela' ), 'h3' ), avela_e_button( __( 'Explore the full collection', 'avela' ), $shop, 'avela-e-link' ) ), 34 ), avela_e_col( 'Questions', '', array( avela_e_heading( __( 'A little help?', 'avela' ), 'h3' ), avela_e_button( __( 'Find an answer in the FAQ', 'avela' ), $faq, 'avela-e-link' ) ), 33 ), avela_e_col( 'Contact', '', array( avela_e_heading( __( 'Let’s talk', 'avela' ), 'h3' ), avela_e_button( __( 'Get in touch with our team', 'avela' ), $contact, 'avela-e-link' ) ), 33 ) ) ) ) ),
 );
 return array_merge( array( $home[0], $home[2], $home[1] ), array_slice( $home, 3 ) );
}
