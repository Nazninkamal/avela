<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header(); ?>
<main id="content" class="avela-home">
<?php
if ( 'page' === get_option( 'show_on_front' ) && have_posts() ) {
 while ( have_posts() ) { the_post(); the_content(); wp_link_pages(); }
} else {
 echo do_blocks( avela_home_content() ); // Core blocks escape their own output.
}
?>
</main>
<?php get_footer(); ?>
