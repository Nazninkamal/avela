<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<!doctype html><html <?php language_attributes(); ?>><head><meta charset="<?php bloginfo( 'charset' ); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?></head>
<body <?php body_class(); ?>><?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'avela' ); ?></a>
<?php if ( ! avela_elementor_region( 'header' ) ) : ?>
<?php $announcement = get_theme_mod( 'avela_announcement', __( 'Thoughtfully chosen. Made for the everyday.', 'avela' ) ); if ( $announcement ) : ?><div class="avela-announcement"><?php echo esc_html( $announcement ); ?></div><?php endif; ?>
<header class="avela-header"><div class="avela-shell avela-header-row">
<div class="avela-brand"><?php if ( has_custom_logo() ) { the_custom_logo(); } else { ?><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php echo esc_html( get_bloginfo( 'name' ) ); ?><span aria-hidden="true">.</span></a><?php } ?></div>
<?php avela_product_search(); ?>
<div class="avela-header-actions">
<?php if ( function_exists( 'wc_get_cart_url' ) ) : ?><a class="avela-account" aria-label="<?php esc_attr_e( 'My account', 'avela' ); ?>" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><svg width="21" height="23" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="1.5"/><path d="M4 22v-3a8 8 0 0 1 16 0v3" stroke="currentColor" stroke-width="1.5"/></svg><span><?php esc_html_e( 'Account', 'avela' ); ?></span></a>
<a class="avela-cart" href="<?php echo esc_url( wc_get_cart_url() ); ?>"><svg width="20" height="22" viewBox="0 0 20 22" fill="none" aria-hidden="true"><path d="M2 7h16l1 13H1L2 7Z M6 8V5a4 4 0 0 1 8 0v3" stroke="currentColor" stroke-width="1.4"/></svg><span class="screen-reader-text"><?php esc_html_e( 'View cart', 'avela' ); ?></span><?php echo avela_cart_badge(); ?></a><?php endif; ?>
<button class="avela-menu-toggle" type="button" aria-controls="avela-navigation" aria-expanded="false"><?php esc_html_e( 'Menu', 'avela' ); ?><span aria-hidden="true"> ☰</span></button>
</div></div>
<div class="avela-nav-bar"><div class="avela-shell"><nav id="avela-navigation" class="avela-navigation" aria-label="<?php esc_attr_e( 'Main navigation', 'avela' ); ?>"><?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'menu_class' => 'avela-menu', 'fallback_cb' => 'avela_primary_fallback' ) ); ?></nav>
<?php if ( has_nav_menu( 'categories' ) ) : ?><nav class="avela-category-nav" aria-label="<?php esc_attr_e( 'Product categories', 'avela' ); ?>"><?php wp_nav_menu( array( 'theme_location' => 'categories', 'container' => false, 'menu_class' => 'avela-menu', 'depth' => 1 ) ); ?></nav><?php endif; ?></div></div></header>

<?php endif; ?>
