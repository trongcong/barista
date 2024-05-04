<?php
/**
 * Created by NTC
 */
define( 'FS_RELEASE_VERSION', '1.4' );
add_action( 'wp_enqueue_scripts', 'lt_enqueue_scripts' );
function lt_enqueue_scripts() {
	//map
	wp_register_script( 'fs-map-listing-script', get_stylesheet_directory_uri() . '/inc/assets/js/fs-map-listing.min.js', array(
		'jquery',
	), WP_DEBUG ? rand() : FS_RELEASE_VERSION, true );

	wp_enqueue_style( 'lt-style', get_stylesheet_directory_uri() . '/inc/assets/css/lt-main.min.css', array(), WP_DEBUG ? rand() : FS_RELEASE_VERSION );
	wp_enqueue_script( 'lt-script', get_stylesheet_directory_uri() . '/inc/assets/js/lt-main.min.js', array(
		'jquery',
		'fs-map-listing-script'
	), WP_DEBUG ? rand() : FS_RELEASE_VERSION, true );

	wp_localize_script( 'fs-map-listing-script', 'ajax_data', [
		'admin_logged'        => in_array( 'administrator', wp_get_current_user()->roles ) ? 'yes' : 'no',
		'ajax_url'            => admin_url( 'admin-ajax.php' ),
		'tpd_uri'             => get_template_directory_uri(),
		'site_url'            => site_url(),
		'rest_url'            => get_rest_url(),
		'_ajax_nonce'         => wp_create_nonce( "_security" ),
		'post_id'             => get_the_ID(),
		'barista_profile_url' => get_barista_profile_link(),
		'barista_data'        => get_map_data_for_barista(),
	] );
}

add_action( 'admin_enqueue_scripts', 'lt_admin_enqueue_scripts' );
function lt_admin_enqueue_scripts() {
	wp_enqueue_style( 'lt-style', get_stylesheet_directory_uri() . '/inc/assets/css/fs-main.admin.min.css', array(), WP_DEBUG ? rand() : FS_RELEASE_VERSION );
	wp_enqueue_script( 'lt-script', get_stylesheet_directory_uri() . '/inc/assets/js/fs-main.admin.min.js', array(
		'jquery',
	), WP_DEBUG ? rand() : FS_RELEASE_VERSION, true );

	wp_localize_script( 'lt-script', 'ajax_data', [
		'admin_logged'        => in_array( 'administrator', wp_get_current_user()->roles ) ? 'yes' : 'no',
		'ajax_url'            => admin_url( 'admin-ajax.php' ),
		'tpd_uri'             => get_template_directory_uri(),
		'site_url'            => site_url(),
		'rest_url'            => get_rest_url(),
		'_ajax_nonce'         => wp_create_nonce( "_security" ),
		'post_id'             => get_the_ID(),
		'barista_profile_url' => get_barista_profile_link(),
	] );
}
