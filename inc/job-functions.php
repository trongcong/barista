<?php
/**
 * @param $date
 *
 * @return bool
 * @throws Exception
 */
function fs_is_job_expiration( $date ) {
	if ( ! $date ) {
		return false;
	}
	$date_now = new DateTime();
	$date2    = new DateTime( $date );

	return $date_now > $date2;
}

function fs_job_header( $post_id ) {
	$email = get_field( 'email', $post_id );
	$phone = get_field( 'contact_number', $post_id );
	if ( $email ) {
		$apply = "mailto:" . $email;
	} elseif ( $phone ) {
		$apply = "tel:" . $phone;
	} else {
		$apply = 'javascript:void(0);';
	}
	?>
    <div id="job-header" class="entry-header job-header">
        <div class="container">
            <div class="max-960">
                <div class="job-header-inner">
                    <div class="job-hd-meta">
                        <div class="logo">
                            <img src="<?php fs_get_business_avatar_uri_by_post_id( $post_id ) ?>" alt="avatar">
                        </div>
                        <div class="job-info">
                            <h2 class="job-title"><?= strip_tags( get_the_title( $post_id ) ); ?></h2>
                            <h3 class="business-title"><span>by</span> <?php fs_get_business_name_by_post_id( $post_id ) ?></h3>
                            <div class="meta-detail">
                                <?= fs_get_job_salary( $post_id ) ? '<div class="job-salary">' . fs_get_job_salary( $post_id ) . '</div>' : '' ?>
                                <?= get_term_list_by_post_id( $post_id, 'job_location' ) ? '<div class="job-category">' . get_term_list_by_post_id( $post_id, 'job_location' ) . '</div>' : '' ?>
                                <?= get_term_list_by_post_id( $post_id, 'job_type' ) ? '<div class="job-type">' . get_term_list_by_post_id( $post_id, 'job_type' ) . '</div>' : '' ?>
                            </div>
                        </div>
                    </div>
                    <div class="job-hd-action">
                        <div class="deadline-time">
                            Application ends:
                            <strong><?= get_field( 'expiration_date', $post_id ) ? get_field( 'expiration_date', $post_id ) : "No expiration" ?></strong>
                        </div>
                        <a href="<?= $apply ?>" class="btn-apply-job">Apply Now<i class="next flaticon-right-up"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<?php
}

function fs_job_overview( $post_id ) {
	$jobs = [
		[
			'icon'  => 'flaticon-calendar',
			'text'  => 'Date Posted',
			'value' => get_the_date( '', $post_id )
		],
		[
			'icon'  => 'flaticon-place',
			'text'  => 'Location',
			'value' => get_term_list_by_post_id( $post_id, 'job_location' ) ? get_term_list_by_post_id( $post_id, 'job_location' ) : "No information"
		],
		[
			'icon'  => 'flaticon-tracking',
			'text'  => 'Address',
			'value' => get_field( 'address', $post_id )
		],
		[
			'icon'  => 'flaticon-fifteen',
			'text'  => 'Expiration date',
			'value' => get_field( 'expiration_date', $post_id ) ? get_field( 'expiration_date', $post_id ) : "No expiration"
		],
		[
			'icon'  => 'flaticon-badge',
			'text'  => 'Experience',
			'value' => get_term_list_by_post_id( $post_id, 'job_experience' ) ? get_term_list_by_post_id( $post_id, 'job_experience' ) : "Not require"
		],
		[
			'icon'  => 'flaticon-working',
			'text'  => 'Job Type',
			'value' => get_term_list_by_post_id( $post_id, 'job_type' ) ? get_term_list_by_post_id( $post_id, 'job_type' ) : "No information"
		],
		[
			'icon'  => 'flaticon-money',
			'text'  => 'Compensation',
			'value' => get_term_list_by_post_id( $post_id, 'job_compensation' ) ? get_term_list_by_post_id( $post_id, 'job_compensation' ) : "No information"
		],
		[
			'icon'  => 'flaticon-category',
			'text'  => 'Compensation Type',
			'value' => get_term_list_by_post_id( $post_id, 'job_compensation_type' ) ? get_term_list_by_post_id( $post_id, 'job_compensation_type' ) : "No information"
		],
	];
	?>
    <div class="job-overview">
        <h3 class="overview-title">Job Overview</h3>
        <ul class="job-service-detail">
            <?php
            foreach ( $jobs as $job ) {
	            if ( is_array( $job['value'] ) ) {
		            $vl = join( ", ", $job['value'] );
	            } else {
		            $vl = $job['value'];
	            }
	            ?>
                <li>
                    <div class="icon"><i class="<?= $job['icon']; ?>"></i></div>
                    <div class="details">
                        <div class="text"><?= $job['text']; ?></div>
                        <div class="value"><?= $vl; ?></div>
                    </div>
                </li>
	            <?php
            }
            ?>
        </ul>
    </div>
	<?php
}

