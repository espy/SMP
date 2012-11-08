<?php
/**
 * Template Name: Project
 */

//include 'dump_r.php';

$project_categories = array('public', 'residential', 'hotel', 'office', 'exhibition', 'masterplanning');

$fields = array(
  array(type => 'year', en => 'Year', de => 'Fertigstellung', 'output' => true),
  array(type => 'contest', en => 'Contest', de => 'Wettbewerb', 'output' => true),
  array(type => 'area', en => 'Area', de => 'BGF', 'output' => true, 'suffix_de' => 'qm', 'suffix_en' => 'sqm'),
  array(type => 'client', en => 'Client', de => 'Bauherr'),
  array(type => 'project_category', en => 'Type', de => 'Kategorie', 'output' => true),
  array(type => 'datasheet', en => 'Data sheet', de => 'Datenblatt', 'output' => false),
  array(type => 'address', en => 'Address', de => 'Adresse', 'output' => false)
);

$translations = array(
  similiarHeadline => array(en => 'Similar Projects', de => 'Ähnliche Projekte')
);

get_header();

$shortLocale = substr(get_locale(), 0, 2);

function getImageNumber($isForURL = true){
  $imageNumber = explode('/', $_SERVER["REQUEST_URI"]);
  if($imageNumber[count($imageNumber)] == ""){
    array_pop($imageNumber);
  }
  $imageNumber = $imageNumber[count($imageNumber)-1];
  if(strlen($imageNumber) >= 2){
    $imageNumber = 1;
  }
  if(!$isForURL){
    $imageNumber--;
  }
  return $imageNumber;
}

// Sort projects by date
function sortProjects($a, $b) {
  return get_field('year', $b->ID) - get_field('year', $a->ID);
}

