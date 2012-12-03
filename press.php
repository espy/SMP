<?php
/**
 * Template Name: Press
 */

$translations = array(
  type => array(en => 'type', de => 'kategorie'),
  attachment => array(en => 'download pdf', de => 'pdf herunterladen')
);

$locales = array("de", "en");

get_header();
echo '<div class="viewport press">';

$shortLocale = substr(get_locale(), 0, 2);

$posts = get_posts();
$index = 0;
echo '<ul class="newsItems">';
foreach ($posts as $post) {
  $title = qtrans_use($shortLocale, get_post($post->ID)->post_title,false);
  $content = qtrans_use($shortLocale, get_post($post->ID)->post_content,false);
  $timestamp = strtotime($post->post_date);
  $date = date('d/m/Y', $timestamp);
  $year = date('Y', $timestamp);


  if( get_field('item', $post->ID) ){
    while( has_sub_field('item', $post->ID) ){
      $preview_image = get_sub_field('preview_image');
      $teaser_image = get_sub_field('teaser_image');
      $type = get_sub_field('type');
      $type = explode("/", $type);
      $type = $type[array_search($shortLocale, $locales)];
      $attachment = get_sub_field('attachment');
      //echo "preview_image: ".$preview_image['sizes']['large']."<br>";
      //echo "preview_image: ".$teaser_image['sizes']['medium']."<br>";
      //echo "type: ".$type."<br>";
      //echo "attachment: ".$attachment['url']."<br>";
    }
  }
  echo '<li><a href="'.basename(get_permalink($post->ID)).'" data-image="'.$preview_image['sizes']['large'].'">';
  echo '<h3>'.$year.'</h3>';
  echo '<span>'.$title.'</span>';
  echo '</a><div class="newsItemView">';
  echo '  <h3>'.$date.'</h3>';
  echo '  <h1>'.$title.'</h1>';
  echo '  <img src="'.$teaser_image['sizes']['medium'].'">';
  echo '  <div class="info group">';
  echo '    <div class="type"><h3>'.$translations['type'][$shortLocale].'</h3><h1>'.$type.'</h1></div>';
  if($attachment != ""){
    echo '    <div class="attachment"><a href="'.$attachment.'" class="datasheet">'.$translations['attachment'][$shortLocale].'</a></div>';
  }
  echo '  </div>';
  echo '  <div class="description">'.wpautop( $content, true ).'</div>';
  echo '</div>';
}
$index++;
echo '</li>';
echo '</ul>';

?>
</div> <!-- end of viewport -->

<div id="backgroundImage" class="press">
  <img alt="">
</div>

<?php get_footer(); ?>