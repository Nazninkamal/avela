<?php
/** Elementor Free integration. Import is explicit; no existing content is replaced. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
function avela_elementor_available() { return did_action( 'elementor/loaded' ) && class_exists( '\Elementor\Plugin' ); }
function avela_elementor_widgets( $manager ) {
 require_once get_theme_file_path( 'inc/elementor-widgets.php' );
 foreach ( array( 'products', 'categories', 'brand', 'search', 'actions', 'navigation', 'footer-menu', 'contact', 'copyright' ) as $kind ) { $manager->register( new Avela_Elementor_Widget( array(), null, $kind ) ); }
}
add_action( 'elementor/widgets/register', 'avela_elementor_widgets' );
add_action( 'elementor/elements/categories_registered', function ( $manager ) { $manager->add_category( 'avela-store', array( 'title' => __( 'Avela Shop', 'avela' ), 'icon' => 'fa fa-shopping-bag' ) ); } );
function avela_elementor_region( $region ) {
 if ( ! avela_elementor_available() || ! in_array( $region, array( 'header', 'footer' ), true ) ) { return false; }
 $id = absint( get_option( 'avela_elementor_' . $region ) );
 if ( ! $id || 'publish' !== get_post_status( $id ) || (int) get_queried_object_id() === $id ) { return false; }
 $document = \Elementor\Plugin::$instance->documents->get( $id );
 if ( ! $document || ! $document->is_built_with_elementor() ) { return false; }
 $html = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $id, true );
 if ( ! $html ) { return false; }
 echo '<' . ( 'header' === $region ? 'header' : 'footer' ) . ' class="avela-elementor-' . esc_attr( $region ) . '">' . $html . '</' . ( 'header' === $region ? 'header' : 'footer' ) . '>'; // Elementor sanitizes widget content; widgets escape their own output.
 return true;
}
function avela_elementor_body_class( $classes ) {
 if ( avela_elementor_available() && ( get_option( 'avela_elementor_header' ) || get_post_meta( get_queried_object_id(), '_avela_elementor_key', true ) ) ) { $classes[] = 'avela-elementor-site'; }
 return $classes;
}
add_filter( 'body_class', 'avela_elementor_body_class' );
function avela_elementor_assets() {
 if ( avela_elementor_available() && ( get_option( 'avela_elementor_header' ) || get_option( 'avela_elementor_footer' ) ) ) { \Elementor\Plugin::$instance->frontend->enqueue_styles(); \Elementor\Plugin::$instance->frontend->enqueue_scripts(); }
 wp_enqueue_style( 'avela-elementor', get_theme_file_uri( 'assets/elementor.css' ), array( 'avela' ), AVELA_VERSION );
}
add_action( 'wp_enqueue_scripts', 'avela_elementor_assets', 30 );
add_action( 'elementor/editor/after_enqueue_styles', function () { wp_enqueue_style( 'avela-elementor-editor', get_theme_file_uri( 'assets/elementor.css' ), array(), AVELA_VERSION ); } );
function avela_elementor_find( $key ) {
 $posts = get_posts( array( 'post_type' => array( 'page', 'elementor_library' ), 'post_status' => array( 'publish', 'draft', 'private', 'pending', 'future', 'trash' ), 'meta_key' => '_avela_elementor_key', 'meta_value' => $key, 'numberposts' => 1 ) );
 return $posts ? $posts[0] : null;
}
function avela_create_elementor_site( $use_home = false, $use_menus = false, $use_regions = false ) {
 if ( ! current_user_can( 'manage_options' ) ) { return array( __( 'Administrator access is required.', 'avela' ) ); }
 if ( ! avela_elementor_available() ) { return array( __( 'Install and activate Elementor Free first. No existing pages were changed.', 'avela' ) ); }
 if ( ! add_option( 'avela_elementor_setup_lock', time(), '', false ) ) { return array( __( 'Elementor setup is already in progress. Try again in five minutes if it was interrupted.', 'avela' ) ); }
 $report = array(); $ids = array(); $new = array();
 try {
  foreach ( array( 'home' => __( 'Home', 'avela' ), 'about' => __( 'Our story', 'avela' ), 'contact' => __( 'Contact', 'avela' ), 'faq' => __( 'FAQ', 'avela' ), 'header' => __( 'Avela Header', 'avela' ), 'footer' => __( 'Avela Footer', 'avela' ) ) as $key => $title ) {
   $existing = avela_elementor_find( $key );
   if ( $existing ) {
    if ( in_array( $existing->post_status, array( 'private', 'trash' ), true ) ) { $report[] = sprintf( __( '%s: existing template is private or in Trash; restore it manually. No duplicate created.', 'avela' ), $title ); continue; }
    $ids[ $key ] = $existing->ID; $report[] = sprintf( __( '%s: reused; all edits preserved.', 'avela' ), $title ); continue;
   }
   $is_region = in_array( $key, array( 'header', 'footer' ), true );
   $fallback = 'home' === $key ? avela_home_content() : ( $is_region ? '' : ( avela_page_definitions()[ $key ]['content'] ?? '' ) );
   $id = wp_insert_post( array( 'post_title' => $title, 'post_name' => 'avela-' . $key, 'post_type' => $is_region ? 'elementor_library' : 'page', 'post_status' => 'publish', 'post_content' => $fallback, 'comment_status' => 'closed' ), true );
   if ( is_wp_error( $id ) ) { $report[] = $id->get_error_message(); continue; }
   $ids[ $key ] = $id; $new[ $key ] = $id; update_post_meta( $id, '_avela_elementor_key', $key );
   $report[] = sprintf( __( '%s: created as a separate editable Elementor layout.', 'avela' ), $title );
  }
  foreach ( $new as $key => $id ) {
   $data = avela_elementor_layout( $key, $ids );
   update_post_meta( $id, '_elementor_edit_mode', 'builder' ); update_post_meta( $id, '_elementor_template_type', in_array( $key, array( 'header', 'footer' ), true ) ? 'section' : 'wp-page' );
   update_post_meta( $id, '_elementor_version', ELEMENTOR_VERSION );
   if ( in_array( $key, array( 'header', 'footer' ), true ) && taxonomy_exists( 'elementor_library_type' ) ) { wp_set_object_terms( $id, 'section', 'elementor_library_type' ); }
   update_post_meta( $id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
   update_post_meta( $id, '_wp_page_template', in_array( $key, array( 'header', 'footer' ), true ) ? 'elementor_canvas' : 'page-elementor.php' );
   update_post_meta( $id, '_elementor_page_settings', array( 'hide_title' => 'yes' ) );
   \Elementor\Core\Files\CSS\Post::create( $id )->update();
  }
  if ( $use_home && ! empty( $ids['home'] ) && 'publish' === get_post_status( $ids['home'] ) ) { update_option( 'page_on_front', $ids['home'] ); update_option( 'show_on_front', 'page' ); $report[] = __( 'Elementor Home assigned as requested. The previous homepage remains in Pages.', 'avela' ); }
  if ( $use_menus ) { $report = array_merge( $report, avela_elementor_menus( $ids ) ); }
  if ( $use_regions ) {
   foreach ( array( 'header', 'footer' ) as $region ) {
    if ( empty( $ids[ $region ] ) || get_option( 'avela_elementor_' . $region ) ) { continue; }
    update_option( 'avela_elementor_' . $region, $ids[ $region ] );
   }
   $report[] = __( 'Elementor header and footer enabled. Existing region assignments were preserved; the original theme regions remain available when these are disabled.', 'avela' );
  }
  \Elementor\Plugin::$instance->files_manager->clear_cache();
 } finally { delete_option( 'avela_elementor_setup_lock' ); }
 return $report;
}
function avela_elementor_menus( $ids ) {
 $locations = get_theme_mod( 'nav_menu_locations', array() ); $report = array();
 foreach ( array( 'primary' => array( 'home', 'shop', 'about', 'contact', 'faq' ), 'footer' => array( 'shop', 'about', 'contact', 'faq', 'order-tracking', 'my-account' ) ) as $location => $keys ) {
  $name = 'Avela Elementor ' . ucfirst( $location ); $menu = wp_get_nav_menu_object( $name );
  if ( ! $menu ) {
   $created = wp_create_nav_menu( $name ); if ( is_wp_error( $created ) ) { $report[] = $created->get_error_message(); continue; }
   foreach ( $keys as $key ) {
    $page_id = $ids[ $key ] ?? ( get_page_by_path( $key ) ? get_page_by_path( $key )->ID : 0 );
    if ( ! $page_id || 'publish' !== get_post_status( $page_id ) ) { continue; }
    $labels = array( 'home' => __( 'Home', 'avela' ), 'about' => __( 'Our story', 'avela' ), 'contact' => __( 'Contact', 'avela' ), 'faq' => __( 'FAQ', 'avela' ) );
    wp_update_nav_menu_item( $created, 0, array( 'menu-item-object-id' => $page_id, 'menu-item-object' => 'page', 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish', 'menu-item-title' => $labels[ $key ] ?? get_the_title( $page_id ) ) );
   }
   $menu = wp_get_nav_menu_object( $created );
  }
  $locations[ $location ] = $menu->term_id; $report[] = sprintf( __( '%s assigned as requested. Other menus were not changed.', 'avela' ), $name );
 }
 set_theme_mod( 'nav_menu_locations', $locations ); return $report;
}
function avela_elementor_setup_action() {
 if ( ! current_user_can( 'manage_options' ) ) { wp_die( esc_html__( 'Administrator access is required.', 'avela' ) ); }
 check_admin_referer( 'avela_elementor_setup' );
 $lock = (int) get_option( 'avela_elementor_setup_lock' ); if ( $lock && $lock < time() - 300 ) { delete_option( 'avela_elementor_setup_lock' ); }
 if ( ! empty( $_POST['avela_disable_regions'] ) ) {
  delete_option( 'avela_elementor_header' ); delete_option( 'avela_elementor_footer' ); $report = array( __( 'Original theme header and footer restored. Your Elementor templates and edits were retained.', 'avela' ) );
 } else { $report = avela_create_elementor_site( ! empty( $_POST['avela_use_home'] ), ! empty( $_POST['avela_use_menus'] ), ! empty( $_POST['avela_use_regions'] ) ); }
 set_transient( 'avela_setup_report_' . get_current_user_id(), $report, HOUR_IN_SECONDS );
 wp_safe_redirect( admin_url( 'themes.php?page=avela-setup' ) ); exit;
}
add_action( 'admin_post_avela_elementor_setup', 'avela_elementor_setup_action' );
require get_theme_file_path( 'inc/elementor-layouts.php' );
function avela_elementor_setup_panel() {
 ?>
 <div style="margin:28px 0;padding:25px;background:#fff;border:1px solid #ccd0c5;border-inline-start:4px solid #23493d">
 <h2><?php esc_html_e( 'Build the ready-made Elementor website', 'avela' ); ?></h2>
 <p><?php esc_html_e( 'Recommended for visual editing: a complete boutique storefront, About, Contact and FAQ pages, plus editable header and footer layouts. Works with Elementor Free. No Pro license, importer plugin or manual JSON import is required.', 'avela' ); ?></p>
 <?php if ( ! avela_elementor_available() ) : ?><p><a class="button" href="<?php echo esc_url( admin_url( 'plugin-install.php?s=elementor&tab=search&type=term' ) ); ?>"><?php esc_html_e( 'Install Elementor Free through WordPress', 'avela' ); ?></a></p><?php endif; ?>
 <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
 <?php wp_nonce_field( 'avela_elementor_setup' ); ?><input type="hidden" name="action" value="avela_elementor_setup">
 <p><label><input type="checkbox" name="avela_use_home" value="1"> <?php esc_html_e( 'Use Elementor Home as my homepage. Keep the previous homepage in Pages.', 'avela' ); ?></label></p>
 <p><label><input type="checkbox" name="avela_use_menus" value="1"> <?php esc_html_e( 'Assign new menus linking to the Elementor pages. Keep my old menus available.', 'avela' ); ?></label></p>
 <p><label><input type="checkbox" name="avela_use_regions" value="1"> <?php esc_html_e( 'Use the editable Elementor header and footer across the website.', 'avela' ); ?></label></p>
 <p><?php esc_html_e( 'Existing pages and edits are never overwritten. Run this again to reuse your templates and apply selected switches. Leave the switches off to create and preview the new layouts first. Product data stays in WooCommerce; shared logo/contact information stays in Customize.', 'avela' ); ?></p>
 <button class="button button-primary" <?php disabled( ! avela_elementor_available() ); ?>><?php esc_html_e( 'Create my Elementor website', 'avela' ); ?></button>
 </form>
 <table class="widefat striped" style="margin-top:20px"><thead><tr><th><?php esc_html_e( 'Ready-made layout', 'avela' ); ?></th><th><?php esc_html_e( 'Edit and preview', 'avela' ); ?></th></tr></thead><tbody>
 <?php foreach ( array( 'home' => __( 'Homepage', 'avela' ), 'about' => __( 'About', 'avela' ), 'contact' => __( 'Contact', 'avela' ), 'faq' => __( 'FAQ', 'avela' ), 'header' => __( 'Header', 'avela' ), 'footer' => __( 'Footer', 'avela' ) ) as $key => $label ) : $page = avela_elementor_find( $key ); ?>
 <tr><td><?php echo esc_html( $label ); ?></td><td><?php if ( $page && avela_elementor_available() && ! in_array( $page->post_status, array( 'trash', 'private' ), true ) ) : ?><a href="<?php echo esc_url( admin_url( 'post.php?post=' . $page->ID . '&action=elementor' ) ); ?>"><?php esc_html_e( 'Edit with Elementor', 'avela' ); ?></a> · <a href="<?php echo esc_url( get_permalink( $page->ID ) ); ?>"><?php esc_html_e( 'Preview', 'avela' ); ?></a><?php else : ?><?php esc_html_e( 'Activate Elementor and create the website above, or restore the existing layout from Trash.', 'avela' ); ?><?php endif; ?></td></tr>
 <?php endforeach; ?></tbody></table>
 <p><?php esc_html_e( 'In Elementor, select any heading, text, image or button to edit it. Use Navigator/Structure to move, duplicate or delete named sections. Select Avela Products to choose featured, new or sale items, columns and image proportions. Header and footer layouts are available from this screen even with Elementor Free.', 'avela' ); ?></p>
 <?php if ( get_option( 'avela_elementor_header' ) || get_option( 'avela_elementor_footer' ) ) : ?>
 <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><?php wp_nonce_field( 'avela_elementor_setup' ); ?><input type="hidden" name="action" value="avela_elementor_setup"><button class="button" name="avela_disable_regions" value="1"><?php esc_html_e( 'Restore original theme header and footer', 'avela' ); ?></button></form>
 <?php endif; ?></div>
 <?php
}

add_filter( 'elementor/fonts/additional_fonts', function ( $fonts ) { $fonts['Avela Editorial'] = 'system'; return $fonts; } );
