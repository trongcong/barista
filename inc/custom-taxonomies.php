<?php
function fs_cptui_register_taxes() {

    /**
     * Taxonomy: Type Of Baristas.
     */
    register_taxonomy( "type_of_barista", [ "barista" ], [
        "label" => esc_html__( "Type Of Baristas", "custom-post-type-ui" ),
        "labels" => [
            "name" => esc_html__( "Type Of Baristas", "custom-post-type-ui" ),
            "singular_name" => esc_html__( "Type of barista", "custom-post-type-ui" ),
        ],
        "public" => true,
        "publicly_queryable" => true,
        "hierarchical" => true,
        "show_ui" => true,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "query_var" => true,
        "rewrite" => [ 'slug' => 'type_of_barista', 'with_front' => true, ],
        "show_admin_column" => true,
        "show_in_rest" => true,
        "show_tagcloud" => false,
        "rest_base" => "type_of_barista",
        "rest_controller_class" => "WP_REST_Terms_Controller",
        "rest_namespace" => "wp/v2",
        "show_in_quick_edit" => true,
        "sort" => false,
        "show_in_graphql" => false,
    ] );

    /**
     * Taxonomy: Job Locations.
     */
    register_taxonomy( "job_location", [ "job" ], [
        "label" => esc_html__( "Job Locations", "custom-post-type-ui" ),
        "labels" => [
            "name" => esc_html__( "Job Locations", "custom-post-type-ui" ),
            "singular_name" => esc_html__( "Job Location", "custom-post-type-ui" ),
        ],
        "public" => true,
        "publicly_queryable" => true,
        "hierarchical" => true,
        "show_ui" => true,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "query_var" => true,
        "rewrite" => [ 'slug' => 'job_location', 'with_front' => true, ],
        "show_admin_column" => false,
        "show_in_rest" => true,
        "show_tagcloud" => false,
        "rest_base" => "job_location",
        "rest_controller_class" => "WP_REST_Terms_Controller",
        "rest_namespace" => "wp/v2",
        "show_in_quick_edit" => false,
        "sort" => false,
        "show_in_graphql" => false,
    ] );

    /**
     * Taxonomy: Job Types.
     */
    register_taxonomy( "job_type", [ "job" ], [
        "label" => esc_html__( "Job Types", "custom-post-type-ui" ),
        "labels" => [
            "name" => esc_html__( "Job Types", "custom-post-type-ui" ),
            "singular_name" => esc_html__( "Job Type", "custom-post-type-ui" ),
        ],
        "public" => true,
        "publicly_queryable" => true,
        "hierarchical" => true,
        "show_ui" => true,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "query_var" => true,
        "rewrite" => [ 'slug' => 'job_type', 'with_front' => true, ],
        "show_admin_column" => false,
        "show_in_rest" => true,
        "show_tagcloud" => false,
        "rest_base" => "job_type",
        "rest_controller_class" => "WP_REST_Terms_Controller",
        "rest_namespace" => "wp/v2",
        "show_in_quick_edit" => false,
        "sort" => false,
        "show_in_graphql" => false,
    ] );

    /**
     * Taxonomy: Job Compensation.
     */
    register_taxonomy( "job_compensation", [ "job" ],  [
        "label" => esc_html__( "Job Compensation", "custom-post-type-ui" ),
        "labels" =>  [
            "name" => esc_html__( "Job Compensation", "custom-post-type-ui" ),
            "singular_name" => esc_html__( "Job Compensations", "custom-post-type-ui" ),
        ],
        "public" => true,
        "publicly_queryable" => true,
        "hierarchical" => true,
        "show_ui" => true,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "query_var" => true,
        "rewrite" => [ 'slug' => 'job_compensation', 'with_front' => true, ],
        "show_admin_column" => false,
        "show_in_rest" => true,
        "show_tagcloud" => false,
        "rest_base" => "job_compensation",
        "rest_controller_class" => "WP_REST_Terms_Controller",
        "rest_namespace" => "wp/v2",
        "show_in_quick_edit" => false,
        "sort" => false,
        "show_in_graphql" => false,
    ] );

    /**
     * Taxonomy: Job Compensation Type.
     */
    register_taxonomy( "job_compensation_type", [ "job" ], [
        "label" => esc_html__( "Job Compensation Type", "custom-post-type-ui" ),
        "labels" => [
            "name" => esc_html__( "Job Compensation Type", "custom-post-type-ui" ),
            "singular_name" => esc_html__( "Job Compensation Types", "custom-post-type-ui" ),
        ],
        "public" => true,
        "publicly_queryable" => true,
        "hierarchical" => true,
        "show_ui" => true,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "query_var" => true,
        "rewrite" => [ 'slug' => 'job_compensation_type', 'with_front' => true, ],
        "show_admin_column" => false,
        "show_in_rest" => true,
        "show_tagcloud" => false,
        "rest_base" => "job_compensation_type",
        "rest_controller_class" => "WP_REST_Terms_Controller",
        "rest_namespace" => "wp/v2",
        "show_in_quick_edit" => false,
        "sort" => false,
        "show_in_graphql" => false,
    ] );

    /**
     * Taxonomy: Job Experiences.
     */
    register_taxonomy( "job_experience", [ "job" ],  [
        "label" => esc_html__( "Job Experiences", "custom-post-type-ui" ),
        "labels" =>  [
            "name" => esc_html__( "Job Experiences", "custom-post-type-ui" ),
            "singular_name" => esc_html__( "Job Experience", "custom-post-type-ui" ),
        ],
        "public" => true,
        "publicly_queryable" => true,
        "hierarchical" => true,
        "show_ui" => true,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "query_var" => true,
        "rewrite" => [ 'slug' => 'job_experience', 'with_front' => true, ],
        "show_admin_column" => false,
        "show_in_rest" => true,
        "show_tagcloud" => false,
        "rest_base" => "job_experience",
        "rest_controller_class" => "WP_REST_Terms_Controller",
        "rest_namespace" => "wp/v2",
        "show_in_quick_edit" => false,
        "sort" => false,
        "show_in_graphql" => false,
    ] );
}
add_action( 'init', 'fs_cptui_register_taxes' );