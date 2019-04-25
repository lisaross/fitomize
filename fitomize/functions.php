<?php
/**
 * Various functions, filters, and actions used by the child theme.
 *
 * @package    Fitomize Theme
 * @subpackage Includes
 * @since      0.1.0
 * @author     Lisa Ross
 * @copyright  Copyright (c) 2019, Fitomize
 * @link       https://dev.fitomize.ca
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Cleanup wp head
 *
 * @since 0.1.0
 */
remove_action( 'wp_head', 'rsd_link' ); // remove really simple discovery link.
remove_action( 'wp_head', 'wp_generator' ); // remove WordPress version.
remove_action( 'wp_head', 'feed_links', 2 ); // remove rss feed links (make sure you add them in yourself if youre using feedblitz or an rss service).
remove_action( 'wp_head', 'feed_links_extra', 3 ); // removes all extra rss feed links.
remove_action( 'wp_head', 'index_rel_link' ); // remove link to index page.
remove_action( 'wp_head', 'wlwmanifest_link' ); // remove wlwmanifest.xml (needed to support windows live writer).
remove_action( 'wp_head', 'start_post_rel_link', 10, 0 ); // remove random post link.
remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 ); // remove parent post link.
remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 ); // remove the next and previous post links.
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );

/**
 * Theme setup
 */
add_action(
	'after_setup_theme',
	function () {
		/**
		 * Enable features from Soil when plugin is activated
		 *
		 * @link https://roots.io/plugins/soil/
		 */
		add_theme_support( 'soil-clean-up' );
		// add_theme_support( 'soil-disable-rest-api' ); !!
		// add_theme_support( 'soil-disable-asset-versioning' ); !!
		add_theme_support( 'soil-disable-trackbacks' );
		add_theme_support( 'soil-google-analytics', 'UA-XXXXX-Y' );
		add_theme_support( 'soil-jquery-cdn' );
		add_theme_support( 'soil-js-to-footer' );
		add_theme_support( 'soil-nav-walker' );
		add_theme_support( 'soil-nice-search' );
		add_theme_support( 'soil-relative-urls' );
		/**
		 * Enable plugins to manage the document title
		 *
		 * @link https://developer.wordpress.org/reference/functions/add_theme_support/#title-tag
		 */
		add_theme_support( 'title-tag' );
		/**
		 * Register navigation menus
		 *
		 * @link https://developer.wordpress.org/reference/functions/register_nav_menus/
		 */
		register_nav_menus(
			[
				'primary_navigation' => __( 'Primary Navigation', 'mini' ),
			]
		);
		/**
		 * Enable post thumbnails
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );
		/**
		 * Enable HTML5 markup support
		 *
		 * @link https://developer.wordpress.org/reference/functions/add_theme_support/#html5
		 */
		add_theme_support( 'html5', [ 'caption', 'comment-form', 'comment-list', 'gallery', 'search-form' ] );
		/**
		 * Enable selective refresh for widgets in customizer
		 *
		 * @link https://developer.wordpress.org/themes/advanced-topics/customizer-api/#theme-support-in-sidebars
		 */
		add_theme_support( 'customize-selective-refresh-widgets' );
	},
	20
);

/**
 * Enqueue child theme styles.
 *
 * Enqueues the child theme styles including version number (so it updates cache as theme changes).
 *
 * @since 0.1.0
 */
function fit_enqueue_styles() {
	$parent_style = 'parent-style';
	wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css', array(), wp_get_theme()->get( 'Version' ) );

	// register webpack stylesheet with theme.
	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/css/build/app.min.css', array( $parent_style ), wp_get_theme()->get( 'Version' ) );
}
add_action( 'wp_enqueue_scripts', 'fit_enqueue_styles' );

/**
 * Enqueue child theme scripts.
 *
 * Enqueues the child theme scripts including version number (so it updates cache as theme changes).
 *
 * @since 0.1.0
 */
function fit_enqueue_scripts() {
	wp_enqueue_script( 'child-scripts', get_stylesheet_directory_uri() . '/js/build/app.min.js', array( 'jquery' ), wp_get_theme()->get( 'Version' ), true );
}
add_action( 'wp_enqueue_scripts', 'fit_enqueue_scripts' );

/**
 * This fixes the WordPress rest-api so we can just lookup pages by their full
 * path (not just their name). This allows us to use React Router.
 *
 * @param mixed $data pull in data from api.
 * @return WP_Error|WP_REST_Response
 */
