<?php

class YT_Liked_Post_Type {

	static $stats;

	function __construct() {
		self::load_hooks();
	}

	private function load_hooks() {

		add_action('init', array( __CLASS__ , 'register_post_type' ) , 1	);
		add_action('init', array( __CLASS__ , 'register_tag_taxonomy' ) , 1 );
		add_action('init', array( __CLASS__ , 'register_post_status' ) , 1 );

		/* Load Admin Only Hooks */
		if (is_admin()) {

			/* Register Columns */
			add_filter( 'manage_liked-videos_posts_columns' , array( __CLASS__ , 'register_columns') );

			/* Prepare Column Data */
			add_action( "manage_posts_custom_column", array( __CLASS__ , 'prepare_column_data' ) , 10, 2 );

			/* Define Sortable Columns */
			add_filter( 'manage_edit_liked-videos_sortable_columns', array( __CLASS__ , 'define_sortable_columns' ) );

			/* Remove 'tags' & 'categories' from menu */
			add_filter( 'admin_footer' , array( __CLASS__ , 'apply_js' ) );

		}
	}


	/**
	*	Rebuilds permalinks after activation
	*/
	public static function rebuild_permalinks() {

	}

	/**
	*	Registers liked-videos post type
	*/
	public static function register_post_type() {

		if ( post_type_exists( 'liked-videos' ) ) {
			return;
		}

		$path = apply_filters( 'yt_liked_video_slug' , 'liked-videos' );

		$labels = array(
			'name' => __( 'Liked Videos', 'liked-videos' ),
			'singular_name' => __('Liked Videos', 'liked-videos' ),
			'add_new' => __('Add New', 'liked-videos' ),
			'add_new_item' => __('Add New ' , 'liked-videos' ),
			'edit_item' => __('Edit' , 'liked-videos' ),
			'new_item' => __('New ' , 'liked-videos' ),
			'view_item' => __('View ' , 'liked-videos' ),
			'search_items' => __('Search' , 'liked-videos' ),
			'not_found' =>	__('Nothing found' , 'liked-videos' ),
			'not_found_in_trash' => __('Nothing found in Trash' , 'liked-videos' ),
			'parent_item_colon' => ''
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'query_var' => true,
			'menu_icon' => '',
			'rewrite' => array("slug" => $path),
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => 34,
			'show_in_nav_menus'	=> false,
			'supports' => array()
		);

		register_post_type( 'liked-videos' , $args );

	}

	/**
	*	Register Tag Taxonomy
	*/
	public static function register_tag_taxonomy() {

	}

	/**
	*  	Register Columns
	*/
	public static function register_columns( $cols ) {

		return $cols;

	}

	/**
	*  	Prepare Column Data
	*/
	public static function prepare_column_data( $column , $post_id ) {
		global $post;

		if ($post->post_type !='liked-videos') {
			return $column;
		}

		switch ($column) {
		}
	}


	/**
	*	Define Sortable Columns
	*/
	public static function define_sortable_columns($columns) {

		return $columns;

	}

	/**
	*	Registers all post status types related to the liked-videos cpt

	*/
	public static function register_post_status() {

	}

	/**
	*	Adds dropdown support for added post status
	*/
	public static function add_post_status() {
		global $post;

		if($post->post_type != 'liked-videos'){
			return;
		}

	}

	/**
	*	Add admin js that removes menu items
	*/
	public static function apply_js() {
		?>
		<script type='text/javascript'>

		jQuery( document ).ready( function() {
		
		});

		</script>
		<?php
	}

}

new YT_Liked_Post_Type();
