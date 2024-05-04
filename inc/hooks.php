<?php
add_filter( 'acf/settings/save_json', 'lt_acf_json_save_point' );
function lt_acf_json_save_point( $path ) {
	// update path
	$path = get_stylesheet_directory() . '/inc/acf-options';

	// return
	return $path;
}

add_filter( 'acf/settings/load_json', 'lt_acf_json_load_point' );
function lt_acf_json_load_point( $paths ) {
	// remove original path (optional)
	unset( $paths[0] );
	// append path
	$paths[] = get_stylesheet_directory() . '/inc/acf-options';

	// return
	return $paths;
}

add_action( 'acf/init', 'lt_acf_init' );
function lt_acf_init() {
	acf_update_setting( 'google_api_key', 'AIzaSyAitFZqjWLqRCzMd8FLqbTjeQnDnVbWwYE' );
}

add_filter( "ocean_post_layout_class", "filter_ocean_post_layout_class" );
function filter_ocean_post_layout_class( $class ) {
	if ( is_singular( "barista" ) || is_singular( "job" ) ) {
		$class = "full-width";
	}

	return $class;
}

add_filter( "ocean_display_page_header", "filter_ocean_display_page_header", 20 );
function filter_ocean_display_page_header( $return ) {
	if ( is_singular( "barista" ) ) {
		$return = false;
	}

	return $return;
}

//add_action( "ocean_before_content_wrap", "add_action_ocean_before_content_wrap", 20 );
function lt_add_page_header( $title ) {
	if ( is_singular( "barista" ) ) { ?>
		<header class="page-header background-image-page-header hide-all-devices" style="
            background-image: url(https://firstshotbaristatraining.com.au/wp-content/uploads/2019/05/cover.jpg);
            background-position: center;
        ">
			<div class="container clr page-header-inner">
				<h1 class="page-header-title clr" itemprop="headline">
					<span class="__thanks"><?= $title ?></span>
				</h1>
			</div>
			<span class="background-image-page-header-overlay"></span>
		</header>
		<?php
	}
}

add_action( 'wp_ajax_lt_ajax_filter_barista', 'lt_ajax_filter_barista' );
add_action( 'wp_ajax_nopriv_lt_ajax_filter_barista', 'lt_ajax_filter_barista' );
function lt_ajax_filter_barista() {
	// First check the nonce, if it fails the function will break
	check_ajax_referer( "_security", 'security' );

	$training_certification = isset( $_POST['training_certification'] ) ? $_POST['training_certification'] : [];
	$barista_skills         = isset( $_POST['barista_skills'] ) ? $_POST['barista_skills'] : [];
	$volumes                = isset( $_POST['volumes'] ) ? $_POST['volumes'] : [];
	$hospitality_skills     = isset( $_POST['hospitality_skills'] ) ? $_POST['hospitality_skills'] : [];
	$year_exp_min           = isset( $_POST['year_exp_min'] ) ? floatval( $_POST['year_exp_min'] ) : 0;
	$year_exp_max           = isset( $_POST['year_exp_max'] ) ? floatval( $_POST['year_exp_max'] ) : 10;
	$year_exp_aus_min       = isset( $_POST['year_exp_aus_min'] ) ? floatval( $_POST['year_exp_aus_min'] ) : 0;
	$year_exp_aus_max       = isset( $_POST['year_exp_aus_max'] ) ? floatval( $_POST['year_exp_aus_max'] ) : 10;
	$layout = isset( $_POST['layout'] ) ? $_POST['layout'] : 'grid';

	$query        = create_query_barista( $layout, $year_exp_min, $year_exp_max, $year_exp_aus_min, $year_exp_aus_max, $barista_skills, $volumes, $hospitality_skills );
	$not_found = '<div class="__lt-item item-not-found">Sorry, no barista matched your criteria.</div>';

	function areAllElementsExistInArray( $list, $listCheck ) {
		$intersect = array_intersect( $list, $listCheck );

		return count( $intersect ) === count( $listCheck );
	}

	$i = 0;
	$barista_data = [];
	ob_start();
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$id            = get_the_ID();
			$certification = get_certification_by_barista( $id, true );

			if ( empty( $training_certification ) ) {
				get_lt_item2( $id );
				$barista_data[] = get_barista_map_item( $id );
				$i ++;
			} else {
				if ( areAllElementsExistInArray( $certification, $training_certification ) ) {
					get_lt_item2( $id );
					$barista_data[] = get_barista_map_item( $id );
					$i ++;
				}
			}
		}
		// Restore original Post Data
		wp_reset_postdata();
	} else {
		echo $not_found;
	}
	$items = ob_get_clean();
	$items = empty( $items ) ? $not_found : $items;
	wp_send_json( [
		"items"       => $layout === 'map' ? $barista_data : $items,
		"count"       => $i,
		"found_posts" => $query->found_posts,
		"query"       => $query
	] );

	wp_die();
}

