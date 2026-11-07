<?php if ( ! defined( 'ABSPATH' ) ) { exit; } get_header(); ?>
<main id="content" class="avela-shell avela-page woocommerce"><?php if ( function_exists( 'woocommerce_content' ) ) { woocommerce_content(); } ?></main><?php get_footer(); ?>
