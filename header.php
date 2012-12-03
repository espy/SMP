<?php
  //include 'dump_r.php';
  $shortLocale = substr(get_locale(), 0, 2);
?>

<!DOCTYPE html>
<!--[if lt IE 7 ]><html <?php language_attributes(); ?> class="no-js ie ie6 lte7 lte8 lte9"><![endif]-->
<!--[if IE 7 ]><html <?php language_attributes(); ?> class="no-js ie ie7 lte7 lte8 lte9"><![endif]-->
<!--[if IE 8 ]><html <?php language_attributes(); ?> class="no-js ie ie8 lte8 lte9"><![endif]-->
<!--[if IE 9 ]><html <?php language_attributes(); ?> class="no-js ie ie9 lte9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<title>
      <?php
			/*
			 * Print the <title> tag based on what is being viewed.
			 * We filter the output of wp_title() a bit -- see
			 * boilerplate_filter_wp_title() in functions.php.
			 */
			wp_title( '|', true, 'right' );
		  ?>
    </title>
		<link rel="profile" href="http://gmpg.org/xfn/11" />
		<link rel="stylesheet" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/css/smp.css" />
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
    <meta name="format-detection" content="telephone=no">
    <?php wp_head(); ?>
	</head>
  <body <?php body_class(); ?> >
    <div class="topBar"></div>
    <div class="wrapper group">
      <header role="banner">
        <a class="title" href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><h1><?php bloginfo( 'name' ); ?></h1></a>
        <ul id="nav">
          <?php
          if ( have_posts() ) : while ( have_posts() ) : the_post();
            global $post;
            $studioID = 17;
            $rootPages = get_pages( array( 'parent' => 0,  'sort_column' => 'menu_order', 'exclude' => 84  ));
            foreach($rootPages as $rootPage){
              if($rootPage->ID == $post->ID || $rootPage->ID == $post->post_parent){
                $listAttribute = ' class="current_page_item"';
              } else {
                $listAttribute = "";
              }
              echo '<li'.$listAttribute.'><a href="'.get_permalink($rootPage->ID).'">'.$rootPage->post_title.'</a></li>';
              if($rootPage->ID == $studioID && ($post->ID == $studioID || $post->post_parent == $studioID)){
                $studioAnchors = get_pages( array( 'child_of' => $studioID, 'sort_column' => 'menu_order' ));
                echo '<ul>';
                foreach($studioAnchors as $studioAnchor){
                  if($studioAnchor->ID == $post->ID){
                    $subListAttribute = ' class="current_page_item"';
                  } else {
                    $subListAttribute = "";
                  }
                  echo '<li'.$subListAttribute.'><a href="'.get_permalink($studioAnchor->ID).'" class="anchor" data-anchor="'.qtrans_use($shortLocale, get_post($studioAnchor->ID)->post_title,false).'">'.$studioAnchor->post_title.'</a></li>';
                }
                echo '</ul>';
              }
            }
          endwhile;
          endif;
          ?>
        </ul>
        <div class="contact" role="contentinfo">
          <a class="phone" href="tel:49 40 369 73 70">+49 40 369 73 70</a>
          <a class="mail" href="mailto:info@stoermer-partner.de">info@stoermer-partner.de</a>
          <?php
          $shortLocale = substr(get_locale(), 0, 2);
          $imprintID = 84;
          $imprint = get_page($imprintID);
          $imprintURL = get_permalink($imprintID);
          echo '<a class="imprint" href="'.$imprintURL.'">'.qtrans_use($shortLocale, $imprint->post_title, false).'</a>';

          # Language switcher
          global $qtranslate_slug;
          switch($shortLocale){
            case "de":
              echo '<a href="'.$qtranslate_slug->get_current_url('en').'" class="language">English</a>';
            break;
            case "en":
              echo '<a href="'.$qtranslate_slug->get_current_url('de').'" class="language">Deutsch</a>';
            break;
          }
          ?>
        </div>
  		</header>
      <section id="content" role="main">
