<?php
/*
  Plugin Name: Random site background
  Plugin URI: http://selikoff.ru/webmaster/random-background-wp-plugin
  Description: The "Random site background" plugin is used to enhance the view of Wordpress theme
  Author: Selikoff Andrey
  Version: 1.0.2
  Author URI: http://www.selikoff.ru
  License: A "Slug" license name e.g. GPL2
*/

/*
  Copyright 2012 Selikov Andrey (email : selffmail@gmail.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
  
*/

// Тут указываем версии 
define('VERSION', '1.0.2');  // Версия плагина
define('DB_VERSION', '1.0.2'); // Версия структуры данных
define('STATUS', 'beta'); // Статус версии

include_once dirname( __FILE__ ) . '/rand-bot-crawler.php';
include_once dirname( __FILE__ ) . '/widget.php';

// Регистрируем хуки для активации и деактивации
register_activation_hook(__FILE__,'rndbg_install_plugin');
register_deactivation_hook(__FILE__,'rndbg_uninstall_plugin');

//load jquery file
wp_enqueue_script('jquery');
wp_register_script('rndbg', WP_PLUGIN_URL . '/rand-background/js/jquery.rndbg.js.php');
wp_enqueue_script('rndbg');
wp_register_style('rndbgcss', WP_PLUGIN_URL . '/rand-background/css/button.css');
wp_enqueue_style('rndbgcss');
add_action('wp_ajax_ajax_bgload', 'ajax_rndbg_callback');
add_action('wp_ajax_nopriv_ajax_bgload', 'ajax_rndbg_callback');


function rndbg_daytime($user_offset_sec){
  $server_offset_sec = date("Z")*60;
  $offset = (-1)*$user_offset_sec + $server_offset_sec;  
  $time = time()+$offset;
  switch(date('H',$time)){
      case 22: case 23: case 0: case 1: case 2: case 3: case 4: return 'night'; break;
      case 5: case 6: case 7: case 8: case 9: case 10: return 'morning'; break;
      case 11: case 12: case 13: case 14: case 15: case 16: case 17: return 'day'; break;
      case 18: case 19: case 20: case 21: return 'evening'; break;
  }  
}
function rndbg_season(){
  switch(date('m')){
      case 1: case 2: case 12: return 'winter'; break;
      case 3: case 4: case 5: return 'spring'; break;
      case 6: case 7: case 8: return 'summer'; break;
      case 9: case 10: case 11: return 'autumn'; break;
  }
}

function ajax_rndbg_callback(){
  global $wpdb;
  $w   = empty($_POST['w']) ? '800' : $_POST['w'];
  $h   = empty($_POST['h']) ? '600' : $_POST['h'];
  //if (preg_match('|url\("([^"]+)"\)$|' ,$_POST['link'],$m)) $l = $m[1]; else $l = '';
  //$l   = empty($_POST['link']) ? '' : preg_replace('|url\("([^)]*)"\)|',"$1",$_POST['link']);
  if ($_POST['link']) $l = substr($_POST['link'],6,-3); else $l = '';
  $o   = empty($_POST['ofs']) ? '0' : $_POST['ofs']*60;
  $k = round(($w/$h)*100)/100;
  $difference = 10;
  $season = rndbg_season();
  $daytime = rndbg_daytime($o);
  $query = ' SELECT ri.*,round(ri.width/ri.height,2) as k FROM `' . $wpdb->prefix . 'rndbg_images` ri WHERE ri.season LIKE "'.$season.'" AND ri.daytime LIKE "'.$daytime.'" AND ri.width > '.$w.' AND ri.height > '.$h.'';
  if ($l) $query .=' AND ri.link NOT LIKE "'.$l.'"';
  // NOT LIKE ""http://literateknits.files.wordpress.com/2010/12/winter-sunset-alaska.jpg\";
  $query .= ';';
  $images = $wpdb->get_results( $query );
  foreach ( $images as $i=>$image ) 
  {
    $r = $k - (float)$image->k;
    if ($r < $difference){
      $difference = $r;
    }
  }
  $admissible_dif = $difference + ($difference/10);
  $arr = array();
  foreach ( $images as $i=>$image ) 
  {
    $r = $k - (float)$image->k;
    if ($r < $admissible_dif){
      $arr[] = $i;
    }
  }
  $index = rand(0,(count($arr)-1));
  //if ($l) echo $query; else
  echo $images[$index]->link;//."?season=".$season."&daytime=".$daytime;
  exit;
}