function get_post_for_url( $data ) {
	$post_id    = url_to_postid( $data['url'] );
	$post_type  = get_post_type( $post_id );
	$controller = new WP_REST_Posts_Controller( $post_type );
	$request    = new WP_REST_Request( 'GET', "/wp/v2/{$post_type}s/{$post_id}" );
	$request->set_url_params( array( 'id' => $post_id ) );
	return $controller->get_item( $request );
}
add_action(
	'rest_api_init',
	function () {
		$namespace = 'fitomize/v1';
		register_rest_route(
			$namespace,
			'/path/(?P<url>.*?)',
			array(
				'methods'  => 'GET',
				'callback' => 'get_post_for_url',
			)
		);
	}
);

/**
 * Disable the emoji's
 *
 * @since 0.1.0
 */
function fit_disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'tiny_mce_plugins', 'fit_disable_emojis_tinymce' );
}
add_action( 'init', 'fit_disable_emojis' );

/**
 * Filter function used to remove the tinymce emoji plugin.
 *
 * @since 0.1.0
 *
 * @param    array $plugins pull list of plugins to pull emojis.
 * @return   array             Difference betwen the two arrays
 */
function fit_disable_emojis_tinymce( $plugins ) {
	if ( is_array( $plugins ) ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	} else {
		return array();
	}
}

/**
 * Remove dashicons for non-admin (front-end)
 *
 * @since 0.1.0
 */
function fit_dequeue_dashicon() {
	if ( current_user_can( 'update_core' ) ) {
		return;
	}
	wp_deregister_style( 'dashicons' );
}
	add_action( 'wp_enqueue_scripts', 'fit_dequeue_dashicon' );

/**
 * Create products post type
 *
 * @since 0.1.0
 */
function fit_post_type_gear() {

	$supports = array(
		'title', // post title.
		'editor', // post content.
		'author', // post author.
		'thumbnail', // featured images.
		'excerpt', // post excerpt.
		'custom-fields', // custom fields.
		'comments', // post comments.
		'revisions', // post revisions.
		'post-formats', // post formats.
	);

	$labels = array(
		'name'           => _x( 'Gear', 'plural' ),
		'singular_name'  => _x( 'Gear', 'singular' ),
		'menu_name'      => _x( 'Gear', 'admin menu' ),
		'name_admin_bar' => _x( 'Gear', 'admin bar' ),
		'add_new'        => _x( 'Add New', 'add new' ),
		'add_new_item'   => __( 'Add New Gear' ),
		'new_item'       => __( 'New Gear' ),
		'edit_item'      => __( 'Edit Gear' ),
		'view_item'      => __( 'View Gear' ),
		'all_items'      => __( 'All Gear' ),
		'search_items'   => __( 'Search Gear' ),
		'not_found'      => __( 'No gear found.' ),
	);

	$args = array(
		'supports'           => $supports,
		'labels'             => $labels,
		'public'             => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'gear' ),
		'has_archive'        => true,
		'hierarchical'       => false,
		'publicly_queryable' => true,
		'menu_icon'          => 'dashicons-cart',
		'taxonomies'         => array( 'category', 'post-tag' ),
	);
	register_post_type( 'gear', $args );
}
add_action( 'init', 'fit_post_type_gear' );

/**
 * Register taxonomies (tags AND categories) for gear
 *
 * @since 0.1.0
 */
function fit_add_gear_tags_categories() {
	register_taxonomy_for_object_type( 'category', 'gear' );
	register_taxonomy_for_object_type( 'post_tag', 'gear' );
}
add_action( 'init', 'fit_add_gear_tags_categories' );

/*Custom Gear Post type end*/

/**
 * Create videos post type
 *
 * @since 0.1.0
 */
