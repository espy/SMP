<?php
/**
 * Template Name: Imprint
 */

get_header();
echo '<div class="viewport imprint group">';

if ( have_posts() ) : while ( have_posts() ) : the_post();
global $post;

echo '<div class="contact">';
echo '<strong>'.$post->post_title.'</strong>';
echo '<p>'.get_field('address', $post->ID).'</p>';
echo '<p>'.get_field('phone', $post->ID).'</p>';
echo '<a class="datasheet" href="mailto:'.get_field('email', $post->ID).'">E-mail</a>';
echo '</div>';

echo '<div class="info">';
echo wpautop( $post->post_content, true );
echo '</div>';

endwhile;
endif;
?>

</div> <!-- end of viewport -->

<?php get_footer(); ?>