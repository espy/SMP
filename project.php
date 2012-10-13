<?php
/**
 * Template Name: Project
 */

get_header(); ?>

<!-- LOOP starts here -->
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

<?php

global $post;
echo $post->post_content;

?>

<!-- LOOP ends here -->
<?php endwhile; else: ?>
<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
<?php endif; ?>


<ul>
<?php

$shortLocale = substr(get_locale(), 0, 2);

if( get_field('gallery') ){
  echo "<li>";
  while( has_sub_field('gallery') ){
    $image = get_sub_field('image');
    echo '<img src="'.$image["sizes"]["thumbnail"].'"/>';
  }
  echo "</li>";
}

?>

<?php get_footer(); ?>