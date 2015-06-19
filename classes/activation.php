<?php


class YT_Liked_Videos_Activation {
	
	/**
	*  	flush rewrite rules 
	*/
	public function __construct() {
		if ( !get_option( 'youtube_liked_videos_flush_permalinks' ) ) {
			return;
		}	
		
		flush_rewrite_rules();
		delete_option( 'youtube_liked_videos_flush_permalinks' );
	}

	public static function activate() {
		wp_schedule_event( current_time( 'timestamp' ), '10min', 'check_for_liked_videos');
		update_option( 'youtube_liked_videos_flush_permalinks', true , true);

	}
	
	public static function deactivate() {

	}

}

/* Add Activation Hook */
register_activation_hook( YT_LIKED_FILE , array( 'YT_Liked_Videos_Activation' , 'activate' ) );
register_deactivation_hook( YT_LIKED_FILE , array( 'YT_Liked_Videos_Activation' , 'deactivate' ) );
new YT_Liked_Videos_Activation;

