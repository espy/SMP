<?php
/**
 * Template Name: Studio
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


<h1>Staff members</h1>
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

if( get_field('partners') ){
  echo "<li>";
  while( has_sub_field('partners') ){
    $name = get_sub_field('name');
    $desc = get_sub_field('description_'.$shortLocale);
    $image = get_sub_field('image');
    echo 'Partner: '.$name;
    echo 'Description: '.$desc;
    echo '<img src="'.$image["sizes"]["medium"].'"/>';
  }
  echo "</li>";
}

if( get_field('staff_members') ){
  echo "<li>";
  while( has_sub_field('staff_members') ){
    $name = get_sub_field('name');
    $phone = get_sub_field('phone');
    $image = get_sub_field('image');
    echo 'Staff member: '.$name;
    echo 'Phone: '.$phone;
    echo '<img src="'.$image["sizes"]["thumbnail"].'"/>';
  }
  echo "</li>";
}
?>
</ul>

<?php get_footer(); ?>