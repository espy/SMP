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

dump_wp_query();
dump_wp();
dump_post();

?>
      </section><!-- #main -->
      <footer>
      </footer><!-- footer -->
      <!-- <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script> -->
      <script src="<?php echo get_template_directory_uri(); ?>/js/jquery.min.js"></script>
      <script src="<?php echo get_template_directory_uri(); ?>/js/underscore-min.js"></script>
      <script src="<?php echo get_template_directory_uri(); ?>/js/smp.min.js"></script>
      <script type="text/javascript" src="http://fast.fonts.com/jsapi/0e219d25-8ad0-414f-9d00-0f494e371b4e.js"></script>
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

