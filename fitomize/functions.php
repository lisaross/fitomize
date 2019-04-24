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
 * Enqueue child theme styles.
 *
 * Enqueues the child theme styles including version number (so it updates cache as theme changes).
 *
 * @since 0.1.0
 */
function fitomize_enqueue_styles() {
	$parent_style = 'parent-style';
	wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css', array(), wp_get_theme()->get( 'Version' ) );

	// register webpack stylesheet with theme.
	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/css/build/main.min.css', array( $parent_style ), wp_get_theme()->get( 'Version' ) );
}
add_action( 'wp_enqueue_scripts', 'fitomize_enqueue_styles' );

/**
 * Enqueue child theme scripts.
 *
 * Enqueues the child theme scripts including version number (so it updates cache as theme changes).
 *
 * @since 0.1.0
 */
function fitomize_enqueue_scripts() {
	wp_enqueue_script( 'child-scripts', get_stylesheet_directory_uri() . '/js/build/app.min.js', array( 'jquery' ), wp_get_theme()->get( 'Version' ), true );
}
add_action( 'wp_enqueue_scripts', 'fitomize_enqueue_scripts' );

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
 * Load jQuery from Google API instead of local
 *
 * @since 0.1.0
 */
function fit_cdn_jquery() {
	if ( ! is_admin() ) {
		// comment out the next two lines to load the local copy of jQuery.
		wp_deregister_script( 'jquery' );
		wp_register_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js', false, '1.12.4' );
		wp_enqueue_script( 'jquery' );
	}
}
add_action( 'init', 'fit_cdn_jquery' );

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
function fit_add_tags_categories() {
	register_taxonomy_for_object_type( 'category', 'gear' );
	register_taxonomy_for_object_type( 'post_tag', 'gear' );
}
add_action( 'init', 'fit_add_tags_categories' );

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