function rndbg_create_tables() {
  global $wpdb;
  if ( ! empty($wpdb->charset) ) $charset_collate =  ' DEFAULT CHARACTER SET ' . $wpdb->charset;
  if ( ! empty($wpdb->collate) ) $charset_collate .= ' COLLATE ' . $wpdb->collate;
$query = ' CREATE TABLE IF NOT EXISTS`' . $wpdb->prefix . 'rndbg_images` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `season` varchar(16) NOT NULL,
  `daytime` varchar(16) NOT NULL,
  `timestamp` datetime NOT NULL,
  `link` varchar(255) NOT NULL,
  `signature` varchar(255) NOT NULL,
  `copyright` varchar(255) NOT NULL,
  `width` int(2) unsigned NOT NULL,
  `height` int(2) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `season` (`season`,`daytime`),
  KEY `width` (`width`,`height`),
) ENGINE=MyISAM AUTO_INCREMENT=47 ' . $charset_collate . ' ; ';
  $wpdb->query( $query );
}
function rndbg_drop_tables() {
  global $wpdb;
  $query = ' DROP TABLE IF EXISTS `' . $wpdb->prefix . 'rndbg_images` ; ';
  $wpdb->query( $query );
}

function rndbg_install_plugin() {
rndbg_create_tables();
$query = "
INSERT INTO `wp_rndbg_images` (`id`, `season`, `daytime`, `timestamp`, `link`, `signature`, `copyright`, `width`, `height`) VALUES
(20, 'winter', 'night', '2012-02-19 06:28:36', 'http://uvee.ru/images/feeria/quiet_winter_night_hd_widescreen_wallpapers_2560x1.jpeg', 'uvee.ru', '', 2560, 1600),
(21, 'winter', 'night', '2012-02-19 06:28:37', 'http://www.jointmeta.com/photos/photos/night_winter.jpg', 'jointmeta.com', '', 1600, 1200),
(44, 'winter', 'night', '2012-02-20 06:06:09', 'http://www.vanachteren.net/wp-content/uploads/2009/03/niagara_falls_winter_night.jpg', 'Niagara Falls by Night', '', 3072, 2304),
(22, 'winter', 'night', '2012-02-19 06:31:32', 'http://www.sky-wallpaper.com/uploads/2011-01/beautiful-winter-snow-sighting-wallpaper/1294800832-3QEOEBI.jpg', 'sky-wallpaper.com', '', 1920, 1080),
(23, 'winter', 'night', '2012-02-19 06:31:47', 'http://upload.wikimedia.org/wikipedia/commons/5/52/Downtown_Minneapolis_at_night.JPG', 'Downtown Minneapolis at night. Copyright 2006 Mark Vancleave', 'Creative Commons Attribution-Share Alike 3.0 Unported', 2034, 1728),
(24, 'winter', 'evening', '2012-02-19 06:41:16', 'http://liachang.files.wordpress.com/2010/11/moon_1356.jpg', 'The Empire State Building on a Crisp Winter''s Night Photo by Lia Chang liachang.wordpress.com', '', 3512, 2604),
(25, 'winter', 'morning', '2012-02-19 06:42:48', 'http://literateknits.files.wordpress.com/2010/12/winter-sunset-alaska.jpg', 'literateknits.wordpress.com', '', 1600, 1200),
(26, 'winter', 'morning', '2012-02-19 06:48:34', 'http://tc.v11.cache2.c.bigcache.googleapis.com/static.panoramio.com/photos/original/16904974.jpg?redirect_counter=1', 'World Map USA MI Ludington  panoramio.com', '', 2592, 1944),
(27, 'winter', 'day', '2012-02-19 06:49:34', 'http://openwalls.com/image/19470/winter_morning_1_1640x1275.jpg', 'openwalls.com', '', 1640, 1275),
(28, 'winter', 'day', '2012-02-19 07:39:22', 'http://www.nps.gov/sajh//images/20100208192733.jpg', 'nps.gov', '', 3264, 2448),
(29, 'winter', 'day', '2012-02-19 07:39:34', 'http://v7.lscache8.c.bigcache.googleapis.com/static.panoramio.com/photos/original/45165064.jpg', 'panoramio.com', '', 3888, 2592),
(30, 'winter', 'day', '2012-02-19 07:46:59', 'http://www.worldwanderingkiwi.com/wp-content/uploads/2011/12/MG_36311.jpg', 'Bran Castle, Transylvania, Romania worldwanderingkiwi.com', '', 3888, 2592),
(31, 'winter', 'day', '2012-02-19 07:47:12', 'http://bylaus.files.wordpress.com/2011/02/day-1-point-hotel-6-the-view.jpg', 'bylaus.wordpress.com', '', 2272, 1704),
(32, 'winter', 'day', '2012-02-19 07:51:11', 'http://4.bp.blogspot.com/-fwLljjiFIhM/TxO3i6hON1I/AAAAAAAAFzI/8e9F7Kygrf8/s1600/IMG_3904.JPG', 'moonrox.blogspot.com', '', 1600, 1600),
(33, 'winter', 'day', '2012-02-19 07:52:50', 'http://4.bp.blogspot.com/_s-WPG9brN6I/TTW9uhJWYVI/AAAAAAAAAA4/YcKlfguWPsU/s1600/Plum%2BIsland%2BJan2011%2B005.jpg', 'Plum Island scenes. runningahospital.blogspot.com', '', 1600, 1200),
(34, 'winter', 'evening', '2012-02-19 07:57:24', 'http://www.patentlyphotographic.com/wp-content/uploads/2011/12/DSC_4773.jpg', 'London patentlyphotographic.com', '', 4256, 2832),
(35, 'winter', 'evening', '2012-02-19 08:01:30', 'http://2.bp.blogspot.com/_pMqNaWEUTt8/TOlxfK4unGI/AAAAAAAAGBM/-AHUkFpzTCs/s1600/SAM_1017.JPG', 'Sunset revpatrickcomerford.blogspot.com', '', 1600, 1200),
(36, 'winter', 'evening', '2012-02-19 08:05:23', 'http://www.nypost.com/rw/nypost/2011/01/26/news/photos_stories/WinterWeather090745.jpg', 'nypost.com', '', 2700, 1843),
(37, 'winter', 'evening', '2012-02-19 08:05:04', 'http://2.bp.blogspot.com/_E0Z6piWkuIc/TAh0aw_0gfI/AAAAAAAADrM/RC4rLEaiqtY/s1600/saigon2.JPG', 'Saigon sarahsandersonwanderlust.blogspot.com', '', 1600, 1200),
(38, 'winter', 'morning', '2012-02-19 08:16:08', 'http://photo-smile.com.ua/wp-content/gallery/zima/winter_morning_1600x1200.jpg', '', '', 1600, 1200),
(39, 'winter', 'morning', '2012-02-19 08:18:01', 'http://1.bp.blogspot.com/-DeTW7A1lI1k/TsFn6OA04qI/AAAAAAAAAF8/gxr34ghFctU/s1600/FebMarch09+022.jpg', 'leahpartridgesings.blogspot.com', '', 1600, 1200),
(40, 'winter', 'morning', '2012-02-19 08:20:08', 'http://irisheyesoncairo.files.wordpress.com/2011/09/dscf0196.jpg', 'irisheyesoncairo.wordpress.com', '', 3648, 2736),
(41, 'winter', 'morning', '2012-02-19 08:20:26', 'http://nikirudolph.files.wordpress.com/2011/12/imag0811.jpg', 'nikirudolph.com', '', 3264, 1952),
(42, 'winter', 'morning', '2012-02-19 08:22:03', 'http://rutheh.files.wordpress.com/2011/01/sunrise-from-the-birmingham-bridge.jpg', 'rutheh.com', '', 4752, 3168),
(43, 'winter', 'morning', '2012-02-19 08:23:35', 'http://scottseyephotos.files.wordpress.com/2012/01/dsc_0580wm.jpg', 'scottseyephotos.wordpress.com', '', 2422, 1615),
(45, 'winter', 'night', '2012-02-20 06:06:55', 'http://desktopart.org/var/albums/Winter/Winter_Night_14.jpg', 'City Wallpaper - Valentine Wallpaper', '', 1920, 1080),
(46, 'winter', 'night', '2012-02-21 02:57:31', 'http://globeattractions.com/wp-content/uploads/2012/02/winter-night-hd-wallpaper.jpg', 'Winter Night hd wallpaper globeattractions.com', '', 1920, 1200);
";
$wpdb->query( $query );
}

?>