add_action( 'wp_ajax_lt_ajax_create_new_barista', 'lt_ajax_create_new_barista' );
add_action( 'wp_ajax_nopriv_lt_ajax_create_new_barista', 'lt_ajax_create_new_barista' );
function lt_ajax_create_new_barista() {
	// First check the nonce, if it fails the function will break
	check_ajax_referer( "_security", 'security' );

	$active_code                      = isset( $_POST['active_code'] ) ? trim( $_POST['active_code'] ) : '';
	$full_name                        = isset( $_POST['full_name'] ) ? $_POST['full_name'] : $active_code;
	$describe_yourself_in_2_sentences = isset( $_POST['describe_yourself_in_2_sentences'] ) ? $_POST['describe_yourself_in_2_sentences'] : '';

	$is_exits_profile       = count_user_posts( get_current_user_id(), "barista" );
	$query_active_code      = new WP_Query( [
		'post_type'      => 'active_code',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'meta_query'     => [
			array(
				'key'     => 'active_code',
				'value'   => $active_code,
				'compare' => '=',
			)
		]
	] );
	$query_active_code_used = new WP_Query( [
		'post_type'      => 'barista',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'meta_query'     => [
			array(
				'key'     => 'active_code',
				'value'   => $active_code,
				'compare' => '=',
			)
		]
	] );

	if ( ! ! $is_exits_profile ) {
		wp_send_json_error( "Your profile really exists! Go to your profile now?", 404 );
		wp_die();
	}
	if ( ! $active_code || ! $query_active_code->found_posts ) {
		wp_send_json_error( "Active code not found!", 404 );
		wp_die();
	}
	if ( $query_active_code_used->found_posts ) {
		wp_send_json_error( "Active code already used!", 404 );
		wp_die();
	}
	$post_data = array(
		'post_title'     => wp_strip_all_tags( $full_name ),
		'post_content'   => $describe_yourself_in_2_sentences,
		'post_status'    => 'publish',
		'post_type'      => 'barista',
		'comment_status' => 'closed',
		'ping_status'    => 'closed',
	);

	$post_id = wp_insert_post( $post_data );
	foreach ( $_POST as $key => $value ) {
		if ( ! in_array( $key, [ 'action', 'security', 'type' ] ) ) {
			update_field( $key, $value, $post_id );
		}
	}
	$attachment_ids = [];
	foreach ( $_FILES as $key => $file ) {
		if ( ! is_array( $file['tmp_name'] ) ) {
			$attachment_id = upload_file_to_media( $file['name'], $file["tmp_name"] );
			update_field( $key, $attachment_id, $post_id );
			$attachment_ids[] = $attachment_id;
		}
		if ( $key == 'your_photos' ) {
			foreach ( $file['tmp_name'] as $index => $v ) {
				$attachment_id = upload_file_to_media( $file['name'][ $index ], $file['tmp_name'][ $index ] );
				add_row( $key, [ 'photo_item' => $attachment_id ], $post_id );
				$attachment_ids[] = $attachment_id;
			}
		}
	}
	$date_of_post = get_the_date( "c", $post_id );
	update_field( "re_active_profile", $date_of_post, $post_id );
	update_field( "barista_had_a_job", false, $post_id );
	update_field( "barista_hide_profile", false, $post_id );
	update_field( "barista_profile_id", $post_id, "user_" . get_current_user_id() );

	wp_send_json( [
		'id'             => $post_id,
		'attachment_ids' => $attachment_ids,
		'url'            => get_permalink( $post_id ),
	] );

	wp_die();
}

