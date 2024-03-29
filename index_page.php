<?php
/**
 * Template Name: Index page
 */

$project_categories = array('public', 'residential', 'hotel', 'office', 'exhibition', 'masterplanning');

get_header();

$projectsID = 44;
$allProjects = get_pages('child_of='.$projectsID);

$startPageProjects = array();

foreach($allProjects as $project){
  #dump_r($project);
  $show = get_field('show_on_startpage', $project->ID);
  if($show == true){
    $startPageProjects[] = $project;
  }
}

shuffle($startPageProjects);

$project = $startPageProjects[0];

$gallery = get_field('gallery', $project->ID);
$image = $gallery[0]['image']['sizes']['large'];

?>
<div class="viewport index group">
  <?php
    echo '<ul class="projectList index">';
    // Welcome
    echo '<li class="project welcome">';
    if ( have_posts() ) : while ( have_posts() ) : the_post();
    global $post;
      echo $post->post_content;
    endwhile;
    endif;
    echo '</li>';
    // Project description
    echo '<a href="'.get_permalink($project->ID).'"></li>';
    echo '<li class="project">';
    echo '<strong>'.$project->post_title.'</strong>';
    $excerpt = explode('<!--more-->',$project->post_content);
    echo $excerpt[0];
    echo '</li></a>';
    echo '</ul>';
  ?>
</div> <!-- end of viewport -->
<div id="backgroundImage">
  <img src="<?php echo $image; ?>" alt="">
</div>

<?php get_footer(); ?>