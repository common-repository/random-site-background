=== Random site background ===
Contributors: selff
Donate link: http://www.selikoff.ru
Plugin Site: http://www.selikoff.ru
Tags: background, images, ajax
Requires at least: 3.0.0
Tested up to: 3.2
Stable tag: 1.0.2

Set random background on your site from web by season and daytime theme.

== Description ==

Set random background on your site from web by season and daytime theme.
1. Standart version of plugin have a static library of web links to images. You may change it.
2. Premium version of plugin automaticaly (one in some hour) found by optionality keywords and update library of web links images with help web search engines.

The both version this plugin consist image links in database by tag "season" and "daytime".
Season tags is a: winter, spring, summer, autumn :)
Daytime is a: morning (05-10 hour), day (11-17), evening (18-21), night (22-04)

Some of its features are:

* Easy setup.
* Minimal Configuration. 
* Ajax technology. Background loading after the content will be loaded.
* Depending on user resolution (window dimension) will be choise the best image link from library; For example: in library store links to images with resolutions: 1600x1200, 1640x1275, 1600x1600, 2272x1704, 3624x2448, 3888x2592. For user with resolution 1368x768 will be choise images with best resolution for this user. For this example is a 3624x2448 and 3888x2592 but ratio of width/height is best for user width reso=1368x768
* User may be reload page background with ajax button without reload the page;

TODO:
1. Admin control images links and more options (very soon)
2. Tell me what todo :)

== Installation ==
Intallation is easy. Install it normally as you do with rest of the plugins.

1. Download and unzip the plugin .zip.
2. Copy the unzipped folder in your Plugins directory under wordpress installation. (wp-content/plugins)
3. Activate the plugin through the plugin window in the admin panel.
4. Go to widgets page and drag&drop "Reload background" button to your sidebar if you want.

== Screenshots ==
1. Frontend View

== Changelog ==

= 1.0.1 =
* First Release

= 1.0.2 =
* Check some bugs with ajax reload. Up static images library.

== Frequently Asked Questions ==

= Where I can see the demo? =

Plugin using on my site. Wellcome http://selikoff.ru 

= What the theme on your site? =

"Suffusion" remaking

= How I can change "Reload" button color? =

Change the /wp-content/plugins/rand-background/css/button.css

= How I can change backgound images? =

Now, go to phpmyadmin on hosting, and see the table "wp_rndbg_images" (wp_ - default wordpress table prefix). But very soon it set by options.

= What the filds "signature,copyright" on your table "wp_rndbg_images" in database? =

Its now not used filds, but more.. soon.

= Can I use my photo by background on site? =

Yes, load its on your site and set add new record to table "wp_rndbg_images" with local link
   