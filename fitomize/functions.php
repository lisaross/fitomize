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
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css', array(), '0.1.0' );
}
add_action( 'wp_enqueue_scripts', 'fitomize_enqueue_styles' );
