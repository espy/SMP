<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content
 * after.  Calls sidebar-footer.php for bottom widgets.
 *
 * @package WordPress
 * @subpackage Boilerplate
 * @since Boilerplate 1.0
 */
?>
  		</section><!-- #main -->
  		<footer role="contentinfo">
  <?php
  	/* A sidebar in the footer? Yep. You can can customize
  	 * your footer with four columns of widgets.
  	   get_sidebar( 'footer' );
     */

    # Language switcher
    $shortLocale = substr(get_locale(), 0, 2);
    switch($shortLocale){
      case "de":
        echo '<a href="" class="localeSwitcher" data-target-locale="en" data-base-url="'.get_bloginfo('url').'">English</a>';
      break;
      case "en":
        echo '<a href="" class="localeSwitcher" data-target-locale="de" data-base-url="'.get_bloginfo('url').'">Deutsch</a>';
      break;
    }
  ?>
  		</footer><!-- footer -->
      <!-- <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script> -->
      <script src="<?php echo get_bloginfo('url'); ?>/wp-content/themes/SMP/js/jquery.min.js"></script>
      <script src="<?php echo get_bloginfo('url'); ?>/wp-content/themes/SMP/js/underscore-min.js"></script>
      <script src="<?php echo get_bloginfo('url'); ?>/wp-content/themes/SMP/js/smp.js"></script>
  <?php
  	/* Always have wp_footer() just before the closing </body>
  	 * tag of your theme, or you will break many plugins, which
  	 * generally use this hook to reference JavaScript files.
  	 */
  	wp_footer();
  ?>
    </div><!-- wrapper -->
	</body>
</html>