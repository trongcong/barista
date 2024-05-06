<?php

function get_barista_profile_id( $user_id = 0 ) {
	return get_field( 'barista_profile_id', 'user_' . ( ! empty( $user_id ) ? $user_id : get_current_user_id() ) );
}

function get_user_id_by_post_id( $post_id ) {
	return get_post_field( 'post_author', $post_id );
}

function get_barista_avatar( $post_id, $size = 'thumbnail' ) {
	$avatar_id = get_field( 'your_avatar', $post_id );

	return $avatar_id ? wp_get_attachment_image_url( $avatar_id['ID'], $size ) : um_get_default_avatar_uri();
}

function get_barista_profile_link( $edit = true ) {
	$profile_id = get_barista_profile_id();

	return can_edit_barista_profile() ? get_the_permalink( $profile_id ) . ( $edit ? '?edit' : '' ) : '/register-barista/';
}

/**
 * @return bool
 */
function can_show_barista_profile_tab() {
	$profile_id = get_barista_profile_id();

	return current_user_can( fs_role_barista() );
}

function can_edit_barista_profile() {

	return is_user_logged_in() && get_barista_profile_id() && get_post_status( get_barista_profile_id() ) == 'publish';
}

function get_barista_meta( $postID, $metaKey ) {
	$count         = get_post_meta( $postID, $metaKey, true );
	$visitor_count = $count;
	if ( empty( $count ) ) {
		return 0;
	}

	if ( $count >= 1000 ) {
		$visitor_count = round( ( intval( $count ) / 1000 ), 2 );
		$visitor_count = $visitor_count . 'k';
	}

	return $visitor_count;
}

function get_barista_view( $postID, $metaKey = "barista_view" ) {
	return get_barista_meta( $postID, $metaKey );
}

function get_barista_contacted( $postID, $metaKey = "barista_contacted" ) {
	return get_barista_meta( $postID, $metaKey );
}

function get_volumes_mapping_data( $volumes = [] ) {
	$volumes_attr = [
		'Less than 3 kgs',
		'More than 3 kgs',
		'More than 4 kgs',
		'More than 5 kgs',
		'More than 6 kgs',
		'More than 7 kgs',
		'More than 8 kgs',
	];
	if ( in_array( $volumes_attr[6], $volumes ) ) {
		return $volumes_attr;
	} elseif ( in_array( $volumes_attr[5], $volumes ) ) {
		return array_slice( $volumes_attr, 0, 6 );
	} elseif ( in_array( $volumes_attr[4], $volumes ) ) {
		return array_slice( $volumes_attr, 0, 5 );
	} elseif ( in_array( $volumes_attr[3], $volumes ) ) {
		return array_slice( $volumes_attr, 0, 4 );
	} elseif ( in_array( $volumes_attr[2], $volumes ) ) {
		return array_slice( $volumes_attr, 0, 3 );
	} elseif ( in_array( $volumes_attr[1], $volumes ) ) {
		return array_slice( $volumes_attr, 0, 2 );
	} elseif ( in_array( $volumes_attr[0], $volumes ) ) {
		return array_slice( $volumes_attr, 0, 1 );
	}
}
/**
 * @param $layout
 * @param $year_exp_min
 * @param $year_exp_max
 * @param $year_exp_aus_min
 * @param $year_exp_aus_max
 * @param $barista_skills
 * @param $volumes
 * @param $hospitality_skills
 *
 * @return WP_Query
 */
function create_query_barista( $layout = 'grid', $year_exp_min = 0, $year_exp_max = 10, $year_exp_aus_min = 0, $year_exp_aus_max = 10, $barista_skills = [], $volumes = [], $hospitality_skills = [] ) {
	$meta_query = array(
		"relation" => "AND",
		array(
			'relation' => 'OR',
			[
				'key'     => 'barista_hide_profile',
				'value'   => '',
				'compare' => '=',
			],
			[
				'key'     => 'barista_hide_profile',
				'compare' => 'NOT EXISTS',
			]
		),
		array(
			'relation' => 'AND',
			array(
				'key'     => 'years_of_experience',
				'value'   => floatval( $year_exp_min ),
				'type'    => 'DECIMAL(2,1)',
				'compare' => '>=',
			),
			array(
				'key'     => 'years_of_experience',
				'value'   => floatval( $year_exp_max ),
				'type'    => 'DECIMAL(2,1)',
				'compare' => '<=',
			),
		),
		array(
			'relation' => 'AND',
			array(
				'key'     => 'experience_in_australia',
				'value'   => floatval( $year_exp_aus_min ),
				'type'    => 'DECIMAL(2,1)',
				'compare' => '>=',
			),
			array(
				'key'     => 'experience_in_australia',
				'value'   => floatval( $year_exp_aus_max ),
				'type'    => 'DECIMAL(2,1)',
				'compare' => '<=',
			),
		),
	);
	if ( ! empty( $barista_skills ) ) {
		$barista_skills_query = array( 'relation' => 'AND' );;
		foreach ( $barista_skills as $item ) {
			$barista_skills_query[] = array( 'key' => 'barista_skills', 'value' => $item, 'compare' => 'LIKE', );
		}
		$meta_query[] = $barista_skills_query;
	}
	if ( ! empty( $volumes ) ) {
		$volumes_mapping = get_volumes_mapping_data( $volumes );
		$volumes_query = array( 'relation' => 'OR' );
		foreach ( $volumes_mapping as $item ) {
			$volumes_query[] = array(
				'key'     => 'volume_you_are_able_to_handle_solo',
				'value'   => $item,
				'compare' => 'LIKE',
			);
		}
		$meta_query[] = $volumes_query;
	}
	if ( ! empty( $hospitality_skills ) ) {
		$hospitality_skills_query = array( 'relation' => 'AND' );;
		foreach ( $hospitality_skills as $item ) {
			$hospitality_skills_query[] = array(
				'key'     => 'hospitality_skills',
				'value'   => $item,
				'compare' => 'LIKE',
			);
		}
		$meta_query[] = $hospitality_skills_query;
	}

	if ( $layout == 'map' ) {
		$meta_query[] = array(
			'relation' => 'AND',
			array(
				'key'     => 'locations',
				'compare' => 'EXISTS',
			),
			array(
				'key'     => 'locations',
				'value'   => null,
				'compare' => '!=',
			),
		);
	}

	$attrs = array(
		'post_type'      => 'barista',
		'post_status'    => 'publish',
		'order'          => 'DESC',
		'orderby'        => 'modified',
		'posts_per_page' => 500,
		'meta_query'     => $meta_query
	);

	return new WP_Query( $attrs );
}