function fit_post_type_video() {

	$supports = array(
		'title', // post title.
		'editor', // post content.
		'author', // post author.
		'thumbnail', // featured images.
		'excerpt', // post excerpt.
		'custom-fields', // custom fields.
		'comments', // post comments.
		'revisions', // post revisions.
		'post-formats', // post formats.
	);

	$labels = array(
		'name'           => _x( 'Videos', 'plural' ),
		'singular_name'  => _x( 'Video', 'singular' ),
		'menu_name'      => _x( 'Videos', 'admin menu' ),
		'name_admin_bar' => _x( 'Video', 'admin bar' ),
		'add_new'        => _x( 'Add New', 'add new' ),
		'add_new_item'   => __( 'Add New Video' ),
		'new_item'       => __( 'New Video' ),
		'edit_item'      => __( 'Edit Video' ),
		'view_item'      => __( 'View Video' ),
		'all_items'      => __( 'All Videos' ),
		'search_items'   => __( 'Search Videos' ),
		'not_found'      => __( 'No videos found.' ),
	);

	$args = array(
		'supports'           => $supports,
		'labels'             => $labels,
		'public'             => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'video' ),
		'has_archive'        => true,
		'hierarchical'       => false,
		'publicly_queryable' => true,
		'menu_icon'          => 'dashicons-format-video',
		'taxonomies'         => array( 'category', 'post-tag' ),
	);
	register_post_type( 'Video', $args );
}
add_action( 'init', 'fit_post_type_video' );

/**
 * Register taxonomies (tags AND categories) for gear
 *
 * @since 0.1.0
 */
function fit_add_video_tags_categories() {
	register_taxonomy_for_object_type( 'category', 'video' );
	register_taxonomy_for_object_type( 'post_tag', 'video' );
}
add_action( 'init', 'fit_add_video_tags_categories' );

/*Custom Post type end*/

/**
 * Set up more non-standard post formats
 *
 * @since 0.1.0
 */
function fit_add_post_formats() {
	add_theme_support( 'post-formats', array( 'gallery', 'quote', 'video', 'aside', 'image', 'link', 'chat', 'audio', 'status' ) );
}

add_action( 'after_setup_theme', 'fit_add_post_formats', 20 );

/**
 * Hide the divi projects post type
 *
 * @since 0.1.0
 * @param mixed[] $args arguments for post type.
 */
function fit_et_project_posttype_args( $args ) {
	return array_merge(
		$args,
		array(
			'public'              => false,
			'exclude_from_search' => false,
			'publicly_queryable'  => false,
			'show_in_nav_menus'   => false,
			'show_ui'             => false,
		)
	);
}
add_filter( 'et_project_posttype_args', 'fit_et_project_posttype_args', 10, 1 );

/**
 * Add page slug to body class
 *
 * @param mixed $classes get the classes.
 * @since 0.1.0
 */
function fit_add_slug_to_body_class( $classes ) {
	global $post;
	if ( is_home() ) {
		$key = array_search( 'blog', $classes, true );
		if ( $key > -1 ) {
			unset( $classes[ $key ] );
		}
	} elseif ( is_page() ) {
		$classes[] = sanitize_html_class( $post->post_name );
	} elseif ( is_singular() ) {
		$classes[] = sanitize_html_class( $post->post_name );
	}
	return $classes;
}
add_filter( 'body_class', 'fit_add_slug_to_body_class' );

/**
 * Don't load google map modules
 *
 * @param mixed $query get the query.
 * @since 0.1.0
 */
function fit_loop_start( $query ) {
	if ( fit_map_modules_in_excerpts( $query ) ) {
		add_filter( 'et_pb_enqueue_google_maps_script', 'fit_return_false' );
	}
}

/**
 * Re-enable google maps
 *
 * @param mixed $query get the query.
 * @since 0.1.0
 */
function fit_loop_end( $query ) {
	if ( fit_map_modules_in_excerpts( $query ) ) {
		remove_filter( 'et_pb_enqueue_google_maps_script', 'fit_return_false' );
	}
}

/**
 * Map modules in excerpts
 *
 * @param mixed $query get the query.
 * @since 0.1.0
 */
function fit_map_modules_in_excerpts( $query ) {

	// Don't affect admin.
	if ( is_admin() ) {
		return false; }

	// Don't affect visual builder.
	if ( ! function_exists( 'et_core_is_fb_enabled' ) || et_core_is_fb_enabled() ) {
		return false; }

	// Don't affect single posts.
	if ( is_singular() ) {
		return false; }

	// Don't affect secondary queries.
	if ( ! $query->is_main_query() ) {
		return false; }

	// Don't affect Divi > Theme Options > General > Blog Style Mode, which shows full post content in loop.
	if ( ! function_exists( 'et_get_option' ) || et_get_option( 'divi_blog_style', 'false' ) === 'on' ) {
		return false; }

	return true;
}
/**
 * Finish up with the google maps script
 *
 * @since 0.1.0
 */
function fit_return_false() {
	return false; }

add_action( 'loop_start', 'fit_loop_start' );
add_action( 'loop_end', 'fit_loop_end', 100 );