function fs_job_contact( $post_id ) {
	$email = get_field( 'email', $post_id );
	$phone = get_field( 'contact_number', $post_id );
	if ( ! $email && ! $phone ) {
		return;
	}
	?>
    <div class="job-contact">
        <h3 class="contact-title">Job Contact</h3>
        <ul class="job-contact-detail">
            <?= $email ? '<li><strong>Email address:</strong><a href="mailto:' . $email . '">' . $email . '</a></li>' : '' ?>
            <?= $phone ? '<li><strong>Phone number:</strong><a href="tel:' . $phone . '">' . $phone . '</a></li>' : '' ?>
        </ul>
    </div>
	<?php
}

function fs_job_related( $post_id ) { ?>
    <div class="jobs-related">
        <h3 class="job-related-title">Related Jobs</h3>
        <div class="job-related-items">

        </div>
    </div>
	<?php
}

function fs_job_photos( $post_id ) {
	?>
    <div class="job-photos">
        <h4 class="__job-title __photo-title">Photos</h4>
        <div class="__photos-grid">
            <?php
            if ( have_rows( 'upload_your_image' ) ):
	            while ( have_rows( 'upload_your_image' ) ) : the_row();
		            $photo = get_sub_field( 'photo_item' );
		            echo '<div class="photo-item"><a target="_blank" href="' . $photo . '"><img src="' . $photo . '" alt="photo"></a></div>';
	            endwhile;
            else :
	            echo '<p>Does not have any photos.</p>';
            endif;
            ?>
         </div>
    </div>
	<?php
}

/**
 * @param $post_id
 * @param $term
 * @param bool $arr
 *
 * @return array|string
 */
function get_term_list_by_post_id( $post_id, $term, $arr = false ) {
	$term_object_list = get_the_terms( $post_id, $term );
	$term_name_list   = wp_list_pluck( $term_object_list, 'name' );

	return $arr ? $term_name_list : join( ', ', $term_name_list );
}

/**
 * @return WP_Query
 */
function fs_create_query_jobs( $location, $compensation_type, $job_type, $job_experience, $title, $order_by ) {
	$attrs      = array(
		'post_type'      => 'job',
		'post_status'    => 'publish',
		'posts_per_page' => 500,
	);
	$meta_query = [];
	$tax_query  = [];
	if ( $location ) {
		$tax_query[] = array(
			'taxonomy' => 'job_location',
			'field'    => 'term_id',
			'terms'    => [ $location ],
		);
	}
	if ( $compensation_type ) {
		$tax_query[] = array(
			'taxonomy' => 'job_compensation_type',
			'field'    => 'term_id',
			'terms'    => [ $compensation_type ],
		);
	}
	if ( $job_type ) {
		$tax_query[] = array(
			'taxonomy' => 'job_type',
			'field'    => 'term_id',
			'terms'    => [ $job_type ],
		);
	}
	if ( $job_experience ) {
		$tax_query[] = array(
			'taxonomy' => 'job_experience',
			'field'    => 'term_id',
			'terms'    => [ $job_experience ],
		);
	}
	if ( ! empty( $tax_query ) ) {
		$tax_query['relation'] = 'AND';
		$attrs['tax_query']    = $tax_query;
	}
	if ( ! empty( $meta_query ) ) {
		$tax_query['relation'] = 'AND';
		$attrs['meta_query']   = $meta_query;
	}

	switch ( $order_by ) {
		case "newest":
			$attrs['order']   = "DESC";
			$attrs['orderby'] = "date";
			break;
		case "oldest":
			$attrs['order']   = "ASC";
			$attrs['orderby'] = "date";
			break;
		case "random":
			$attrs['orderby'] = "rand";
			break;
	}
	if ( $title ) {
		$attrs['s'] = $title;
	}

	return new WP_Query( $attrs );
}

