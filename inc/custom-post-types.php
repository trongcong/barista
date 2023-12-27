<?php
function fs_cptui_register_post_types() {
    /**
     * Post Type: Jobs.
     */

    register_post_type( "job", [
        "label" => esc_html__( "Jobs", "custom-post-type-ui" ),
        "labels" => [
            "name" => esc_html__( "Jobs", "custom-post-type-ui" ),
            "singular_name" => esc_html__( "Job", "custom-post-type-ui" ),
        ],
        "description" => "",
        "public" => true,
        "publicly_queryable" => true,
        "show_ui" => true,
        "show_in_rest" => true,
        "rest_base" => "",
        "rest_controller_class" => "WP_REST_Posts_Controller",
        "rest_namespace" => "wp/v2",
        "has_archive" => false,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "delete_with_user" => false,
        "exclude_from_search" => true,
        "capability_type" => "job",
        "map_meta_cap" => true,
        "hierarchical" => false,
        "can_export" => true,
        "rewrite" => [ "slug" => "job", "with_front" => true ],
        "query_var" => true,
        "menu_position" => 5,
        "menu_icon" => "dashicons-money-alt",
        "supports" => [ "title", "editor", "thumbnail", "excerpt", "custom-fields", "author" ],
        "taxonomies" => [ "job_location", "job_type", "job_compensation", "job_compensation_type", "job_experience" ],
        "show_in_graphql" => false,
    ] );

    /**
     * Post Type: Baristas.
     */

    register_post_type( "barista", [
        "label" => esc_html__( "Baristas", "custom-post-type-ui" ),
        "labels" => [
            "name" => esc_html__( "Baristas", "custom-post-type-ui" ),
            "singular_name" => esc_html__( "Barista", "custom-post-type-ui" ),
        ],
        "description" => "",
        "public" => true,
        "publicly_queryable" => true,
        "show_ui" => true,
        "show_in_rest" => true,
        "rest_base" => "",
        "rest_controller_class" => "WP_REST_Posts_Controller",
        "rest_namespace" => "wp/v2",
        "has_archive" => true,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "delete_with_user" => true,
        "exclude_from_search" => false,
        "capability_type" => "barista",
        "map_meta_cap" => true,
        "hierarchical" => true,
        "can_export" => false,
        "rewrite" => [ "slug" => "barista", "with_front" => true ],
        "query_var" => true,
        "menu_position" => 5,
        "menu_icon" => "/wp-content/uploads/2021/08/course-icon.png",
        "supports" => [ "title", "editor", "thumbnail", "custom-fields", "author" ],
        "show_in_graphql" => false,
    ] );

    /**
     * Post Type: Active Codes.
     */

    register_post_type( "active_code",  [
        "label" => esc_html__( "Active Codes", "custom-post-type-ui" ),
        "labels" => [
            "name" => esc_html__( "Active Codes", "custom-post-type-ui" ),
            "singular_name" => esc_html__( "Active Code", "custom-post-type-ui" ),
        ],
        "description" => "",
        "public" => true,
        "publicly_queryable" => false,
        "show_ui" => true,
        "show_in_rest" => false,
        "rest_base" => "",
        "rest_controller_class" => "WP_REST_Posts_Controller",
        "rest_namespace" => "wp/v2",
        "has_archive" => false,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "delete_with_user" => false,
        "exclude_from_search" => true,
        "capability_type" => "active_code",
        "map_meta_cap" => true,
        "hierarchical" => false,
        "can_export" => false,
        "rewrite" => [ "slug" => "active_code", "with_front" => true ],
        "query_var" => true,
        "menu_position" => 5,
        "supports" => [ "title", "custom-fields", "author" ],
        "show_in_graphql" => false,
    ] );

    /**
     * Post Type: Advanced Codes.
     */

    register_post_type( "advanced_code",  [
        "label" => esc_html__( "Advanced Codes", "custom-post-type-ui" ),
        "labels" => [
            "name" => esc_html__( "Advanced Codes", "custom-post-type-ui" ),
            "singular_name" => esc_html__( "Advanced Code", "custom-post-type-ui" ),
        ],
        "description" => "",
        "public" => true,
        "publicly_queryable" => false,
        "show_ui" => true,
        "show_in_rest" => false,
        "rest_base" => "",
        "rest_controller_class" => "WP_REST_Posts_Controller",
        "rest_namespace" => "wp/v2",
        "has_archive" => false,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "delete_with_user" => false,
        "exclude_from_search" => true,
        "capability_type" => "advanced_code",
        "map_meta_cap" => true,
        "hierarchical" => false,
        "can_export" => false,
        "rewrite" => [ "slug" => "advanced_code", "with_front" => true ],
        "query_var" => true,
        "menu_position" => 5,
        "supports" => [ "title", "custom-fields", "author" ],
        "show_in_graphql" => false,
    ] );
}

add_action( 'init', 'fs_cptui_register_post_types' );
