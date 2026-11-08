<?php
/** Disposable WordPress test site only. Requires Elementor Free and WooCommerce. */
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) { exit; }
wp_set_current_user( 1 );
function avela_elementor_assert( $value, $message ) { if ( ! $value ) { WP_CLI::error( $message ); } WP_CLI::success( $message ); }
$before_home = get_option( 'page_on_front' ); $before_menus = get_theme_mod( 'nav_menu_locations' );
$ids = array(); $data = array();
foreach ( array( 'home', 'about', 'contact', 'faq', 'header', 'footer' ) as $key ) { $p = avela_elementor_find( $key ); avela_elementor_assert( (bool) $p, $key . ' template exists.' ); $ids[$key] = $p->ID; $data[$key] = get_post_meta( $p->ID, '_elementor_data', true ); }
$page_count = wp_count_posts( 'page' ); $library_count = wp_count_posts( 'elementor_library' );
$report = avela_create_elementor_site( false, false, false );
foreach ( $ids as $key => $id ) { avela_elementor_assert( $data[$key] === get_post_meta( $id, '_elementor_data', true ), $key . ' content preserved on repeated setup.' ); }
avela_elementor_assert( $page_count == wp_count_posts( 'page' ) && $library_count == wp_count_posts( 'elementor_library' ), 'No duplicate pages or library templates.' );
avela_elementor_assert( $before_home === get_option( 'page_on_front' ) && $before_menus === get_theme_mod( 'nav_menu_locations' ), 'Unselected homepage/menu switches change no assignments.' );
$edited = json_decode( $data['home'], true ); $edited[0]['settings']['avela_test_marker'] = 'Merchant edit'; update_post_meta( $ids['home'], '_elementor_data', wp_slash( wp_json_encode( $edited ) ) );
avela_create_elementor_site(); avela_elementor_assert( false !== strpos( get_post_meta( $ids['home'], '_elementor_data', true ), 'Merchant edit' ), 'Merchant Elementor changes are never overwritten.' );
update_post_meta( $ids['home'], '_elementor_data', wp_slash( $data['home'] ) );
$original_page = get_page_by_path( 'home' ); avela_elementor_assert( (bool) $original_page && $original_page->ID !== $ids['home'], 'Original block homepage remains separate and untouched.' );
foreach ( array( 'shipping-and-returns', 'terms-and-conditions' ) as $slug ) { avela_elementor_assert( 'draft' === get_page_by_path( $slug )->post_status, $slug . ' remains a draft.' ); }
wp_set_current_user( 0 ); $count = wp_count_posts( 'page' ); avela_create_elementor_site( true, true, true ); avela_elementor_assert( $count == wp_count_posts( 'page' ), 'Unauthorized import changes no pages.' );
