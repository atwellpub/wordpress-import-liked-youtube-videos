<?php

/**
 * Creates Global Settings
 *
*/


class YT_Liked_Videos_Settings {

	static $core_settings;
	static $active_tab;

	/**
	*	Initializes class
	*/
	public function __construct() {
		self::add_hooks();
	}

	/**
	*	Loads hooks and filters
	*/
	public static function add_hooks() {
		/* save settings */
		add_action( 'admin_init'  , array( __CLASS__ , 'save_settings' ) );
		
		add_action( 'admin_enqueue_scripts' , array( __CLASS__ , 'enqueue_scripts' ) );
	
		/* store authentication */
		add_action( 'admin_init', array( __CLASS__ , 'store_access_tokens' ) );
	}

	/**
	*	Load CSS & JS
	*/
	public static function enqueue_scripts() {
		$screen = get_current_screen();

		if ( ( isset($screen) && $screen->base != 'liked-videos_page_liked_video_settings' ) ){
			return;
		}

		wp_enqueue_style('liked-video-settings', YT_LIKED_URLPATH . 'assets/css/admin/settings.css');
	}

	

	/**
	*	Display sidebar
	*/
	public static function display_sidebar() {
		?>

		<div class='sidebar'>
			
		</div>
		<?php
	}

	/**
	*	Display global settings
	*/
	public static function display_settings()	{
		global $wpdb;

		$settings = self::get_settings();
		$history = get_option( 'yt_liked_videos' , array() );
		$history_imploded = implode( "\r\n" , $history );
		$users = get_users( 'blog_id=1&orderby=nicename&role=administrator' );
		
		?>

		<div class="clear" id="php-sql-wp-cta-version">
		<form action='edit.php?post_type=liked-videos&page=liked_video_settings' method='POST'>
		<input type='hidden' name='youtube_liked_videos_authorize' value='true'>
		<?php
		  if (empty($settings['access_token'])) {
            ?>               
                <h3><?php _e("Youtube Authentication", 'youtube-liked-videos'); ?></h3>
                <div id='installation-docs'>
                    <ol>
                        <li><?php echo sprintf(  __( 'Go to the %sDeveloper\'s Console%s.' , 'youtube-liked-videos') , '<a href="https://console.developers.google.com/project" target="_blank">' , '</a>'); ?></li>
                        <li><?php _e( 'Select a project, or create a new one.' , 'youtube-liked-videos' ); ?></li>
                        <li><?php _e( 'In the sidebar on the left, expand APIs & auth. Next, click APIs. .' , 'youtube-liked-videos' ); ?></li>
                        <li><?php _e( 'In the sidebar on the left, select Credentials.' , 'youtube-liked-videos' ); ?></li>
                        <li><?php echo sprintf( __(  'Click create new client id , choose "Web Application" and after setting up your consent screen, set your \'Authorized redirect URIs\' to:<br> %s ' , 'youtube-liked-videos' )  , $settings['redirect_uri'] ); ?></li>
                    </ol>


                </div>
                <table class="form-table">


                <tr>
                    <th><label for="youtube_client_id"><?php _e( 'Client ID' , 'youtube-liked-videos'); ?></label></th>
                    <td>
                        <input type="text" name="client_id" id="client_id" value="<?php echo $settings['client_id']; ?>" class="regular-text" /><br />
                        <span class="description"><?php _e("Please enter your youtube_client_id."); ?></span>
                    </td>
                </tr>
                <tr>
                    <th><label for="youtube_client_secret"><?php _e('Client secret' , 'youtube-liked-videos'); ?></label></th>
                    <td>
                        <input type="text" name="client_secret" id="client_secret" value="<?php echo $settings['client_secret']; ?>" class="regular-text" /><br />
                        <span class="description"><?php _e("Please enter your youtube_client_secret.", 'youtube-liked-videos'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th><?php _e( 'Authorize' , 'youtube-liked-videos' ); ?></th>
                    <td>
                        <input type="submit" name="authorize" id="authorize" value="<?php _e( 'Authorize' , 'youtube-liked-videos' ); ?>" class="button button-primary primary" /><br />

                    </td>
                </tr>
                </table>
            <?php
            } else {
            ?>              
                <h3><?php _e("Youtube Authentication", 'youtube-liked-videos'); ?></h3>
                <table class="form-table">
                <tr>
                    <th><?php _e( 'De-Authorize' , 'youtube-liked-videos' ); ?></th>
                    <td>
                        <input type="submit" name="deauthorize" id="deauthorize" value="<?php _e( 'Deauthorize' , 'youtube-liked-videos' ); ?>" class="button button-primary primary" /><br />

                    </td>
                </tr>
                </table>
            <?php
            }
            ?>
             <table class="form-table">
                <tr>
                    <th><?php _e("Publish As:", 'youtube-liked-videos'); ?></th>
                    <td>
						<select name='post_author' id='post_author'>
                        <?php
						
						foreach ( $users as $user ) {
							echo '<option value="'.$user->ID.'" '.( $settings['post_author'] == $user->ID ? 'selected="selected"' : '' ) .'>'.$user->data->display_name.'</option>';
						}
						?>
						</select>
                    </td>
                </tr>
             </table>
			 <h3><?php _e("Templating:", 'youtube-liked-videos'); ?></h3>
             <table class="form-table">
                <tr>
                    <th><?php _e( 'Title' , 'youtube-liked-videos' ); ?></th>
                    <td>
                        <input type="text" name="title" value="<?php echo $settings['title']; ?>" />
                    </td>
                </tr>
                <tr>
                    <th><?php _e( 'Body' , 'youtube-liked-videos' ); ?></th>
                    <td>
                        <textarea type="text" name="postbody"/><?php echo $settings['postbody']; ?></textarea>
						<div class='available-tokens'>
						{{title}} {{iframe-embed}} {{description}}  {{thumbnail-default}} {{thumbnail-medium}} {{thumbnail-high}} {{link}} {{video-id}}
						</div>
                    </td>
                </tr>
                <tr>
                    <th><?php _e( 'Save Settings' , 'youtube-liked-videos' ); ?></th>
                    <td>
                       <input type="submit" name="save" id="save" value="<?php _e('Save', 'youtube-liked-videos' ); ?>" class="button button-secondary">
                    </td>
                </tr>
             </table>
			 <h3><?php _e("History:", 'youtube-liked-videos'); ?></h3>
             <table class="form-table">
                <tr>
                    <th><?php _e( 'Record' , 'youtube-liked-videos' ); ?></th>
                    <td>
                        <textarea type="text" name="history" id='history' /><?php echo $history_imploded; ?></textarea>						
                    </td>
                </tr>
                <tr>
                    <th><?php _e( 'Modify' , 'youtube-liked-videos' ); ?></th>
                    <td>
                       <input type="submit" name="modify_history" id="modify_history" value="<?php _e('Modify History', 'youtube-liked-videos' ); ?>" class="button button-secondary">
                    </td>
                </tr>
             </table>
		</form>
	<?php
	}


	/**
	*	Renders supporting JS
	*/
	public static function inline_js() {

		?>
		<script type='text/javascript'>
			jQuery(document).ready(function($) {

				

			});
		</script>
		<?php

	}

	/**
	*	Listens for POST & saves settings changes
	*/
	public static function save_settings() {
		
		if ( !isset($_POST['youtube_liked_videos_authorize'])) {
			return;
		}
		
		$settings = self::get_settings();

		
		if (isset($_POST['client_id'])) {
			$settings['client_id'] = $_POST['client_id'];
		}
		if (isset($_POST['client_secret'])) {
			$settings['client_secret'] =  $_POST['client_secret'];
		}
		if (isset($_POST['title'])) {
			$settings['title'] =  stripslashes($_POST['title']);
		}
		if (isset($_POST['postbody'])) {
			$settings['postbody'] =  stripslashes($_POST['postbody']);
		}
		if (isset($_POST['post_author'])) {
			$settings['post_author'] =  stripslashes($_POST['post_author']);
		}
		
		self::update_settings($settings);

		/* return if fields are empty */
		if ( isset($_POST['authorize']) ) {
			self::start_authorization();
		}
		 /* return if fields are empty */
		if ( isset($_POST['deauthorize']) ) {
			self::start_deauthorization();
		}
		
		/* update history if user is updating history */
		if ( $_POST['modify_history'] ) {
			$history = explode( "\r\n" , stripslashes($_POST['history']));
			update_option( 'yt_liked_videos', $history, false );
		}
	}

	/**
	*  Get Saved API Key
	*/
	public static function get_settings() {
		$settings = get_option( 'youtube-liked-videos');	
		$settings['redirect_uri'] = trim(admin_url('edit.php?post_type=liked-videos&page=liked_video_settings&yt-get-tokens=true'));
		$settings['access_token_json'] = (!empty($settings['access_token_json'])) ? trim( $settings['access_token_json']) : '';
        $settings['access_token'] = (!empty($settings['access_token'])) ? trim( $settings['access_token']) : '';
        $settings['refresh'] = (!empty($settings['refresh_token'])) ? trim( $settings['refresh_token']) : '';
        $settings['client_id'] = (!empty($settings['client_id'])) ? trim( $settings['client_id']) : '';
        $settings['client_secret'] = (!empty($settings['client_secret'])) ? trim( $settings['client_secret']) : '';
        $settings['post_author'] = (!empty($settings['post_author'])) ? trim( $settings['post_author']) : '';
		
		
        $settings['title'] = (!empty($settings['title'])) ? trim( $settings['title']) : '{{video-title}}';
        $settings['postbody'] = (!empty($settings['postbody'])) ? trim( $settings['postbody']) : '<div class="liked-video-embed">{{iframe-embed}}</div><div class="liked-video-description"><b>Uploader Comment:</b><br><blockquote>{{video-description}}</blockquote></div>';
	
		return $settings;
	}
	/**
	*  Get Saved API Key
	*/
	public static function update_settings( $settings ) {
		update_option( 'youtube-liked-videos' , $settings );		

	}
	
	/**
	 * redirect to google calendar oauth
	 */
	public static function start_authorization() {
		include YT_LIKED_PATH .  'assets/libraries/google-api-php-client-master/src/Google/autoload.php';
		$authUrl = Youtube_Liked_Videos_Connect::get_oauth_url();
		header('Location:'.$authUrl);
		exit;

	}

	/**
	 * redirect to google calendar oauth
	 */
	public static function start_deauthorization() {

		
		$settings = self::get_settings();
		$settings = $_POST['youtube-liked-videos'];
		unset($settings['access_token_json']);
		unset($settings['access_token']);
		unset($settings['refresh_token']);
		self::update_settings($settings);

	}

	/**
	 * Coverts google oauth code to access token
	 */
	public static function store_access_tokens() {
		if (!isset($_GET['yt-get-tokens']) ) {
			return;
		}


		$response = Youtube_Liked_Videos_Connect::get_access_token( $_GET['code'] );
		$token = json_decode( $response , true );

		if (isset($token['error'])) {
			print_r($token);exit;
		}
		
		$settings = self::get_settings();
		$settings['access_token_json'] = $response;
		$settings['access_token'] = $token['access_token'];
		$settings['refresh_token'] = $token['refresh_token'];
		self::update_settings($settings);
	}

}

new YT_Liked_Videos_Settings;



