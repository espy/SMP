<?php
/**
 * Template Name: Projects
 */

$project_categories = array('public', 'residential', 'hotel', 'office', 'exhibition', 'masterplanning');


get_header(); ?>

<!-- LOOP starts here -->
<div class="viewport">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post();
  global $post;

  $projects = get_pages('child_of='.$post->ID);
  # Sort projects by date
  function sortProjects($a, $b) {
      return get_field('year', $b->ID) - get_field('year', $a->ID);
  }
  usort($projects, "sortProjects");

  echo '<div class="listTitles">';
  foreach ($project_categories as $project_category) {
    echo '<div class="listTitle">'.$project_category.'</div>';
  }
  echo '</div>';

  foreach ($project_categories as $project_category) {
    echo '<ul class="projectList active projects">';
    // Here's a line of dummy projects
    //echo '<a href="http://localhost/stoermer/app/de/projekte/another-public-project/"><li class="project" data-image="http://localhost/stoermer/app/wp-content/uploads/2012/10/419671263_bf1053cd69_b.jpg"><strong>2012</strong><span>Noch ein öffentliches Projekt</span></li></a>';
    foreach($projects as $project){
      #dump_r($project);
      $category = get_field('project_category', $project->ID);
      if($category == $project_category){
        $title = $project->post_title;
        $gallery = get_field('gallery', $project->ID);
        $year = get_field('year', $project->ID);
        $image = $gallery[0]['image']['sizes']['large'];
        echo '<a href="'.get_permalink($project->ID).'"><li class="project" data-image="'.$image.'">';
        echo '<strong>'.$year.'</strong><span>'.$title.'</span>';
        echo '</li></a>';
      }
    }
    echo '</ul>';
  }
  ?>
</div> <!-- end of viewport -->
<div id="backgroundImage">
  <img src="" alt="">
</div>

<!-- LOOP ends here -->
<?php endwhile; else: ?>
<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
<?php endif; ?>

<?php get_footer(); ?>