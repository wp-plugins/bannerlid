=== Plugin Name ===
Contributors: web_lid
Tags: banners, ads, adverts
Requires at least: 3.8
Tested up to: 4.3
Stable tag: 1.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Simple banner management plugin.

== Description ==

Bannerlid provides a simple Wordpress admin extension to add banners and groups of banners to your Wordpress template or your Wordpress content. Banners can be added through content shortcodes or functions in your template files.

The plugin also tracks clicks and impressions and displays the data in html5 charts in wp-admin. Stats include who's been clicking, when and where from.

Banners can be standard graphic file such as gif or png. It also supports flash banners, although these can cause speed and compatibility issues.

The plugin does not facilitate the selling of banners or banner space. I created this to be a simple Banner management system that provided clients with simplicity and important stats rather than to try and do too much. 

The plugin comes with some basic filters and hook actions for customization.

== Installation ==

1. Copy bannerlid directory to your wp-content/plugins directory.
1. In wp-admin go to Plugins > Installed Plugins
1. Find 'Banner Boss' and click 'Activate'
2. See instruction.txt in the plugin root for more instructions

== Frequently Asked Questions ==

= What is flash support like? =

Basic flash support is provided. The plugin can show flash banners and hyperlinks can be used on them, however this requires some CSS and has not been tested on older browsers. Also, flash can cause speed/stability issues.  

= Does the plugin have banner fade/transitions? =

No, I have kept the plugin as basic as possible so that the user can add his own transitions / slides or whatever by using one of the libraries already out there. The banners are wrapped in divs with classes so you should be able to apply jquery to them easily.

= Does the plugin support text/html banners =

I've been thinking about this one and it's something I might add in so users can add in html for Google Ads etc. If there are requests for this I'll add it.

= What stats does it collect? =

The system collects banner and zone clicks and impressions as well as the time, WP user, country, ip and date of the action.



== Changelog ==

= 1.1.0 =
* Added statistics reporting using chartjs
* Added start and end publish dates

= 1.0.0 =
* First release
