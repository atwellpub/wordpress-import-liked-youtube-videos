<?php

/**
*  Loads admin sub-menus and performs misc menu related functions
*/
class YT_Liked_Videos_Menus {

	/**
	*  Initializes class
	*/
	public function __construct() {
		self::load_hooks();
	}

	/**
	*  Loads hooks and filters
	*/
	public static function load_hooks() {
		add_action('admin_menu', array( __CLASS__ , 'add_sub_menus' ) );
	}

	/**
	*  Adds sub-menus to 'Calls to Action'
	*/
	public static function add_sub_menus() {
		if ( !current_user_can('manage_options')) {
			return;
		}

		add_submenu_page('edit.php?post_type=liked-videos', __( 'Settings' , 'youtube-liked-videos' ) , __( 'Settings' , 'youtube-liked-videos') , 'manage_options', 'liked_video_settings', array( 'YT_Liked_Videos_Settings' , 'display_settings' ) );

	}

}

/**
*  Loads Class Pre-Init
*/
 new YT_Liked_Videos_Menus();

