<?php
/**
 * Template Name: Avela Elementor Full Width
 * Template Post Type: page
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header(); ?>
<main id="content" class="avela-elementor-page">
<?php while ( have_posts() ) { the_post(); if ( ! avela_elementor_available() ) { echo '<article class="avela-shell avela-page"><h1 class="avela-page-title">' . esc_html( get_the_title() ) . '</h1>'; } the_content(); wp_link_pages(); if ( ! avela_elementor_available() ) { echo '</article>'; } } ?>
</main>
<?php get_footer(); ?>
