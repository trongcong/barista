<?php

function get_barista_profile_id( $user_id = 0 ) {
	return get_field( 'barista_profile_id', 'user_' . ( ! empty( $user_id ) ? $user_id : get_current_user_id() ) );
}

function get_user_id_by_post_id( $post_id ) {
	return get_post_field( 'post_author', $post_id );
}

function get_barista_avatar( $post_id ) {
	$avatar_id = get_field( 'your_avatar', $post_id );

	return $avatar_id ? wp_get_attachment_image_url( $avatar_id['ID'] ) : um_get_default_avatar_uri();
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

/**
 * @param int $year_exp_min
 * @param int $year_exp_max
 * @param int $year_exp_aus_min
 * @param int $year_exp_aus_max
 * @param array $barista_skills
 * @param array $volumes
 * @param array $hospitality_skills
 *
 * @return WP_Query
 */
function create_query_barista( $year_exp_min = 0, $year_exp_max = 10, $year_exp_aus_min = 0, $year_exp_aus_max = 10, $barista_skills = [], $volumes = [], $hospitality_skills = [] ) {
	$meta_query = array(
		"relation" => "AND",
		array(
			'relation' => 'AND',
			array(
				'key'     => 'years_of_experience',
				'value'   => $year_exp_min,
				'type'    => 'DECIMAL',
				'compare' => '>=',
			),
			array(
				'key'     => 'years_of_experience',
				'value'   => $year_exp_max,
				'type'    => 'DECIMAL',
				'compare' => '<=',
			),
		),
		array(
			'relation' => 'AND',
			array(
				'key'     => 'experience_in_australia',
				'value'   => $year_exp_aus_min,
				'type'    => 'DECIMAL',
				'compare' => '>=',
			),
			array(
				'key'     => 'experience_in_australia',
				'value'   => $year_exp_aus_max,
				'type'    => 'DECIMAL',
				'compare' => '<=',
			),
		),
	);
	if ( ! empty( $barista_skills ) ) {
		$barista_skills_query = array( 'relation' => 'AND' );;
		foreach ( $barista_skills as $item ) {
			$barista_skills_query[] = array(
				'key'     => 'barista_skills',
				'value'   => $item,
				'compare' => 'LIKE',
			);
		}
		$meta_query[] = $barista_skills_query;
	}
	if ( ! empty( $volumes ) ) {
		$volumes_query = array( 'relation' => 'OR' );;
		foreach ( $volumes as $item ) {
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

	$attrs = array(
		'post_type'      => 'barista',
		'post_status'    => 'publish',
		'order'          => 'DESC',
		'orderby'        => 'ID',
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
function get_certification_by_barista( $id ) {
	$cer         = [];
	$active_code = get_field( "active_code", $id );

	$attrs = array(
		'post_type'      => 'active_code',
		'post_status'    => 'publish',
		'posts_per_page' => 2,
		'meta_query'     => [
			array(
				'key'     => 'active_code',
				'value'   => $active_code,
				'compare' => '=',
			)
		]
	);

	$query = new WP_Query( $attrs );
	if ( $query->found_posts ) {
		$code_id = $query->posts[0]->ID;
		$cer     = get_field( "training_certification", $code_id );
	}

	return $cer;
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
