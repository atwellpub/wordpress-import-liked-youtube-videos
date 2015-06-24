<?php
/*  
Plugin Name: YouTube Liked Video Importer 
Plugin URI: http://www.hudsonatwell.co/
Description: Made to be used on personal blogging sites, this plugin imports & publishes YouTube videos that a user likes. 
Version: 1.0.3
Author: Hudson Atwell
Author URI: http://www.hudsonatwell.co/
Text Domain: youtube-liked-videos
Domain Path: lang
*/


if ( !class_exists( 'YouTube_Liked_Video_Importer' )) {

	class YouTube_Liked_Video_Importer {


		/**
		*  initiates class
		*/
		public function __construct() {

			/* Define constants */
			self::define_constants();

			/* Define hooks and filters */
			self::load_hooks();

			/* Load supportive files */
			self::load_files();

		}

		/**
		*  Loads hooks and filters
		*/
		public static function load_hooks() {

		}



		/**
		*  Defines constants
		*/
		public static function define_constants() {
			define('YT_LIKED_CURRENT_VERSION', '1.0.2' );
			define('YT_LIKED_SLUG' , plugin_basename( dirname(__FILE__) ) );
			define('YT_LIKED_FILE' ,  __FILE__ );
			define('YT_LIKED_URLPATH', plugins_url( ' ', __FILE__ ) );
			define('YT_LIKED_PATH', WP_PLUGIN_DIR.'/'.plugin_basename( dirname(__FILE__) ).'/' );
		}

		/**
		*  Loads supportive files
		*/
		public static function load_files() {
			
			include 'classes/cron.php';
			include 'classes/post-type.php';
			include 'classes/connect.php'; /* required on frontend too for cron */			
			include 'classes/settings.php'; /* required on frontend too for cron */
			
			if (is_admin()) {
				include 'classes/activation.php';
				include 'classes/menus.php';
			}
		}


	}


	new YouTube_Liked_Video_Importer();
}