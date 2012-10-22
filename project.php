<?php
/**
 * Template Name: Project
 */

$fields = array(
  array(type => 'year', en => 'Year', de => 'Jahr', 'output' => true),
  array(type => 'contest', en => 'Contest', de => 'Wettbewerb', 'output' => true),
  array(type => 'area', en => 'Area', de => 'BGF', 'output' => true),
  array(type => 'client', en => 'Client', de => 'Bauherr'),
  array(type => 'project_category', en => 'Type', de => 'Kategorie', 'output' => true),
  array(type => 'datasheet', en => 'Data sheet', de => 'Datenblatt', 'output' => false),
  array(type => 'address', en => 'Address', de => 'Adresse', 'output' => false)
);

get_header();

$shortLocale = substr(get_locale(), 0, 2);

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
  <ul class="projectList"></ul>
  <ul class="projectList"></ul>
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
          echo '<h2>'.$field[$shortLocale].'</h2>';
          echo '<span>'.$$variableNameValue.'</span>';
        }
      }
      $gallery = get_field('gallery', $post->ID);
      $image = $gallery[0]['image']['sizes']['large'];

      // Datasheet download button
      if($datasheet){
        echo '<a class="datasheet" href="'.$datasheet['url'].'">'.$datasheetLabel.'</a>';
      }

      echo '<p class="description">'.$post->post_content.'</p>';

      if($address){
        // Address
        echo '<h2>'.$addressLabel.'</h2>';
        echo '<p>'.$address.'</p>';
        // Google map view
        $mapString = str_replace("<br>", "", $address);
        $mapString = str_replace("<br />", "", $mapString);
        $mapString = rawurlencode($mapString);
        $mapString = str_replace("%0D%0A", "%20", $mapString);
        $mapSource = 'http://maps.googleapis.com/maps/api/staticmap?center='.$mapString.'&zoom=16&size=360x360&sensor=false&markers='.$mapString;
        echo '<img class="map lazy" data-src="'.$mapSource.'" alt="">';
      }

    endwhile;
    endif;
    echo '</div></ul>';
  ?>
  <ul class="projectList"></ul>
</div> <!-- end of viewport -->
<div id="backgroundImage">
  <img src="<?php echo $image; ?>" alt="">
</div>

<?php get_footer(); ?>