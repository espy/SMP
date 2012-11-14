<?php
/**
 * Template Name: Studio
 */

$translations = array(
  jobAttachment => array(en => 'Download sheet', de => 'Informationen'),
  staffPhone => array(en => 'Extension', de => 'Durchwahl')
);


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

get_header();
echo '<div class="viewport studio">';

$shortLocale = substr(get_locale(), 0, 2);

// The various page IDs
$studioID = 17;

$studioPageNameSlug = basename(get_permalink($studioID));

$aboutID = 227;
$partnersID = 194;
$staffID = 206;
$jobsID = 219;
$awardsID = 237;

if ( have_posts() ) : while ( have_posts() ) : the_post();
global $post;
if($post->ID != $studioID){
  $currentSection = qtrans_use($shortLocale, get_post($post->ID)->post_title,false);
}
endwhile; endif;

// The studio gallery
$gallery = get_field('gallery', $studioID);
$backgroundImage = $gallery[getImageNumber(false)]['image']['sizes']['large'];
?>

<div id="backgroundImage" class="studio" data-gallery='<?php echo json_encode($gallery) ?>'>

<?php

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
    if($urlArray[count($urlArray) - 1] != $studioPageNameSlug){
      array_pop($urlArray);
    }
    $baseUrl = join($urlArray, "/");
  }
  echo '<div class="prevNavi"><a href="http://'.$baseUrl."/".$prev.'" class="galleryNav previous"><span>PREVIOUS</span></a></div>';
  echo '<div class="nextNavi">';
  echo '<a href="http://'.$baseUrl."/".$next.'" class="galleryNav next repeat'.$lastInGalleryClass.'"><span>REPEAT GALLERY</span></a>';
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

  <div class="columnsOverlay"></div>
  <img src="<?php echo $backgroundImage; ?>" alt="">
</div>

<?php
// About/Studio
$aboutTitle = qtrans_use($shortLocale, get_post($aboutID)->post_title,false);
$aboutContent = wpautop( qtrans_use($shortLocale, get_post($aboutID)->post_content,false), true );
echo '<div class="section about group">';
echo '<div class="info">';
echo '<strong class="'.$aboutTitle.'">'.$aboutTitle.'</strong>';
echo '<p>'.$aboutContent.'</p>';

// Grey buttons
echo '<ul>';
// About links (i.e. Google map link)
if( get_field('links', $aboutID) ){
  echo "<li>";
  while( has_sub_field('links', $aboutID) ){
    $url = get_sub_field('url');
    $a = get_sub_field('a_'.$shortLocale);
    echo '<a class="datasheet showOnHover" href="'.$url.'" target="_blank">'.$a.'</a>';
  }
  echo "</li>";
}
// Attachments (i.e. vCard)
if( get_field('attachments', $aboutID) ){
  while( has_sub_field('attachments', $aboutID) ){
    echo "<li>";
    $url = get_sub_field('attachment');
    $a = get_sub_field('a_'.$shortLocale);
    echo '<a class="datasheet showOnHover" href="'.$url["url"].'" target="_blank">'.$a.'</a>';
    echo "</li>";
  }
}
echo '</ul>';
echo '</div>';

// About images with hover text
echo '<div class="imageBlock first"><p class="showOnHover">'.get_field('text_1_'.$shortLocale, $aboutID).'</p>';
$image_1 = get_field('foto_1', $aboutID);
echo '<img  class="hideOnHover" src="'.$image_1['url'].'"></div>';
echo '<div class="imageBlock second"><p class="showOnHover">'.get_field('text_2_'.$shortLocale, $aboutID).'</p>';
$image_2 = get_field('foto_2', $aboutID);
echo '<img  class="hideOnHover" src="'.$image_2['url'].'"></div>';
echo '</div>';


// Partners
echo '<div class="section partners group">';
$partnersTitle = qtrans_use($shortLocale, get_post($partnersID)->post_title,false);
echo '<strong class="'.$partnersTitle.'">'.$partnersTitle.'</strong>';