add_action( 'wp_ajax_lt_ajax_create_new_job', 'lt_ajax_create_new_job' );
add_action( 'wp_ajax_nopriv_lt_ajax_create_new_job', 'lt_ajax_create_new_job' );
function lt_ajax_create_new_job() {
	check_ajax_referer( "_security", 'security' );
	$title                 = isset( $_POST['title'] ) ? $_POST['title'] : '';
	$description           = isset( $_POST['description'] ) ? $_POST['description'] : '';
	$job_location          = isset( $_POST['job_location'] ) ? array_map( 'intval', $_POST['job_location'] ) : [];
	$job_type              = isset( $_POST['job_type'] ) ? array_map( 'intval', $_POST['job_type'] ) : [];
	$job_compensation      = isset( $_POST['job_compensation'] ) ? array_map( 'intval', $_POST['job_compensation'] ) : [];
	$job_compensation_type = isset( $_POST['job_compensation_type'] ) ? array_map( 'intval', $_POST['job_compensation_type'] ) : [];
	$job_experience        = isset( $_POST['job_experience'] ) ? array_map( 'intval', $_POST['job_experience'] ) : [];

	$post_data = array(
		'post_title'     => wp_strip_all_tags( $title ),
		'post_content'   => $description,
		'post_status'    => 'publish',
		'post_type'      => 'job',
		'comment_status' => 'closed',
		'ping_status'    => 'closed',
	);

	$post_id = wp_insert_post( $post_data );
	foreach ( $_POST as $key => $value ) {
		if ( ! in_array( $key, [
			'action',
			'security',
			'type',
			"job_location",
			"job_type",
			"job_compensation",
			"job_compensation_type",
			"job_experience",
		] ) ) {
			update_field( $key, $value, $post_id );
		}
	}
	if ( ! empty( $job_location ) ) {
		wp_set_post_terms( $post_id, $job_location, "job_location" );
	}
	if ( ! empty( $job_type ) ) {
		wp_set_post_terms( $post_id, $job_type, "job_type" );
	}
	if ( ! empty( $job_compensation ) ) {
		wp_set_post_terms( $post_id, $job_compensation, "job_compensation" );
	}
	if ( ! empty( $job_compensation_type ) ) {
		wp_set_post_terms( $post_id, $job_compensation_type, "job_compensation_type" );
	}
	if ( ! empty( $job_experience ) ) {
		wp_set_post_terms( $post_id, $job_experience, "job_experience" );
	}

	$attachment_ids = [];
	foreach ( $_FILES as $key => $file ) {
		if ( ! is_array( $file['tmp_name'] ) ) {
			$attachment_id = upload_file_to_media( $file['name'], $file["tmp_name"] );
			update_field( $key, $attachment_id, $post_id );
			$attachment_ids[] = $attachment_id;
		}
		if ( $key == 'upload_your_image' ) {
			foreach ( $file['tmp_name'] as $index => $v ) {
				$attachment_id = upload_file_to_media( $file['name'][ $index ], $file['tmp_name'][ $index ] );
				add_row( $key, [ 'photo_item' => $attachment_id ], $post_id );
				$attachment_ids[] = $attachment_id;
			}
		}
	}

	wp_send_json( [
		'id'             => $post_id,
		'attachment_ids' => $attachment_ids,
		'url'            => get_permalink( $post_id ),
	] );

	wp_die();
}

add_action( 'wp_ajax_lt_ajax_contact_action', 'lt_ajax_contact_action' );
add_action( 'wp_ajax_nopriv_lt_ajax_contact_action', 'lt_ajax_contact_action' );
function lt_ajax_contact_action() {
	check_ajax_referer( "_security", 'security' );
	$id        = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 1;
	$contacted = get_post_meta( $id, "barista_contacted", true );
	$contacted = ! empty( $contacted ) ? $contacted + 1 : 1;

	update_post_meta( $id, "barista_contacted", $contacted );

	wp_send_json( [
		'data' => $contacted,
	] );

	wp_die();
}

add_action( 'wp_ajax_lt_ajax_barista_action', 'lt_ajax_barista_action' );
add_action( 'wp_ajax_nopriv_lt_ajax_barista_action', 'lt_ajax_barista_action' );
function lt_ajax_barista_action() {
	check_ajax_referer( "_security", 'security' );
	$id          = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : null;
	$hadAJob     = isset( $_POST['had_a_job'] ) ? filter_var( $_POST['had_a_job'], FILTER_VALIDATE_BOOLEAN ) : false;
	$hideProfile = isset( $_POST['hide_profile'] ) ? filter_var( $_POST['hide_profile'], FILTER_VALIDATE_BOOLEAN ) : false;

	if ( isset( $_POST['had_a_job'] ) ) {
		update_post_meta( $id, "barista_had_a_job", $hadAJob );
	}
	if ( isset( $_POST['hide_profile'] ) ) {
		update_post_meta( $id, "barista_hide_profile", $hideProfile );
	}

	wp_send_json( [
		'data' => [
			get_post_meta( $id, "barista_had_a_job", true ),
			get_post_meta( $id, "barista_hide_profile", true )
		],
	] );

	wp_die();
}