/**
 * @param $id
 *
 * @return array
 */
function get_certification_by_barista( $id, $advanced_code_only = false ) {
	$cer               = [];
	$cer_advanced_code = [];
	$active_code       = get_field( "active_code", $id );
	$advanced_code     = get_field( "advanced_code", $id );

	$attrs               = array(
		'post_type'      => 'active_code',
		'post_status'    => 'publish',
		'posts_per_page' => 2,
		'meta_query'     => [ array( 'key' => 'active_code', 'value' => $active_code, 'compare' => '=', ) ]
	);
	$attrs_advanced_code = array(
		'post_type'      => 'advanced_code',
		'post_status'    => 'publish',
		'posts_per_page' => 2,
		'meta_query'     => [ array( 'key' => 'advanced_code', 'value' => $advanced_code, 'compare' => '=', ) ]
	);
	$query_active_code   = new WP_Query( $attrs );
	$query_advanced_code = new WP_Query( $attrs_advanced_code );

	if ( $query_active_code->found_posts ) {
		$code_id = $query_active_code->posts[0]->ID;
		$cer     = get_field( "training_certification", $code_id );
	}
	if ( $query_advanced_code->found_posts ) {
		$code_id           = $query_advanced_code->posts[0]->ID;
		$cer_advanced_code = get_field( "training_certification", $code_id );
		$cer               = array_merge( $cer, $cer_advanced_code );
	}

	return $advanced_code_only ? $cer_advanced_code : $cer;
}

add_action( 'wp_head', 'set_barista_view_count' );
function set_barista_view_count() {
	if ( is_single() && get_post_type() === "barista" ) {
		global $post;
		$count_key     = 'barista_view';
		$visitor_count = esc_attr( get_post_meta( $post->ID, $count_key, true ) );
		if ( $visitor_count == '' ) {
			$visitor_count = 1;
			add_post_meta( $post->ID, $count_key, $visitor_count );
		} else {
			$visitor_count = (int) $visitor_count + 1;
			update_post_meta( $post->ID, $count_key, $visitor_count );
		}
	}
}

function get_map_data_for_barista() {
	$layout          = $_GET['layout'] ?? 'grid';
	$is_map          = $layout === 'map';
	$is_listing_page = get_page_template_slug() === 'page-listings.php';
	if ( ! $is_map && ! $is_listing_page ) {
		return [];
	}

	$query    = create_query_barista( $layout );
	$map_data = [];
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$map_data[] = get_barista_map_item( get_the_ID() );
		}

		wp_reset_postdata();

		return $map_data;
	} else {
		return [];
	}
}

function get_barista_map_item( $id ) {
	$locations               = get_field( 'locations' );
	$volume                  = get_field( 'volume_you_are_able_to_handle_solo', $id ) ?? '';
	$volume                  = str_replace( 'More than', '>', $volume );
	$volume                  = str_replace( 'Less than', '<', $volume );
	$name                    = get_field( 'preferred_name', $id );
	$name                    = ! $name ? get_the_title( $id ) : $name;
	$experience_in_australia = get_field( "experience_in_australia", $id )['value'];
	$years_of_experience     = get_field( "years_of_experience", $id )['value'];

	return [
		"id"                      => $id,
		"title"                   => $name,
		"url"                     => get_the_permalink(),
		'avatar'                  => get_barista_avatar( $id ),
		"experience_in_australia" => $experience_in_australia . ' yrs',
		"years_of_experience"     => $years_of_experience . ' yrs',
		"volume"                  => $volume,
		"locations"               => $locations
	];
}