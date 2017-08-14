<?php
/*
Plugin Name: Advanced Instagram Carousel
Plugin URI: https://wordpress.org/plugins/advanced-instagram-carousel/
Description: Advanced Instagram Carousel is easy way to integrate your Instagram photos/feeds into your WordPress site. 
Version: 1.1.0
Author: Animesh
Author URI: http://www.thelogicalcoder.com/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


*/

define( 'TWAIC_VERSION', '1.1.0' );
define( 'TWAIC_PLUGIN', __FILE__ );
define( 'TWAIC_PLUGIN_BASENAME', plugin_basename( TWAIC_PLUGIN ) );

require_once('twaic-admin-settings.php');
require_once('twaic-front-view.php');