add_action( 'acf/save_post', 'fs_update_barista' );
function fs_update_barista( $post_id ) {
	if ( get_post_type( $post_id ) !== 'barista' || is_admin() ) {
		return;
	}

	$post_update = [
		'post_title'   => wp_strip_all_tags( get_field( "full_name" ) ),
		'post_content' => get_field( "describe_yourself_in_2_sentences" ),
	];
	wp_update_post( $post_update );
}

add_action( 'wp_footer', 'lt_cta_contact' );
function lt_cta_contact() {
	if ( is_single() && get_post_type() === "barista" ) {
		$phone_number = get_field( "your_phone_number" ); ?>
		<div class="__cta __contact-item">
			<a href="tel:<?= $phone_number ?>">
				<span>Contact me</span>
				<span class="__svg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
                      <path fill-rule="evenodd"
                            d="M3.6 7.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V17c0 .6-.4 1-1 1C7.6 18 0 10.4 0 1c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.2.2 2.4.6 3.6.1.3 0 .7-.2 1L3.6 7.8Z"/>
                    </svg>
                </span>
			</a>
		</div>
		<?php
	}
}

//add_action( 'wp_trash_post', 'lt_add_action_before_delete_post', 99, 2 );
add_action( 'before_delete_post', 'lt_add_action_before_delete_post', 99, 2 );
function lt_add_action_before_delete_post( $postid, $post ) {
	if ( 'barista' !== $post->post_type ) {
		return;
	}

	$author_id = $post->post_author;
	delete_field( 'barista_profile_id', 'user_' . $author_id );
}

add_filter( 'um_account_page_default_tabs_hook', 'lt_add_filter_um_account_page_default_tabs_hook', 100 );
function lt_add_filter_um_account_page_default_tabs_hook( $tabs ) {
	if ( ! can_show_barista_profile_tab() ) {
		return $tabs;
	}
	$title = 'Update Barista Profile';
	if ( ! can_edit_barista_profile() ) {
		$title = 'Register Barista Profile';
	}
	$tabs[110]['profile_barista']['icon']   = 'um-faicon-pencil';
	$tabs[110]['profile_barista']['title']  = $title;
	$tabs[110]['profile_barista']['custom'] = true;
	if ( can_edit_barista_profile() ) {
		unset( $tabs[300] );
	}

	return $tabs;
}

/* make our new tab hookable */
add_action( 'um_account_tab_profile_barista', 'um_account_tab_profile_barista' );
function um_account_tab_profile_barista( $info ) {
	global $ultimatemember;
	extract( $info );
	$output = $ultimatemember->account->get_tab_output( 'profile_barista' );
	if ( $output ) {
		echo $output;
	}
}

/* Finally we add some content in the tab */
add_filter( 'um_account_content_hook_profile_barista', 'um_account_content_hook_profile_barista' );
function um_account_content_hook_profile_barista( $output ) {
	ob_start(); ?>
	<div class="um-field">
		<?php if ( can_edit_barista_profile() ) {
			echo '<a href="' . ( get_barista_profile_link() ) . '">Update Barista Profile Now</a>';
		} else {
			echo "<p>Your barista profile not found! </p>";
			echo '<a href="/register-barista/">Register Barista Profile Now</a>';
		} ?>
	</div>
	<?php

	$output .= ob_get_contents();
	ob_end_clean();

	return $output;
}

add_action( 'um_delete_user', 'add_action_um_delete_user', 20, 1 );
function add_action_um_delete_user( $user_id ) {
	$args       = array(
		'numberposts' => - 1,
		'post_type'   => [ 'barista' ],
		'author'      => $user_id
	);
	$user_posts = get_posts( $args );

	if ( empty( $user_posts ) ) {
		return;
	}
	foreach ( $user_posts as $user_post ) {
		wp_delete_post( $user_post->ID, true );
	}
}

add_filter( 'manage_barista_posts_columns', 'custom_columns_list_for_barista' );
function custom_columns_list_for_barista( $columns ) {
	$columns['actions'] = 'Actions';

	return $columns;
}

add_action( 'manage_barista_posts_custom_column', 'barista_custom_column_values', 10, 2 );
function barista_custom_column_values( $column, $post_id ) {
	if ( ! function_exists( 'get_field' ) ) {
		return;
	}
	$active_profile = active_profile( $post_id );
	$hide_profile   = get_post_meta( $post_id, "barista_hide_profile", true );

	switch ( $column ) {
		case 'actions':
			fs_actions_profile( $post_id );
			break;
		default:
			break;
	}
}