function fs_get_job_item( $post_id ) { ?>
    <div class="__job-item <?= fs_is_job_expiration( get_field( 'expiration_date', $post_id ) ) ? '_is-expiration' : '' ?>">
        <div class="__job-item-inner">
            <div class="__inner-wrap">
                <div class="__item-top">
                    <div class="__avatar">
                        <a href="<?= get_the_permalink( $post_id ) ?>">
                            <img src="<?php fs_get_business_avatar_uri_by_post_id( $post_id ) ?>" alt="avatar">
                        </a>
                    </div>
                    <div class="__name">
                        <a href="<?= get_the_permalink( $post_id ) ?>">
                            <?php fs_get_business_name_by_post_id( $post_id ) ?>
                        </a>
                    </div>
                </div>
                <div class="__item-meta">
                    <h3 class="job-title"><a href="<?= get_the_permalink( $post_id ) ?>" rel="bookmark"><?= strip_tags( get_the_title( $post_id ) ) ?></a></h3>
                    <div class="job-salary">
                        <?= fs_get_job_salary( $post_id ) ?>
                    </div>
                    <div class="__job-info">
	                    <?= get_term_list_by_post_id( $post_id, 'job_type' ) ? '<div class="job-type">' . get_term_list_by_post_id( $post_id, 'job_type' ) . '</div>' : '' ?>
	                    <?= get_term_list_by_post_id( $post_id, 'job_location' ) ? '<div class="job-category">' . get_term_list_by_post_id( $post_id, 'job_location' ) . '</div>' : '' ?>
	                    <?= get_term_list_by_post_id( $post_id, 'job_experience' ) ? '<div class="job-exp">' . get_term_list_by_post_id( $post_id, 'job_experience' ) . '</div>' : '' ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<?php
}

add_action( 'wp_ajax_lt_ajax_filter_jobs', 'lt_ajax_filter_jobs' );
add_action( 'wp_ajax_nopriv_lt_ajax_filter_jobs', 'lt_ajax_filter_jobs' );
function lt_ajax_filter_jobs() {
	// First check the nonce, if it fails the function will break
	check_ajax_referer( "_security", 'security' );

	$job_location      = isset( $_POST['job-location'] ) ? intval( $_POST['job-location'] ) : '';
	$compensation_type = isset( $_POST['job-compensation-types'] ) ? intval( $_POST['job-compensation-types'] ) : '';
	$job_type          = isset( $_POST['job-type'] ) ? intval( $_POST['job-type'] ) : '';
	$job_experience    = isset( $_POST['job-experience'] ) ? intval( $_POST['job-experience'] ) : '';
	$title             = isset( $_POST['filter-title'] ) ? ( $_POST['filter-title'] ) : '';
	$order_by          = isset( $_POST['order-by'] ) ? ( $_POST['order-by'] ) : '';
	$filter_link       = isset( $_POST['filter_link'] ) ? ( $_POST['filter_link'] ) : '';

	$query     = fs_create_query_jobs( $job_location, $compensation_type, $job_type, $job_experience, $title, $order_by );
	$not_found = '<div class="__job-item item-not-found">Sorry, no jobs matched your criteria.</div>';

	ob_start();
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			fs_get_job_item( get_the_ID() );
		}
		// Restore original Post Data
		wp_reset_postdata();
	} else {
		echo $not_found;
	}
	$items           = ob_get_clean();
	$items           = empty( $items ) ? $not_found : $items;
	$selected_filter = fs_build_remove_selected_link( $filter_link, [
		'job-location'           => $job_location,
		'job-compensation-types' => $compensation_type,
		'job-type'               => $job_type,
		'job-experience'         => $job_experience,
		'filter-title'           => $title,
		'order-by'               => $order_by
	] );

	wp_send_json( [
		"items"           => $items,
		"selected_filter" => $selected_filter,
		"found_posts"     => $query->found_posts,
	] );

	wp_die();
}

function fs_job_compensation_types_select_box( $selected ) {
	$job_compensation_types = get_terms( array(
		'taxonomy'   => 'job_compensation_type',
		'hide_empty' => false,
		'fields'     => 'id=>name'
	) );
	?>
    <select name="job-compensation-types" class="form-control">
        <option value="">Compensation type</option>
		<?php foreach ( $job_compensation_types as $key_compensation_type => $compensation_type ) { ?>
            <option <?= $key_compensation_type == $selected ? "selected" : '' ?> value="<?= $key_compensation_type ?>"><?= $compensation_type ?></option>
		<?php } ?>
    </select>
	<?php
}

function fs_job_types_select_box( $selected ) {
	$job_types = get_terms( array(
		'taxonomy'   => 'job_type',
		'hide_empty' => false,
		'fields'     => 'id=>name'
	) );
	?>
    <select name="job-type" class="form-control">
        <option value="">Job type..</option>
		<?php foreach ( $job_types as $key_type => $type ) { ?>
            <option <?= $key_type == $selected ? "selected" : '' ?> value="<?= $key_type ?>"><?= $type ?></option>
		<?php } ?>
    </select>
	<?php
}

