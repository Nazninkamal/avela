<?php if ( ! defined( 'ABSPATH' ) || post_password_required() ) { return; } ?>
<section id="comments" class="avela-comments"><?php if ( have_comments() ) : ?><h2><?php esc_html_e( 'Conversation', 'avela' ); ?></h2><ol class="comment-list"><?php wp_list_comments( array( 'style' => 'ol', 'short_ping' => true ) ); ?></ol><?php the_comments_navigation(); endif; comment_form(); ?></section>