add_filter( 'manage_active_code_posts_columns', 'custom_columns_list_for_active_code' );
function custom_columns_list_for_active_code( $columns ) {
	$columns['active_code'] = 'Active Code';
	$columns['used_by']     = 'User By';

	return $columns;
}

add_action( 'manage_active_code_posts_custom_column', 'active_code_custom_column_values', 10, 2 );
function active_code_custom_column_values( $column, $post_id ) {
	if ( ! function_exists( 'get_field' ) ) {
		return;
	}
	$active_code = get_field( 'active_code', $post_id );

	$query_active_code_used = new WP_Query( [
		'post_type'      => 'barista',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'meta_query'     => [
			array(
				'key'     => 'active_code',
				'value'   => $active_code,
				'compare' => '=',
			)
		]
	] );

	switch ( $column ) {
		case 'active_code'    :
			echo '<code style="padding: 5px;">' . $active_code . '</code>';

			break;
		case 'used_by':
			if ( ! $query_active_code_used->found_posts ) {
				echo '<code style="padding: 5px;">Unused</code>';
			} else {
				$post_title = $query_active_code_used->post->post_title;
				echo '<code style="padding: 5px;">Used by <a href="' . admin_url( '/edit.php?s=' . $post_title . '&post_status=all&post_type=barista' ) . '">' . $post_title . '</a></code>';
			}
			break;
	}
}

add_filter( 'manage_advanced_code_posts_columns', 'custom_columns_list_for_advanced_code' );
function custom_columns_list_for_advanced_code( $columns ) {
	$columns['advanced_code'] = 'Advanced Code';
	$columns['used_by']       = 'User By';

	return $columns;
}

add_action( 'manage_advanced_code_posts_custom_column', 'advanced_code_custom_column_values', 10, 2 );
function advanced_code_custom_column_values( $column, $post_id ) {
	if ( ! function_exists( 'get_field' ) ) {
		return;
	}
	$advanced_code = get_field( 'advanced_code', $post_id );

	$query_advanced_code_used = new WP_Query( [
		'post_type'      => 'barista',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'meta_query'     => [
			array(
				'key'     => 'advanced_code',
				'value'   => $advanced_code,
				'compare' => '=',
			)
		]
	] );

	switch ( $column ) {
		case 'advanced_code'    :
			echo '<code style="padding: 5px;">' . $advanced_code . '</code>';

			break;
		case 'used_by':
			if ( ! $query_advanced_code_used->found_posts ) {
				echo '<code style="padding: 5px;">Unused</code>';
			} else {
				$post_title = $query_advanced_code_used->post->post_title;
				echo '<code style="padding: 5px;">Used by <a href="' . admin_url( '/edit.php?s=' . $post_title . '&post_status=all&post_type=barista' ) . '">' . $post_title . '</a></code>';
			}
			break;
	}
}

add_filter( 'acf/validate_value/key=field_65844e8455881', 'filter_acf_validate_update_advanced_code', 99, 4 );
function filter_acf_validate_update_advanced_code( $valid, $value, $field, $input ) {
	$post_id = $_POST['_acf_post_id'] ?? $_POST['post_id'];
	if ( ! $value || ! $post_id ) {
		return $valid;
	}

	$query_advanced_code      = new WP_Query( [
		'post_type'      => 'advanced_code',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'meta_query'     => [
			array(
				'key'     => 'advanced_code',
				'value'   => $value,
				'compare' => '=',
			)
		]
	] );
	$query_advanced_code_used = new WP_Query( [
		'post_type'      => 'barista',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'post__not_in'   => [ $post_id ],
		'meta_query'     => [
			array(
				'key'     => 'advanced_code',
				'value'   => $value,
				'compare' => '=',
			)
		]
	] );

	if ( ! $query_advanced_code->found_posts ) {
		return "Advanced code not found!";
	}
	if ( $query_advanced_code_used->found_posts ) {
		return "Advanced code already used!";
	}

	return $valid;
}

//add_filter( 'acf/load_field/name=location', 'lt_acf_load_field_location', 10, 3 );
function lt_acf_load_field_location( $field ) {
	$nsw     = lt_read_post_codes();
	$choices = [];
	foreach ( $nsw as $item ) {
		$choices[ $item['id'] ] = $item['postcode'] . ' - ' . $item['locality'] . ' / ' . ( $item['dc'] !== 'NULL' ? $item['dc'] : $item['sa4name'] );
	}
	$field['choices'] = $choices;

	return $field;
}