function fs_job_locations_select_box( $selected ) {
	$job_locations = get_terms( array(
		'taxonomy'   => 'job_location',
		'hide_empty' => false,
		'fields'     => 'id=>name'
	) ); ?>
    <select name="job-location" class="form-control">
        <option value="">City, state, or location</option>
		<?php foreach ( $job_locations as $key_location => $location ) { ?>
            <option <?= $key_location == $selected ? "selected" : '' ?> value="<?= $key_location ?>"><?= $location ?></option>
		<?php } ?>
    </select>
	<?php
}

function fs_job_experiences_select_box( $selected ) {
	$job_experiences = get_terms( array(
		'taxonomy'   => 'job_experience',
		'hide_empty' => false,
		'fields'     => 'id=>name'
	) ); ?>
    <select name="job-experience" class="form-control">
        <option value="">Experiences</option>
		<?php foreach ( $job_experiences as $key_experience => $experience ) { ?>
            <option <?= $key_experience == $selected ? "selected" : '' ?> value="<?= $key_experience ?>"><?= $experience ?></option>
		<?php } ?>
    </select>
	<?php
}

function fs_build_remove_selected_link( $url, $params = [] ) {
	ob_start();
	foreach ( $params as $key => $val ) {
		if ( $val ) {
			$filter = array_filter( $params, function ( $p_val, $p_key ) use ( $key ) {
				return $p_key !== $key && ( ! ! $p_val );
			}, ARRAY_FILTER_USE_BOTH );

			$link = $url . "?" . build_query( $filter );
			$text = _get_text_remove_by_selected( $key, $val );
			if ( $text ) { ?>
                <li><a href="<?= $link ?>"><span class="close-value">x</span><?= $text ?></a></li>
				<?php
			}
		}
	}

	return ob_get_clean();
}

/**
 * @param $key
 * @param $val
 *
 * @return array|false|mixed|string|WP_Error|WP_Term|null
 */
function _get_text_remove_by_selected( $key, $val ) {
	switch ( $key ) {
		case "job-location":
			$text = get_term_by( 'term_id', intval( $val ), 'job_location' );
			$text = ! ! $text ? $text->name : '';
			break;
		case "job-compensation-types":
			$text = get_term_by( 'term_id', intval( $val ), 'job_compensation_type' );
			$text = ! ! $text ? $text->name : '';
			break;
		case "job-type":
			$text = get_term_by( 'term_id', intval( $val ), 'job_type' );
			$text = ! ! $text ? $text->name : '';
			break;
		case "job-experience":
			$text = get_term_by( 'term_id', intval( $val ), 'job_experience' );
			$text = ! ! $text ? $text->name : '';
			break;
		case "filter-title":
			$text = $val;
			break;
		case "order-by":
			$text = ucfirst( $val );
			break;
		default:
			$text = '';
			break;
	}

	return $text;
}

function fs_jobs_filter_top(
	$current_link, $job_location, $compensation_type, $job_type, $job_experience, $title
) {
	?>
    <div class="__job-filter-top-inner max-960">
        <h2><?= get_the_title() ?></h2>
        <h3>Awesome jobs for awesome people</h3>
        <form id="filter-listing" action="<?= $current_link ?>" class="form-search filter-listing-form " method="GET">
            <div class="search-form-inner">
                <div class="main-inner">
                    <div class="form-item">
                        <div class="--inner has-icon">
                            <i class="flaticon-loupe"></i>
                            <input type="text" name="filter-title" class="form-control" value="<?= $title ?>" placeholder="Job, title, skills, or keywords">
                        </div>
                    </div>
                    <div class="form-item">
                        <div class="--inner">
                            <?php fs_job_locations_select_box( $job_location ); ?>
                        </div>
                    </div>
                    <div class="form-item">
                        <div class="--inner">
                            <?php fs_job_types_select_box( $job_type ); ?>
                        </div>
                    </div>
                    <div class="form-item">
                        <div class="--inner">
                            <?php fs_job_compensation_types_select_box( $compensation_type ); ?>
                        </div>
                    </div>
                    <div class="form-item">
                        <div class="--inner">
                            <?php fs_job_experiences_select_box( $job_experience ); ?>
                        </div>
                    </div>
                    <div class="form-item">
                        <div class="--inner">
                            <button class="btn-submit-filter" type="submit">Search </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
	<?php
}

function fs_get_job_salary( $post_id ) {
	$arr = [];
	if ( get_term_list_by_post_id( $post_id, 'job_compensation' ) ) {
		$arr[] = get_term_list_by_post_id( $post_id, 'job_compensation' );
	}
	if ( get_term_list_by_post_id( $post_id, 'job_compensation_type' ) ) {
		$arr[] = get_term_list_by_post_id( $post_id, 'job_compensation_type' );
	}

	return join( ' / ', $arr );
}
