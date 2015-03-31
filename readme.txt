=== Plugin Name ===
Contributors: web_lid
Tags: banners, ads, adverts
Requires at least: 3.8
Tested up to: 4.3
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Simple banner management plugin.

== Description ==

Bannerlid provides a simple Wordpress admin extension to create banners and groups of banners. Banners can be added through content shortcodes or functions in your template files.

The plugin also tracks clicks and impressions in a stats table in the database. At the moment there is no interface to view the stats. This feature will be added soon.

Banners can be standard graphic file such as gif or png. It also supports flash banners, although these can cause speed and compatibility issues.

== Installation ==

1. Upload `bannerlid` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Create a banner in the newly created 'banners' page
1. Use the following shortcode in your content to load a banner [banner id="*"] where * is the numeric ID of the banner
1. You can add a zone to your page using [zone id="*"]
1. You can add banners and zones directly to your template using the functions `<?php BannerlidBanner(array("id" => 1)); ?>` and `<?php BannerlidZone(array("id" => 1)); ?>`

== Frequently Asked Questions ==

= Where are the stats? =

The stats are stored in the table _bannerlid_stats. 

= What is flash support like? =

Basic flash support is provided. The plugin can show flash banners and hyperlinks can be used on them, however this requires some CSS and has not been tested on older browsers. Also, flash can cause speed/stability issues.  



== Changelog ==

= 1.0.0 =
* First release
