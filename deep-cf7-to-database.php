<?php

/*
Plugin Name: CF7 to Database 
Plugin URI: http://plugins.deepdemo.us/
Description: Contact Form 7 To Database. Simple but flexible.
Author: DeepDev
Author URI: http://plugins.deepdemo.us/
Text Domain: cf7-to-database
Domain Path: /languages/
Version: 1.0
*/

/* Install and default settings */

if ( ! defined( 'WP_DEEP_CF7_PLUGIN_BASENAME' ) )
  define( 'WP_DEEP_CF7_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

if ( ! defined( 'WP_DEEP_CF7_PLUGIN_DIR' ) )
  define( 'WP_DEEP_CF7_PLUGIN_DIR', untrailingslashit( dirname( __FILE__ ) ) );

if ( ! defined( 'WP_DEEP_CF7_PLUGIN_URL' ) )
  define( 'WP_DEEP_CF7_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );

if ( ! defined( 'WP_DEEP_CF7_ADMIN_READ_CAPABILITY' ) )
  define( 'WP_DEEP_CF7_ADMIN_READ_CAPABILITY', 'edit_posts' );

if ( ! defined( 'WP_DEEP_CF7_ADMIN_READ_WRITE_CAPABILITY' ) )
  define( 'WP_DEEP_CF7_ADMIN_READ_WRITE_CAPABILITY', 'publish_pages' );

require_once( WP_DEEP_CF7_PLUGIN_DIR . '/includes/admin.php' );