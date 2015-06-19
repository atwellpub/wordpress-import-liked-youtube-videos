<?php



class Youtube_Liked_Videos_Connect {

	static $client;

	/**
	 * Setup the Google Client
	 * @return Google_Client
	 */
	public static function get_client() {
		include_once YT_LIKED_PATH .  'assets/libraries/google-api-php-client-master/src/Google/autoload.php';

		$settings = YT_Liked_Videos_Settings::get_settings();

		self::$client = new Google_Client();
		self::$client->setApplicationName("Google Youtube PHP Starter Application");
		self::$client->setScopes(array(
			'https://www.googleapis.com/auth/youtube',
			'https://www.googleapis.com/auth/youtube.readonly',
		));
		self::$client->setClientId($settings['client_id']);
		self::$client->setClientSecret($settings['client_secret']);
		self::$client->setRedirectUri($settings['redirect_uri']);
		self::$client->setAccessType('offline');
		self::$client->setApprovalPrompt('force');

		/* check if access token exists and set it or reset it if expired */
	   Youtube_Liked_Videos_Connect::check_token();

	}


	/**
	 * Get oauth URL
	 */
	public static function get_oauth_url() {
		Youtube_Liked_Videos_Connect::get_client();
		return self::$client->createAuthUrl();
	}


	/**
	 * Get access token from oauth code
	 */
	 public static function get_access_token( $code ) {
		 Youtube_Liked_Videos_Connect::get_client();

		 /* get access tokens from auth code */
		 return self::$client->authenticate( $code );

	 }

	/**
	 * Checks if access token is expired and renews if neccecary
	 * @param self::$client
	 * @return mixed
	 */
	public static function check_token() {
		$settings = YT_Liked_Videos_Settings::get_settings();

		if (empty($settings['access_token_json'])) {
			return;
		}

		self::$client->setAccessToken( $settings['access_token_json'] );

		if(self::$client->isAccessTokenExpired()) {
			self::$client->refreshToken( $settings['refresh_token'] );
			$new_token = self::$client->getAccessToken();
			self::$client->setAccessToken( $new_token );
			$settings = YT_Liked_Videos_Settings::get_settings();
			$settings['access_token'] = $new_token;
			YT_Liked_Videos_Settings::update_settings($settings);
		}

	}


	/**
	 * Get information given calendar id
	 */
	public static function get_liked_videos(  ) {

		Youtube_Liked_Videos_Connect::get_client();

		$service = new Google_Service_Youtube( self::$client );
		$videos = $service->videos->listVideos( 'contentDetails,player,snippet,id' , array('myRating'=>'like' ) );
		return $videos['items'];
	}


}

new Youtube_Liked_Videos_Connect;