?>
<div class="viewport project">
  <?php
    echo '<ul class="projectList projectDescription"><div class="projectSlider group">';
    if ( have_posts() ) : while ( have_posts() ) : the_post();
    global $post;
      echo '<div class="header"><h1>'.$post->post_title.'</h1></div>';
      // Show all available special fields
      echo '<div class="fieldCollection group">';
      foreach ($fields as $field) {
        // Make var for localized label, i.e. $yearLabel = "Jahr"
        $variableNameLabel = $field['type'].'Label';
        $$variableNameLabel = $field[$shortLocale];
        // Make var for localized content, i.e. $year = 2012
        $variableNameValue = $field['type'];
        $$variableNameValue = get_field($field['type'], $post->ID);
        if($field['output'] === true && $$variableNameValue != null){
          $suffix = ' '. $field['suffix_'.$shortLocale];
          echo '<div class="field"><h2>'.$field[$shortLocale].'</h2>';
          echo '<span>'.$$variableNameValue.$suffix.'</span></div>';
        }
      }
      echo '</div>';
      $gallery = get_field('gallery', $post->ID);
      $image = $gallery[getImageNumber(false)]['image']['sizes']['large'];

      // Datasheet download button
      if($datasheet){
        echo '<a class="datasheet" href="'.$datasheet['url'].'" target="_blank">'.$datasheetLabel.'</a>';
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
        echo '<a class="map" href="https://maps.google.com/maps?q='.$mapString.'"><img class="map lazy" data-src="'.$mapSource.'" alt=""></a>';
      }

    endwhile;
    endif;

    // get all projects
    $projectsID = 44;
    $projects = get_pages('child_of='.$projectsID);
    usort($projects, "sortProjects");
    // find projects of same type as current
    $projectsOfSameTypeAsPost = array();
    $index = 0;
    foreach($projects as $project){
      $category = get_field('project_category', $project->ID);
      if($category == $project_category){
        if($project->ID == $post->ID){
          $nextProjectIndex = $index + 1;
        } else {
          array_push($projectsOfSameTypeAsPost, $project);
        }
        // if this is not the last project in this category, find ID of next project
        if($index == $nextProjectIndex){
          $nextProjectPageID = $project->ID;
        }
        $index++;
      }
    }
    // if this was the last project in a category, find next category
    if($nextProjectIndex >= count($projectsOfSameTypeAsPost)){
      $nextProjectPageID = getFirstPostOfNextAvailableCategoryAfter($project_category);
    }

    // small excursion to find similar projects
    $randomProjectIndices = array_rand($projectsOfSameTypeAsPost, 2);
    echo '<div class="similarProjects">';
    echo '<h2>'.$translations['similiarHeadline'][$shortLocale].'</h2>';
    foreach($randomProjectIndices as $randomProjectIndex){
      echo '<div>';
      echo '<a href="'.get_permalink($projectsOfSameTypeAsPost[$randomProjectIndex]->ID).'"><strong>'.get_field('year', $projectsOfSameTypeAsPost[$randomProjectIndex]->ID).'</strong>';
      echo '<p>'.qtrans_use($shortLocale, get_post($projectsOfSameTypeAsPost[$randomProjectIndex])->post_title,false).'</p></a>';
      echo '</div>';
    }
    echo '</div>';

    // End of project description
    echo '</div></ul>';

    function getFirstPostOfNextAvailableCategoryAfter($currentCategory){
      global $project_categories, $projects;
      $nextCategoryIndex = array_search($currentCategory, $project_categories) + 1;
      // if this is the last category, loop around to the first
      if($nextCategoryIndex >= count($project_categories)){
        $nextCategoryIndex = 0;
      }
      $nextCategory = $project_categories[$nextCategoryIndex];
      foreach($projects as $project){
        $category = get_field('project_category', $project->ID);
        // just use the first page of the matching category
        if($category == $nextCategory){
          return $project->ID;
        }
      }
      // if we get to this point, the category was empty. Let's try the next one:
      return getFirstPostOfNextAvailableCategoryAfter($nextCategory);
    }

    $nextProjectPermalink = get_permalink($nextProjectPageID);

    // image navi
    // lots of jumping through hoops to get the correct urls with locales
    // (sometimes the locale string vanishes in the middle of the url)
    if(count($gallery) > 1){
      $shortLocale = substr(get_locale(), 0, 2);
      $root = site_url();
      $rootWithLocale = $root."/".$shortLocale;
      $completeUrl = str_replace($root, $rootWithLocale, full_url());
      $url = str_replace('http://', '', $completeUrl);
      $urlArray = array_filter(explode("/", $url));
      $lastHash = $urlArray[count($urlArray)-1];
      $current = getImageNumber(true);
      $prev = $current - 1;
      if($prev <= 0){
        $prev = count($gallery);
      }
      $next = $current + 1;
      if($next > count($gallery)){
        $lastInGalleryClass = " lastImageInGallery";
        $next = 1;
      } else {
        $lastInGalleryClass = "";
      }
      if(strlen($lastHash) <= 2){
        // probably is image number
        array_pop($urlArray);
        $baseUrl = join($urlArray, "/");
      } else {
        // probably isn't
        $baseUrl = join($urlArray, "/");
      }
      echo '<div class="prevNavi"><a href="http://'.$baseUrl."/".$prev.'" class="galleryNav previous"><span>PREVIOUS</span></a></div>';
      echo '<div class="nextNavi">';
      echo '<a href="http://'.$baseUrl."/".$next.'" class="galleryNav next repeat'.$lastInGalleryClass.'"><span>REPEAT GALLERY</span></a>';
      echo '<a href="'.$nextProjectPermalink.'" class="galleryNav nextProject'.$lastInGalleryClass.'"><span>NEXT PROJECT</span></a>';
      echo '<a href="http://'.$baseUrl."/".$next.'" class="galleryNav next'.$lastInGalleryClass.'"><span>NEXT</span></a>';
      echo '</div>';
    }

    function full_url(){
      $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
      $protocol = substr(strtolower($_SERVER["SERVER_PROTOCOL"]), 0, strpos(strtolower($_SERVER["SERVER_PROTOCOL"]), "/")) . $s;
      $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
      return $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];
    }

  ?>
</div> <!-- end of viewport -->
<div id="backgroundImage" data-gallery='<?php echo json_encode($gallery) ?>'>
  <img src="<?php echo $image; ?>" alt="">
</div>

<?php get_footer(); ?>