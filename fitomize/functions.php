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
