<?php
/**
 * Plugin Name: WP-REST-Allow-All-CORS
 * Plugin URI: http://AhmadAwais.com/
 * Description: Allow all cross origin requests to your WordPress site's REST API.
 * Author: mrahmadawais, WPTie
 * Author URI: http://AhmadAwais.com/
 * Version: 1.0.0
 * License: GPL2+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package WPRAC
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the Header Image custom post type.
 */
function sixohthree_init() {
    $labels = array(
        'name' => 'Home Banners',
        'singular_name' => 'Banner',
        'add_new_item' => 'Add Banner',
        'edit_item' => 'Edit Home Banner',
        'new_item' => 'New Home Banner',
        'view_item' => 'View Home Banner',
        'search_items' => 'Search Home Banner',
        'not_found' => 'No Home Banner encontrados',
        'not_found_in_trash' => 'No Home Banner encontrados na lixeira'
    );

    $args = array(
        'labels' => $labels,
        'public' => false,
        'show_ui' => true,
        'supports' => array('thumbnail')
    );

    register_post_type( 'homebanner', $args );
}
add_action( 'init', 'sixohthree_init' );

/**
 * Modify which columns display when the admin views a list of header-image posts.
 */
function sixohthree_homebanner_posts_columns( $posts_columns ) {
    $tmp = array();

    foreach( $posts_columns as $key => $value ) {
        if( $key == 'title' ) {
            $tmp['homebanner'] = 'Home Banner';
        } else {
            $tmp[$key] = $value;
        }
    }

    return $tmp;
}
add_filter( 'manage_homebanner_posts_columns', 'sixohthree_homebanner_posts_columns' );

/**
 * Custom column output when admin is view the header-image post list.
 */
function sixohthree_homebanner_custom_column( $column_name ) {
    global $post;

    if( $column_name == 'homebanner' ) {
        echo "<a href='", get_edit_post_link( $post->ID ), "'>", get_the_post_thumbnail( $post->ID ), "</a>";
    }
}
add_action( 'manage_posts_custom_column', 'sixohthree_homebanner_custom_column' );

/**
 * Make the "Featured Image" metabox front and center when editing a header-image post.
 */
function sixohthree_homebanner_metaboxes( $post ) {
    global $wp_meta_boxes;

    remove_meta_box('postimagediv', 'home-banner', 'side');
    add_meta_box('postimagediv', __('Featured Image'), 'post_thumbnail_meta_box', 'home-banner', 'normal', 'high');
}
add_action( 'add_meta_boxes_header-image', 'sixohthree_homebanner_metaboxes' );

/**
 * Enable thumbnail support in the theme, and set the thumbnail size.
 */
function sixohthree_after_setup() {
    add_theme_support( 'post-thumbnails' );
    set_post_thumbnail_size(150, 100, true);
}
add_action( 'after_setup_theme', 'sixohthree_after_setup' );

// Hook.
add_action( 'rest_api_init', 'wp_rest_allow_all_cors', 15 );

/**
 * Allow all CORS.
 *
 * @since 1.0.0
 */
function wp_rest_allow_all_cors() {
	// Remove the default filter.
	remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );

	// Add a Custom filter.
	add_action( 'send_headers', function() {
		if ( ! did_action('rest_api_init') && $_SERVER['REQUEST_METHOD'] == 'HEAD' ) {
			header( 'Access-Control-Allow-Origin: *' );
			header( 'Access-Control-Expose-Headers: Link' );
			header( 'Access-Control-Allow-Methods: HEAD' );
	
		}
	} );
	add_filter( 'rest_pre_serve_request', function( $value ) {
		header( 'Access-Control-Allow-Origin: *' );
		header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE' );
		header( 'Access-Control-Allow-Credentials: true' );
		return $value;
	});
} // End fucntion wp_rest_allow_all_cors().
