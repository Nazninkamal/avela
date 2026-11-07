<?php
/** Editable starter layouts. No content is written on activation. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
function avela_block( $name, $attrs = array(), $html = '' ) {
 return '<!-- wp:' . $name . ( $attrs ? ' ' . wp_json_encode( $attrs ) : '' ) . ( '' === $html ? ' /-->' : ' -->' . $html . '<!-- /wp:' . $name . ' -->' );
}
function avela_p( $text, $class = '' ) { return avela_block( 'paragraph', $class ? array( 'className' => $class ) : array(), '<p' . ( $class ? ' class="' . esc_attr( $class ) . '"' : '' ) . '>' . esc_html( $text ) . '</p>' ); }
function avela_heading( $text, $level = 2 ) { return avela_block( 'heading', array( 'level' => $level ), '<h' . $level . ' class="wp-block-heading">' . esc_html( $text ) . '</h' . $level . '>' ); }
function avela_group( $content, $class ) { return avela_block( 'group', array( 'className' => $class, 'layout' => array( 'type' => 'default' ) ), '<div class="wp-block-group ' . esc_attr( $class ) . '">' . $content . '</div>' ); }
function avela_button_block( $label, $url ) {
 return avela_block( 'buttons', array(), '<div class="wp-block-buttons">' . avela_block( 'button', array(), '<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></div>' ) . '</div>' );
}
function avela_demo_image_block( $name, $alt ) {
 $ids = get_option( 'avela_demo_images', array() );
 $id = absint( $ids[ $name ] ?? 0 );
 $url = $id ? wp_get_attachment_url( $id ) : get_theme_file_uri( 'assets/demo/' . $name . '.jpg' );
 return avela_block( 'image', array_filter( array( 'id' => $id ?: null, 'sizeSlug' => 'large', 'linkDestination' => 'none' ) ), '<figure class="wp-block-image size-large"><img src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '"' . ( $id ? ' class="wp-image-' . $id . '"' : '' ) . '/></figure>' );
}
function avela_home_content() {
 $copy = avela_group( avela_p( __( 'THE EVERYDAY EDIT / AVELA', 'avela' ), 'avela-eyebrow' ) . avela_heading( __( 'Small rituals. Beautiful everyday.', 'avela' ), 1 ) . avela_p( __( 'Thoughtfully chosen essentials for your shelf, your space, and a little time for yourself.', 'avela' ) ) . avela_button_block( __( 'Explore the collection', 'avela' ), avela_shop_url() ) . avela_p( __( 'Considered essentials. A fresh perspective.', 'avela' ), 'avela-hero-note' ), 'avela-hero-copy' );
 $hero = avela_group( $copy . avela_demo_image_block( 'hero', __( 'Avela bottles, linen and a ceramic dish in warm afternoon light', 'avela' ) ), 'avela-hero-block' );
 $categories = avela_group( avela_p( __( 'A PLACE FOR EVERYTHING', 'avela' ), 'avela-eyebrow' ) . avela_heading( __( 'Find your everyday favourites.', 'avela' ) ) . avela_block( 'avela/categories' ), 'avela-section' );
 $featured = avela_group( avela_p( __( 'THE CONSIDERED COLLECTION', 'avela' ), 'avela-eyebrow' ) . avela_heading( __( 'Meet your new essentials.', 'avela' ) ) . avela_block( 'avela/catalog', array( 'mode' => 'featured', 'count' => 4 ) ) . avela_button_block( __( 'View the whole collection', 'avela' ), avela_shop_url() ), 'avela-section avela-featured' );
 $story = avela_group( avela_demo_image_block( 'linen', __( 'Natural linen and a soap bar on a warm stone surface', 'avela' ) ) . avela_group( avela_p( __( 'A MORE CONSIDERED EVERYDAY', 'avela' ), 'avela-eyebrow' ) . avela_heading( __( 'Less rush. More ritual.', 'avela' ) ) . avela_p( __( 'A familiar texture. A quiet corner. Something lovely to reach for each day. We bring together useful, beautiful things that make the ordinary feel a little more special.', 'avela' ) ) . avela_button_block( __( 'Our story', 'avela' ), avela_page_url( 'about' ) ), 'avela-story-copy' ), 'avela-story-block' );
 $new = avela_group( avela_p( __( 'JUST ADDED', 'avela' ), 'avela-eyebrow' ) . avela_heading( __( 'Fresh finds for your routine.', 'avela' ) ) . avela_block( 'avela/catalog', array( 'mode' => 'new', 'count' => 4 ) ), 'avela-section' );
 $sale = avela_group( avela_p( __( 'THE SEASONAL EDIT', 'avela' ), 'avela-eyebrow' ) . avela_heading( __( 'A little something to discover.', 'avela' ) ) . avela_p( __( 'Explore selected pieces at a special price while they are part of our seasonal collection.', 'avela' ) ) . avela_block( 'avela/catalog', array( 'mode' => 'sale', 'count' => 4 ) ), 'avela-section avela-sale' );
 return $hero . $categories . $featured . $story . $new . $sale;
}
function avela_page_definitions() {
 return array(
  'home' => array( 'title' => __( 'Home', 'avela' ), 'content' => '', 'status' => 'publish' ),
  'shop' => array( 'title' => __( 'Shop', 'avela' ), 'content' => '', 'status' => 'publish', 'wc' => 'shop' ),
  'cart' => array( 'title' => __( 'Cart', 'avela' ), 'content' => avela_block( 'avela/store-page', array( 'kind' => 'cart' ) ), 'status' => 'publish', 'wc' => 'cart' ),
  'checkout' => array( 'title' => __( 'Checkout', 'avela' ), 'content' => avela_block( 'avela/store-page', array( 'kind' => 'checkout' ) ), 'status' => 'publish', 'wc' => 'checkout' ),
  'my-account' => array( 'title' => __( 'My Account', 'avela' ), 'content' => avela_block( 'avela/store-page', array( 'kind' => 'account' ) ), 'status' => 'publish', 'wc' => 'myaccount' ),
  'about' => array( 'title' => __( 'A more considered everyday.', 'avela' ), 'content' => avela_demo_image_block( 'hero', __( 'The Avela everyday collection', 'avela' ) ) . avela_heading( __( 'Useful things. Beautiful details.', 'avela' ) ) . avela_p( __( 'We believe the things you reach for every day deserve a little thought. Our collection brings together simple pleasures for your routine and your space.', 'avela' ) ) . avela_heading( __( 'Make this story your own.', 'avela' ) ) . avela_p( __( 'This is starter content. Tell your customers who you are, how you choose your products, and what makes your store different. Replace this paragraph before launch.', 'avela' ) ), 'status' => 'publish' ),
  'contact' => array( 'title' => __( 'Let’s talk.', 'avela' ), 'content' => avela_p( __( 'Questions about a product or an order? We would love to hear from you. For an existing order, please include your order number.', 'avela' ) ) . avela_block( 'avela/contact' ) . avela_heading( __( 'Before you get in touch', 'avela' ) ) . avela_p( __( 'You may find your answer on our FAQ or Order Tracking page.', 'avela' ) ), 'status' => 'publish' ),
  'faq' => array( 'title' => __( 'A few helpful answers.', 'avela' ), 'content' => avela_p( __( 'Starter FAQ: edit these answers to match your actual business before launch.', 'avela' ) ) . avela_block( 'details', array(), '<details class="wp-block-details"><summary>' . esc_html__( 'How can I track my order?', 'avela' ) . '</summary>' . avela_p( __( 'Use the Order Tracking page with your order number and billing email address. Tracking shows your store order status; carrier tracking depends on your shipping integration.', 'avela' ) ) . '</details>' ) . avela_block( 'details', array(), '<details class="wp-block-details"><summary>' . esc_html__( 'Where do you deliver?', 'avela' ) . '</summary>' . avela_p( __( 'Merchant: replace this answer with the destinations and delivery estimates supported by your configured shipping zones.', 'avela' ) ) . '</details>' ) . avela_block( 'details', array(), '<details class="wp-block-details"><summary>' . esc_html__( 'Can I return an item?', 'avela' ) . '</summary>' . avela_p( __( 'Merchant: add your approved return conditions and contact instructions, consistent with applicable consumer law.', 'avela' ) ) . '</details>' ), 'status' => 'publish' ),
  'order-tracking' => array( 'title' => __( 'Track your order.', 'avela' ), 'content' => avela_p( __( 'Enter the order number from your confirmation email and the email address used at checkout.', 'avela' ) ) . avela_block( 'avela/store-page', array( 'kind' => 'tracking' ) ), 'status' => 'publish' ),
  'shipping-and-returns' => array( 'title' => __( 'Shipping and Returns', 'avela' ), 'content' => avela_policy_content( 'shipping' ), 'status' => 'draft' ),
  'privacy-policy' => array( 'title' => __( 'Privacy Policy', 'avela' ), 'content' => avela_policy_content( 'privacy' ), 'status' => 'draft' ),
  'terms-and-conditions' => array( 'title' => __( 'Terms and Conditions', 'avela' ), 'content' => avela_policy_content( 'terms' ), 'status' => 'draft' ),
 );
}
function avela_policy_content( $kind ) {
 $topics = array(
  'shipping' => array( __( 'Delivery destinations and costs', 'avela' ), __( 'Dispatch and delivery estimates', 'avela' ), __( 'Returns, refunds and statutory rights', 'avela' ), __( 'Return contact and address', 'avela' ) ),
  'privacy' => array( __( 'Business identity and privacy contact', 'avela' ), __( 'Data collected and purposes', 'avela' ), __( 'Payments, shipping, cookies and service providers', 'avela' ), __( 'Retention, international transfers and customer rights', 'avela' ) ),
  'terms' => array( __( 'Business identity and contact', 'avela' ), __( 'Products, prices and placing an order', 'avela' ), __( 'Payment, delivery and cancellation', 'avela' ), __( 'Consumer rights and governing law', 'avela' ) ),
 );
 $html = avela_heading( __( 'DRAFT — merchant review required', 'avela' ) ) . avela_p( __( 'This checklist is not a finished policy or legal advice. Replace every section with wording suitable for your business and jurisdiction. Review with a qualified adviser before publishing. No policy promises are supplied by the theme.', 'avela' ) );
 foreach ( $topics[ $kind ] as $topic ) { $html .= avela_heading( $topic, 3 ) . avela_p( __( '[Merchant: supply your reviewed wording here.]', 'avela' ) ); }
 return $html;
}
