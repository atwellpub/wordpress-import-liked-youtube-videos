<?php

class Youtube_Liked_Cron {
	static $history;
	static $settings;

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
			'interval' => 60 * 10,
			'display' => __('Every 10 Min')
		);

		return $schedules;
	}

	/**
	*
	*/
	public static function debug_cron() {
		if ( isset($_GET['debugyt']) ) {
			do_action('check_for_liked_videos');
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

        /* required to set featured image */
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

		$videos = Youtube_Liked_Videos_Connect::get_liked_videos();

		self::$settings = YT_Liked_Videos_Settings::get_settings();
		self::$history = get_option( 'yt_liked_videos' , array() );

		foreach ($videos as $key => $video) {
			$video_id = $video['id'];
			$link = 'https://www.youtube.com/watch?v=' . $video_id;
			$data = $video['modelData'];
			$data['id'] = $video_id;

			/* check if already processed */
			if ( self::check_history( $video_id ) ) {
				continue;
			}


			if (!isset($data['player']['embedHtml']) || empty($data['player']['embedHtml'])) {
				errorLog('YTLV: Embedding disabled for video '.$video_id);
				continue;
			}

			/* prepare post body and title */
			$postbody = self::replace_tokens(self::$settings['postbody'] , $data );
			$title = self::replace_tokens(self::$settings['title'] , $data );

			/* create post */
			global $allowedposttags;
			$allowedposttags['div'] = array('align' => array (), 'class' => array (), 'id' => array (), 'dir' => array (), 'lang' => array(), 'style' => array (), 'xml:lang' => array() );
			$allowedposttags['iframe'] = array('src' => array () );
			$post_id = wp_insert_post(
				array(
					'comment_status'	=>	'closed',
					'ping_status'		=>	'closed',
					'post_title'		=>	$title,
					'post_content'		=>	$postbody,
					'post_status'		=>	'publish',
					'post_type'		=>	'liked-videos',
					'post_author'		=>	self::$settings['post_author'],
					'filter' => true
				)
			);

            /* attach youtube thumbnail as featured image */
            if (isset($data['snippet']['thumbnails']['high']['url'])) {
                $attachment_id = media_sideload_image($data['snippet']['thumbnails']['high']['url'], $post_id , 'testing' , 'id');
                update_post_meta( $post_id, '_thumbnail_id', $attachment_id );
            }

			kses_init_filters();

			self::$history[] = $video_id;
		}

		self::update_history();
	}

	/**
	*  replace tokens with content
	*/
	public static function replace_tokens( $string , $data ) {

		$string = str_replace( '{{video-title}}' , $data['snippet']['title'], $string );
		$string = str_replace( '{{video-id}}' , $data['id'], $string );
		$string = str_replace( '{{video-description}}' , $data['snippet']['description'], $string );
		$string = str_replace( '{{thumbnail-default}}' , $data['snippet']['thumbnails']['default']['url'], $string );
		$string = str_replace( '{{thumbnail-medium}}' , $data['snippet']['thumbnails']['medium']['url'], $string );
		$string = str_replace( '{{thumbnail-high}}' , $data['snippet']['thumbnails']['high']['url'], $string );
		$string = str_replace( '{{iframe-embed}}' , $data['player']['embedHtml'], $string );

		return $string;
	}

	/**
	*  check to see if post already exist
	*/
	public static function check_history( $video_id ) {

		if (in_array($video_id , self::$history)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	*  updates history
	*/
	public static function update_history(  ) {
		update_option( 'yt_liked_videos', self::$history, false );
	}
}

new Youtube_Liked_Cron;
