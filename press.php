<?php
/**
 * Template Name: Press
 */

//include 'dump_r.php';

$locales = array("de", "en");

get_header();
echo '<div class="viewport press">';

$shortLocale = substr(get_locale(), 0, 2);

$posts = get_posts();

foreach ($posts as $post) {
  $title = qtrans_use($shortLocale, get_post($post->ID)->post_title,false);
  $content = qtrans_use($shortLocale, get_post($post->ID)->post_content,false);
  $excerpt = explode("<!--more-->", $content);

  $timestamp = strtotime($post->post_date);
  $date = date('d/m/Y', $timestamp);

  echo "date ".$date."<br>";
  echo "post_title ".$title."<br>";
  echo "post_excerpt ".$excerpt[0]."<br>";
  echo "post_content ".$content."<br>";

  if( get_field('item', $post->ID) ){
    while( has_sub_field('item', $post->ID) ){
      $preview_image = get_sub_field('preview_image');
      $teaser_image = get_sub_field('teaser_image');
      $type = get_sub_field('type');
      $type = explode("/", $type);
      $type = $type[array_search($shortLocale, $locales)];
      $attachment = get_sub_field('attachment');
      echo "preview_image: ".$preview_image['sizes']['large']."<br>";
      echo "preview_image: ".$teaser_image['sizes']['medium']."<br>";
      echo "type: ".$type."<br>";
      echo "attachment: ".$attachment['url']."<br>";
    }
  }
}

?>
</div> <!-- end of viewport -->

<script type="text/javascript">
  var scrollToSection = "<?php echo $currentSection; ?>";
</script>

<?php get_footer(); ?>