echo '<ul class="group">';
if( get_field('partners', $partnersID) ){
  while( has_sub_field('partners', $partnersID) ){
    echo '<li class="group">';
    $name = get_sub_field('name');
    $desc = get_sub_field('description_'.$shortLocale);
    $image = get_sub_field('image');
    echo '<span>'.$name.'</span>';
    echo '<p class="showOnHover">'.$desc.'</p>';
    echo '<img src="'.$image["sizes"]["large"].'"/>';
    echo "</li>";
  }
}
echo '</ul>';
echo '</div>';


// Staff
echo '<div class="section staff group">';
$staffTitle = qtrans_use($shortLocale, get_post($staffID)->post_title,false);
echo '<strong class="'.$staffTitle.'">'.$staffTitle.'</strong>';

echo '<ul>';
$staffPerRow = 3;
$index = 0;

if( get_field('staff_members', $staffID) ){
  while( has_sub_field('staff_members', $staffID) ){
    $rowType = rand(1,5);
    if($index == 0){
      $front = '<div class="staffRow rowType_'.$rowType.' group"><div class="staffInfo"><ul>';
      $back = '<div class="staffImages group"><ul>';
    }
    $name = get_sub_field('name');
    $phone = get_sub_field('phone');
    $email = get_sub_field('email');
    $image = get_sub_field('image');
    $front .= '<li><a class="staff_'.$index.' showOnHover" data-item="staff_'.$index.'" href="mailto:'.$email.'">'.$name.'</a></li>';
    $back .= '<li class="staff_'.$index.'" data-item="staff_'.$index.'"><img src="'.$image["sizes"]["large"].'"/><div class="overlay"><span class="title">'.$translations['staffPhone'][$shortLocale].'</span><span class="phone">'.$phone.'</span></div></li>';
    if($index == $staffPerRow-1){
      $front .= '</ul></div>';
      $back .= '</ul></div></div>';
      echo $front.$back;
      $index = 0;
    } else {
      $index++;
    }
  }
}
echo '</ul>';
echo '</div>';

// Jobs
echo '<div class="section jobs group">';
$jobsTitle = qtrans_use($shortLocale, get_post($jobsID)->post_title,false);
echo '<strong class="'.$jobsTitle.'">'.$jobsTitle.'</strong>';

echo '<div class="subline">'.get_field('subline_'.$shortLocale, $jobsID).'</div>';
echo '<div class="general">'.wpautop(qtrans_use($shortLocale, get_post($jobsID)->post_content, false), true).'</div>';

$leftColumn = '<ul class="left">';
$rightColumn = '<ul>';
$index = 0;
if( get_field('job', $jobsID) ){
  while( has_sub_field('job', $jobsID) ){
    $item = '<li>';
    $title = get_sub_field('title_'.$shortLocale);
    $description = get_sub_field('description_'.$shortLocale);
    $attachment = get_sub_field('attachment');
    $item .= '<strong>'.$title.'</strong>';
    $item .= '<p>'.$description.'</p>';
    $item .= '<a class="datasheet" target="_blank" href="'.$attachment['url'].'">'.$translations['jobAttachment'][$shortLocale].'</a>';
    $item .= "</li>";
    if($index % 2 == 0){
      $leftColumn .= $item;
    } else {
      $rightColumn .= $item;
    }
    $index++;
  }
}
$leftColumn .= '</ul>';
$rightColumn .= '</ul>';
echo $leftColumn;
echo $rightColumn;
echo '</div>';

// Awards
echo '<div class="section awards group">';
$awardsTitle = qtrans_use($shortLocale, get_post($awardsID)->post_title,false);
echo '<strong class="'.$awardsTitle.'">'.$awardsTitle.'</strong>';

echo '<ul class="group">';
if( get_field('awards', $awardsID) ){
  while( has_sub_field('awards', $awardsID) ){
    echo "<li>";
    $year = get_sub_field('year');
    $description = get_sub_field('description_'.$shortLocale);
    echo '<h2>'.$year.'</h2>';
    echo '<p>'.$description.'</p>';
    echo "</li>";
  }
}
echo '</ul>';
echo '</div>';

?>
</div> <!-- end of viewport -->

<script>var scrollToSection = "<?php echo $currentSection ?>";</script>


<?php get_footer(); ?>