<?php
/** Run only on a disposable WP site: wp eval-file tests/setup-integration.php */
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) { exit; }
wp_set_current_user( 1 );
function avela_test_assert( $condition, $label ) { if ( ! $condition ) { WP_CLI::error( $label ); } WP_CLI::success( $label ); }
$before_pages = get_posts( array( 'post_type' => 'page', 'post_status' => 'any', 'numberposts' => -1, 'orderby' => 'ID', 'order' => 'ASC' ) );
$before_products = get_posts( array( 'post_type' => 'product', 'post_status' => 'any', 'numberposts' => -1, 'orderby' => 'ID', 'order' => 'ASC' ) );
$before_menus = wp_get_nav_menus();
$before_settings = array( get_option( 'show_on_front' ), get_option( 'page_on_front' ), get_option( 'woocommerce_currency' ), get_option( 'woocommerce_store_address' ), get_theme_mod( 'nav_menu_locations' ) );
$report = avela_run_setup( true );
avela_test_assert( $before_pages == get_posts( array( 'post_type' => 'page', 'post_status' => 'any', 'numberposts' => -1, 'orderby' => 'ID', 'order' => 'ASC' ) ), 'Repeated setup preserves every page and content.' );
avela_test_assert( count( $before_products ) === count( get_posts( array( 'post_type' => 'product', 'post_status' => 'any', 'numberposts' => -1 ) ) ), 'Repeated demo setup creates no duplicate products.' );
avela_test_assert( count( $before_menus ) === count( wp_get_nav_menus() ), 'Repeated setup creates no duplicate menus.' );
avela_test_assert( $before_settings === array( get_option( 'show_on_front' ), get_option( 'page_on_front' ), get_option( 'woocommerce_currency' ), get_option( 'woocommerce_store_address' ), get_theme_mod( 'nav_menu_locations' ) ), 'Homepage, menu assignments and business settings preserved.' );
$home = get_page_by_path( 'home' ); $original = $home->post_content;
wp_update_post( array( 'ID' => $home->ID, 'post_content' => '<!-- wp:paragraph --><p>Merchant content must survive.</p><!-- /wp:paragraph -->' ) );
$custom = wp_insert_post( array( 'post_title' => 'Existing merchant homepage', 'post_type' => 'page', 'post_status' => 'publish', 'post_content' => 'Existing site content' ) );
update_option( 'page_on_front', $custom );
avela_run_setup( false );
avela_test_assert( (int) get_option( 'page_on_front' ) === $custom && str_contains( get_post_field( 'post_content', $home->ID ), 'Merchant content must survive' ), 'Existing static homepage and edited starter content preserved.' );
update_option( 'page_on_front', $home->ID ); wp_update_post( array( 'ID' => $home->ID, 'post_content' => $original ) ); wp_delete_post( $custom, true );
foreach ( array( 'shipping-and-returns', 'terms-and-conditions' ) as $slug ) { avela_test_assert( 'draft' === get_page_by_path( $slug )->post_status, $slug . ' remains a draft.' ); }
$product = wc_get_products( array( 'limit' => 1, 'sku' => 'AVELA-SAMPLE-CLEANSER' ) )[0];
$product->set_name( 'Merchant edited cleanser' ); $product->save();
$original_name = 'Everyday gentle cleanser';
$cleanup = avela_cleanup_demo();
avela_test_assert( 'publish' === get_post_status( $product->get_id() ), 'Cleanup preserves edited sample product.' );
avela_test_assert( count( get_posts( array( 'post_type' => 'product', 'post_status' => 'trash', 'meta_key' => '_avela_demo_product', 'posts_per_page' => -1 ) ) ) === 5, 'Cleanup trashes only five unedited samples.' );
// Restore fixtures for the browser checkout tests.
foreach ( get_posts( array( 'post_type' => 'product', 'post_status' => 'trash', 'meta_key' => '_avela_demo_product', 'posts_per_page' => -1 ) ) as $post ) { wp_untrash_post( $post->ID ); wp_update_post( array( 'ID' => $post->ID, 'post_status' => 'publish' ) ); }
$product = wc_get_product( $product->get_id() ); $product->set_name( $original_name ); $product->save(); update_post_meta( $product->get_id(), '_avela_demo_fingerprint', avela_demo_fingerprint( $product->get_id() ) );
wp_set_current_user( 0 );
$count = count( get_posts( array( 'post_type' => 'page', 'post_status' => 'any', 'numberposts' => -1 ) ) );
avela_run_setup( true );
avela_test_assert( $count === count( get_posts( array( 'post_type' => 'page', 'post_status' => 'any', 'numberposts' => -1 ) ) ), 'Unauthorized setup call changes no content.' );
