<?php


class YT_Liked_Videos_Activation {

	public static function activate() {
		wp_schedule_event( current_time( 'timestamp' ), '10min', 'check_for_liked_videos');
	}
	
	public static function deactivate() {

	}

}

/* Add Activation Hook */
register_activation_hook( YT_LIKED_FILE , array( 'YT_Liked_Videos_Activation' , 'activate' ) );
register_deactivation_hook( YT_LIKED_FILE , array( 'YT_Liked_Videos_Activation' , 'deactivate' ) );


