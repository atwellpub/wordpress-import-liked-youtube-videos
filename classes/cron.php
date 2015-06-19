<?php

class Youtube_Liked_Cron {
	

	function __construct() {
		/* add debug handler for cronjob */
		add_action( 'admin_init' , array( __CLASS__  , 'debug_cron' ) ) ;
		
		/* add cronjob */
		add_action( 'check_for_liked_videos', array( __CLASS__ , 'check_for_liked_videos') );
		add_filter( 'cron_schedules', array( __CLASS__ , 'add_new_intervals' ) );
	}

	/**
	*  
	*/
	public static function add_new_intervals($schedules) {
		$schedules['10min'] = array(
			'interval' => 1000 * 10,
			'display' => __('Every 10 Min')
		);	

		return $schedules;
	}
	
	/**
	*  
	*/
	public static function debug_cron() {
		if ( isset($_GET['debugyt']) ) {
			self::check_for_liked_videos();
		}	
	}
	
	/**
	*  
	*/
	public static function check_for_liked_videos() {
		$settings = YT_Liked_Videos_Settings::get_settings();
		
		if ( empty( $settings['access_token'] ) ) {
			return;
		}
		
		$videos = Youtube_Liked_Videos_Connect::get_liked_videos();

		foreach ($videos as $key => $video) {
			$id = $video['id'];
			$data = $video['modelData'];
			$title = $data['snippet']['title'];
			echo $id;
			print_r($data);exit;
			if ( check_history( $id , 'OBJECT', 'liked-video') ) {
				continue;
			}
			
			
			
		}
	}
	
	/**
	*  check to see if post already exist
	*/
	public static function check_history() {

		$history = get_option( 'yt_liked_videos' );
		if (in_array($video_id , $history)) {
			return true;
		} else {
			return false;
		}
	}
}

new Youtube_Liked_Cron;