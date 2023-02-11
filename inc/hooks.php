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

add_filter( "ocean_post_layout_class", "filter_ocean_post_layout_class" );
function filter_ocean_post_layout_class( $class ) {
	if ( is_singular( "barista" ) ) {
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

//add_filter( 'ocean_main_metaboxes_post_types', 'filter_oceanwp_metabox', 20 );
//function filter_oceanwp_metabox( $types ) {
//	$types[] = 'barista';
//
//	// Return
//	return $types;
//}

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

	$query     = create_query_barista( $year_exp_min, $year_exp_max, $year_exp_aus_min, $year_exp_aus_max, $barista_skills, $volumes, $hospitality_skills );
	$not_found = '<div class="__lt-item item-not-found">Sorry, no barista matched your criteria.</div>';

	$i = 0;
	ob_start();
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$certification = get_certification_by_barista( get_the_ID() );
			if ( empty( $training_certification ) ) {
				get_lt_item( get_the_ID() );
				$i ++;
			} else {
				if ( count( $training_certification ) == 1 && in_array( $training_certification[0], $certification ) ) {
					get_lt_item( get_the_ID() );
					$i ++;
				} else {
					if ( in_array( $certification, [ $training_certification ] ) ) {
						get_lt_item( get_the_ID() );
						$i ++;
					}
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
	//sleep(100);
	wp_send_json( [
		"items"       => $items,
		"count"       => $i,
		"found_posts" => $query->found_posts
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
	foreach ( $_FILES as $key => $value ) {
		$attachment_id    = upload_file_to_media( $key, $post_id );
		$attachment_ids[] = $attachment_id;
	}
	$date_of_post = get_the_date( "c", $post_id );
	update_field( "re_active_profile", $date_of_post, $post_id );

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
			"job_compensation_type"
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

	$attachment_ids = [];
	foreach ( $_FILES as $key => $value ) {
		$attachment_id    = upload_file_to_media( $key, $post_id );
		$attachment_ids[] = $attachment_id;
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

add_action( 'acf/save_post', 'update_barista' );
function update_barista( $post_id ) {
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
	if ( is_single() && get_post_type() === "barista" ) { ?>
        <div class="__cta __contact-item">
            <a href="tel:0123456">
                <span>Contact me</span>
                <span class="__svg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
                      <path fill-rule="evenodd" d="M3.6 7.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V17c0 .6-.4 1-1 1C7.6 18 0 10.4 0 1c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.2.2 2.4.6 3.6.1.3 0 .7-.2 1L3.6 7.8Z"/>
                    </svg>
                </span>
            </a>
        </div>
		<?php
	}
}
