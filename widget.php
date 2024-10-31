<?php
/*
  @package Random site background
  @version 1.0.2
  @author Selikoff Andrey <selffmail@gmail.com>
 */
/*
  Plugin Name: Random site background 
  Plugin URI: http://selikoff.ru/webmaster/random-background-wp-plugin
  Description: The "Random site background" plugin is used to enhance the view of Wordpress theme
  Author: Selikoff Andrey
  Version: 1.0.2
  Author URI: http://www.selikoff.ru
  License: A "Slug" license name e.g. GPL2
*/
function widget_rndbgreloader($args) {
    extract($args);
?>
    <?php echo $before_widget; ?>
    <div id="ajax-bg-loader"></div>
    <div class="rndbg_reload">
      <a class="large awesome rndbgreloader" id="<?php echo $id ?>" href="#">Reload background</a>
      <span class="ajax_rndbg_loading" style="display: none;"><img src="<?php WP_PLUGIN_URL . '/rand-background/img/ajax-loader.gif'; ?>" alt="Loading.." /></span>
    </div>
    <script type="text/javascript">jQuery(function() {jQuery("#<?php echo $id ?>").ajaxrndbgreload({});});</script>
    <?php echo $after_widget; ?>
<?php
}
wp_register_sidebar_widget(
    'rndbgreloader_1',        // your unique widget id
    'Reload background',          // widget name
    'widget_rndbgreloader',  // callback function
    array(                  // options
        'description' => 'Reload background button'
    )
);
