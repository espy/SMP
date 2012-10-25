<?php
/**
 * Template Name: Studio
 */

get_header(); ?>

<ul>
<?php
if ( have_posts() ) : while ( have_posts() ) : the_post();
global $post;
$currentSection = $post->post_title;
echo "currentSection ".$currentSection;
endwhile;
endif;

$shortLocale = substr(get_locale(), 0, 2);

//$sections = get_pages( array( 'child_of' => $studioID,  'sort_column' => 'menu_order' ));

$studioID = 17;
$partnersID = 194;
$staffID = 206;

echo '<ul>';
if( get_field('gallery', $studioID) ){
  echo "<li>";
  while( has_sub_field('gallery', $studioID) ){
    $image = get_sub_field('image');
    echo '<img src="'.$image["sizes"]["thumbnail"].'"/>';
  }
  echo "</li>";
}
echo '</ul>';



$partnersTitle = qtrans_use($shortLocale, get_post($partnersID)->post_title,false);
echo '<h1 class="'.$partnersTitle.'">'.$partnersTitle.'</h1>';

echo '<ul>';
if( get_field('partners', $partnersID) ){
  echo "<li>";
  while( has_sub_field('partners', $partnersID) ){
    $name = get_sub_field('name');
    $desc = get_sub_field('description_'.$shortLocale);
    $image = get_sub_field('image');
    echo 'Partner: '.$name;
    echo 'Description: '.$desc;
    echo '<img src="'.$image["sizes"]["medium"].'"/>';
  }
  echo "</li>";
}
echo '</ul>';



$staffTitle = qtrans_use($shortLocale, get_post($staffID)->post_title,false);
echo '<h1 class="'.$staffTitle.'">'.$staffTitle.'</h1>';

echo '<ul>';
if( get_field('staff_members', $staffID) ){
  echo "<li>";
  while( has_sub_field('staff_members', $staffID) ){
    $name = get_sub_field('name');
    $phone = get_sub_field('phone');
    $image = get_sub_field('image');
    echo 'Staff member: '.$name;
    echo 'Phone: '.$phone;
    echo '<img src="'.$image["sizes"]["thumbnail"].'"/>';
  }
  echo "</li>";
}
echo '</ul>';

?>
<script type="text/javascript">
  var scrollToSection = "<?php echo $currentSection; ?>";
</script>
<?php get_footer(); ?>