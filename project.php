<?php
/**
 * Template Name: Project
 */

$fields = array(
  array(type => 'year', en => 'Year', de => 'Fertigstellung', 'output' => true),
  array(type => 'contest', en => 'Contest', de => 'Wettbewerb', 'output' => true),
  array(type => 'area', en => 'Area', de => 'BGF', 'output' => true, 'suffix_de' => 'qm', 'suffix_en' => 'sqm'),
  array(type => 'client', en => 'Client', de => 'Bauherr'),
  array(type => 'project_category', en => 'Type', de => 'Kategorie', 'output' => true),
  array(type => 'datasheet', en => 'Data sheet', de => 'Datenblatt', 'output' => false),
  array(type => 'address', en => 'Address', de => 'Adresse', 'output' => false)
);

get_header();

$shortLocale = substr(get_locale(), 0, 2);

function getImageNumber(){
  $imageNumber = explode('/', $_SERVER["REQUEST_URI"]);
  if($imageNumber[count($imageNumber)] == ""){
    array_pop($imageNumber);
  }
  $imageNumber = $imageNumber[count($imageNumber)-1];
  if(strlen($imageNumber) >= 2){
    $imageNumber = 0;
  }
  return $imageNumber;
}

/*
if( get_field('gallery') ){
  echo "<li>";
  while( has_sub_field('gallery') ){
    $image = get_sub_field('image');
    echo '<img src="'.$image["sizes"]["thumbnail"].'"/>';
  }
  echo "</li>";
}
 */

?>
<div class="viewport project">
  <?php
    echo '<ul class="projectList projectDescription"><div class="projectSlider">';
    if ( have_posts() ) : while ( have_posts() ) : the_post();
    global $post;
      echo '<div class="header"><h1>'.$post->post_title.'</h1></div>';
      // Show all available special fields
      foreach ($fields as $field) {
        // Make var for localized label, i.e. $yearLabel = "Jahr"
        $variableNameLabel = $field['type'].'Label';
        $$variableNameLabel = $field[$shortLocale];
        // Make var for localized content, i.e. $year = 2012
        $variableNameValue = $field['type'];
        $$variableNameValue = get_field($field['type'], $post->ID);
        if($field['output'] === true){
          $suffix = ' '. $field['suffix_'.$shortLocale];
          echo '<div class="field"><h2>'.$field[$shortLocale].'</h2>';
          echo '<span>'.$$variableNameValue.$suffix.'</span></div>';
        }
      }
      $gallery = get_field('gallery', $post->ID);
      $image = $gallery[getImageNumber()]['image']['sizes']['large'];

      // Datasheet download button
      if($datasheet){
        echo '<a class="datasheet" href="'.$datasheet['url'].'">'.$datasheetLabel.'</a>';
      }

      echo '<div class="description">'.$post->post_content.'</div>';

      if($address){
        // Address
        echo '<h2>'.$addressLabel.'</h2>';
        echo '<p class="address">'.$address.'</p>';
        // Google map view
        $mapString = str_replace("<br>", "", $address);
        $mapString = str_replace("<br />", "", $mapString);
        $mapString = rawurlencode($mapString);
        $mapString = str_replace("%0D%0A", "%20", $mapString);
        $mapSource = 'http://maps.googleapis.com/maps/api/staticmap?center='.$mapString.'&zoom=16&size=450x450&sensor=false&markers='.$mapString;
        echo '<a href="https://maps.google.com/maps?q='.$mapString.'"><img class="map lazy" data-src="'.$mapSource.'" alt=""></a>';
      }

    endwhile;
    endif;
    echo '</div></ul>';

    // image navi
    if(count($gallery) > 1){
      $current = getImageNumber();
      $prev = $current - 1;
      if($prev <= 0){
        $prev = count($gallery);
      }
      $next = $current + 1;
      if($next > count($gallery)){
        $next = 1;
      }
      echo '<a href="'.$prev.'" class="previous"><span>PREVIOUS</span></a>';
      echo '<a href="'.$next.'" class="next"><span>NEXT</span></a>';
    }

  ?>
</div> <!-- end of viewport -->
<div id="backgroundImage" data-gallery='<?php echo json_encode($gallery) ?>'>
  <?php


  ?>
  <img src="<?php echo $image; ?>" alt="">
</div>

<?php get_footer(); ?>