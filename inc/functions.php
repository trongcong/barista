<?php
/**
 * Created by NTC
 */
add_action( 'wp_enqueue_scripts', 'lt_enqueue_scripts' );
function lt_enqueue_scripts() {
	wp_enqueue_style( 'lt-style', get_stylesheet_directory_uri() . '/inc/assets/css/lt-main.min.css', array(), WP_DEBUG ? rand() : "1.1" );
	wp_enqueue_script( 'lt-script', get_stylesheet_directory_uri() . '/inc/assets/js/lt-main.min.js', array(
		'jquery',
	), WP_DEBUG ? rand() : "1.1", true );

	wp_localize_script( 'lt-script', 'ajax_data', [
		'admin_logged' => in_array( 'administrator', wp_get_current_user()->roles ) ? 'yes' : 'no',
		'ajax_url'     => admin_url( 'admin-ajax.php' ),
		'tpd_uri'      => get_template_directory_uri(),
		'site_url'     => site_url(),
		'rest_url'     => get_rest_url(),
		'_ajax_nonce'  => wp_create_nonce( "_security" ),
		'post_id'      => get_the_ID(),
	] );
}

function get_lt_item( $id ) {
	$avatar            = get_field( "your_avatar", $id );
	$re_active_profile = get_field( 're_active_profile', $id );
	$re_active_profile = $re_active_profile ? $re_active_profile : get_the_date( 'c' );
	$exp               = get_number_of_days_from_date_to_now( $re_active_profile );
	$can_do            = intval( $exp ) > 7 ? "__can-do" : "";
	?>
    <div class="__lt-item <?= $can_do; ?>">
        <div class="__lt-item-inner">
            <div class="__inner-wrap">
                <div class="__item-top">
                <div class="__avatar">
                    <a href="<?= get_the_permalink( $id ) ?>"><img src="<?= ! empty( $avatar ) ? wp_get_attachment_image_url( $avatar['ID'] ) : 'https://via.placeholder.com/80x80.png' ?>" alt="avatar"></a>
                </div>
                <div class="__name-wrap">
                    <div class="__name"><a href="<?= get_the_permalink( $id ) ?>"><?= get_the_title( $id ) ?></a></div>
	                <?php
	                $certification = get_certification_by_barista( $id );
	                if ( ! empty( $certification ) ) {
		                foreach ( $certification as $cer ) {
			                echo '<div class="__exp">🏅' . $cer . '</div>';
		                }
	                }
	                ?>
                </div>
                </div>
                <div class="__item-meta">
                    <div class="__num-exp">
                        Barista Experience (Years): <strong><?= get_field( "years_of_experience", $id )['value'] ?></strong>
                    </div>
                    <div class="__viewed">
                        Viewed: <strong><?= get_barista_view( $id ) ?></strong>
                    </div>
                    <div class="__num-exp-aus">
                        Retail or Hospitality Experience: <strong><?= get_field( "experience_in_australia", $id )['value'] ?></strong>
                    </div>
                    <div class="__contacted">
                        Contacted: <strong><?= get_barista_contacted( $id ) ?></strong>
                    </div>
                </div>
                <div class="__description">
                    <?= get_field( "describe_yourself_in_2_sentences", $id ) ?>
                </div>
            </div>
            <div class="__detail">
                <a href="<?= get_the_permalink( $id ) ?>">
                    <?php
                    if ( intval( $exp ) > 7 ) {
	                    echo "Click Here To See Everything I Can Do";
                    } else {
	                    echo "I am still looking for more work. Contact me";
                    }
                    ?>
                </a>
            </div>
        </div>
    </div>
	<?php
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

function get_video_url( $postID ) {
	$video_url = get_field( "upload_your_video", $postID );
	preg_match( "#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $video_url, $matches );

	return isset( $matches[0] ) ? $matches[0] : $video_url;
}

/**
 * @param string $file_key
 * @param $post_id
 *
 * @return int|WP_Error|null
 */
function upload_file_to_media( $file_key, $post_id ) {
	$fileName      = preg_replace( '/\s+/', '-', $_FILES[ $file_key ]["name"] );
	$fileName      = preg_replace( '/[^A-Za-z0-9.\-]/', '', $fileName );
	$upload        = wp_upload_bits( $fileName, null, file_get_contents( $_FILES[ $file_key ]["tmp_name"] ) );
	$attachment_id = wp_insert_attachment( array(
		'guid'           => $upload['url'],
		'post_mime_type' => $upload['type'],
		'post_title'     => basename( $upload['file'] ),
		'post_content'   => '',
		'post_status'    => 'inherit',
	), $upload['file'] );

	// update medatata, regenerate image sizes
	require_once( ABSPATH . 'wp-admin/includes/image.php' );

	wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );
	update_field( $file_key, $attachment_id, $post_id );

	return $attachment_id;
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

/**
 * @param $field
 */
function render_tag_from_acf_fields( $field ) {
	$label_required = $field['required'] ? "<span class='__label-required'> *</span>" : "";
	$class_required = $field['required'] ? "__required" : "";
	echo '<div class="__ltrg-item">';
	if ( $field['type'] == "checkbox" ) { ?>
        <div class="__lt-checkbox-group <?= $class_required ?>">
            <span><?= $field['label'] ?><?= $label_required ?></span>
			<?php
			foreach ( $field['choices'] as $key => $choice ) { ?>
                <div class="__lt-checkbox">
                    <label>
                        <input name="<?= $field['name'] ?>[]" type="checkbox" value="<?= $key ?>" />
                        <span><?= $choice ?></span>
                    </label>
                </div>
			<?php } ?>
        </div>
		<?php
	} elseif ( $field['type'] == "select" ) { ?>
        <div class="__lt-input-select <?= $class_required ?>">
            <label>
                <span><?= $field['label'] ?><?= $label_required ?></span>
                <select name="<?= $field['name'] ?>" <?= $field['required'] ? 'required' : '' ?>>
                    <?php foreach ( $field['choices'] as $key => $choice ) {
	                    echo "<option value='" . $key . "'>" . $choice . "</option>";
                    } ?>
                </select>
            </label>
        </div>
		<?php
	} elseif ( in_array( $field['type'], [ "textarea" ] ) ) { ?>
        <div class="__lt-input <?= $class_required ?>">
            <label>
                <span><?= $field['label'] ?><?= $label_required ?></span>
                <textarea maxlength="<?= $field['maxlength'] ?>" name="<?= $field['name'] ?>" <?= $field['required'] ? 'required' : '' ?>></textarea>
            </label>
        </div>
		<?php
	} elseif ( in_array( $field['type'], [
		"number",
		"text",
		"file",
		"image",
		"email",
		"url",
		"password"
	] ) ) {
		$type          = "image" == $field['type'] ? "file" : $field['type'];
		$accept_avatar = 'accept="image/*"';
		$accept_resume = 'accept="image/*, .doc, .docx, .pdf"';
		$mime_types    = isset( $field["mime_types"] ) && $field["mime_types"] ? 'accept="' . $field["mime_types"] . '"' : "";
		$mime_types    = "image" == $field['type'] ? $accept_avatar : ( "file" == $field['type'] ? $accept_resume : $mime_types );
		?>
        <div class="__lt-input <?= $class_required ?>">
            <label>
                <span><?= $field['label'] ?><?= $label_required ?></span>
                <input <?= $mime_types ?> name="<?= $field['name'] ?>" type="<?= $type ?>" placeholder="" <?= $field['required'] ? 'required' : '' ?>/>
            </label>
        </div>
		<?php
	} else {
		echo "<!-- " . $field['type'] . " -->";
	}
	echo '</div>';
}

function render_tag_from_job_taxonomies() {
	$taxonomies     = get_object_taxonomies( 'job', 'objects' );
	$label_required = "<span class='__label-required'> *</span>";
	foreach ( $taxonomies as $tax => $taxonomy ) {
		$terms = get_terms( array(
			'taxonomy'   => $tax,
			'hide_empty' => false,
			'fields'     => 'id=>name'
		) );

		if ( ! empty( $terms ) ) { ?>
            <div class="__ltrg-item">
                <div class="__lt-checkbox-group">
                    <span><?= $taxonomy->label ?></span>
	                <?php
	                foreach ( $terms as $key_term => $term ) { ?>
                        <div class="__lt-checkbox">
                            <label>
                                <input name="<?= $tax ?>[]" type="checkbox" value="<?= $key_term ?>" />
                                <span><?= $term ?></span>
                            </label>
                        </div>
	                <?php } ?>
                </div>
            </div>
			<?php
		}
	}
}

/**
 * @param $from
 *
 * @return float
 */
function get_number_of_days_from_date_to_now( $from ) {
	$now       = time();
	$your_date = strtotime( $from );
	$date_diff = $now - $your_date;

	return round( $date_diff / ( 60 * 60 * 24 ) );
}

function lt_filter_group() {
	$barista_skills     = acf_get_field( "field_625cf654af1dc" )["choices"];
	$volumes            = acf_get_field( "field_62887394b3a58" )["choices"];
	$hospitality_skills = acf_get_field( "field_625cf65daf1de" )["choices"];
	?>
    <div class="__lt-filter-group">
        <h4 class="__lt-filter-title">First Shot Barista Training Certification</h4>
        <div class="__lt-filter-checkbox-group">
            <div class="__lt-checkbox">
                <label>
                    <input name="training_certification[]" type="checkbox" value="Professional Training" />
                    <span>Professional Training</span>
                </label>
            </div>
            <div class="__lt-checkbox">
                <label>
                    <input name="training_certification[]" type="checkbox" value="Basic Sensory" />
                    <span>Basic Sensory</span>
                </label>
            </div>
            <div class="__lt-checkbox">
                <label>
                    <input name="training_certification[]" type="checkbox" value="Advanced Sensory" />
                    <span>Advanced Sensory</span>
                </label>
            </div>
        </div>
    </div>

    <div class="__lt-filter-group">
        <h4 class="__lt-filter-title">Barista Experience (Years)</h4>
        <div class="__lt-range-slider">
          <input name="year_exp_min" type="range" min="0.5" max="10" step="0.5" value="0.5" class="__lt-range-slider__input" />
          <input name="year_exp_max" type="range" min="0.5" max="10" step="0.5" value="10" class="__lt-range-slider__input" />
          <div class="__lt-range-slider__display">
            <!-- This node is optional and only used to display the current values -->
          </div>
        </div>
    </div>

    <div class="__lt-filter-group">
        <h4 class="__lt-filter-title">Retail or Hospitality Experience</h4>
        <div class="__lt-range-slider">
          <input name="year_exp_aus_min" type="range" min="0.5" max="10" step="0.5" value="0.5" class="__lt-range-slider__input" />
          <input name="year_exp_aus_max" type="range" min="0.5" max="10" step="0.5" value="10" class="__lt-range-slider__input" />
          <div class="__lt-range-slider__display">
            <!-- This node is optional and only used to display the current values -->
          </div>
        </div>
    </div>

    <div class="__lt-filter-group">
        <h4 class="__lt-filter-title">Barista skills</h4>
        <div class="__lt-filter-checkbox-group">
            <?php foreach ( $barista_skills as $skill ) { ?>
                <div class="__lt-checkbox">
                    <label>
                        <input name="barista_skills[]" type="checkbox" value="<?= trim( $skill ) ?>" />
                        <span><?= trim( $skill ) ?></span>
                    </label>
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="__lt-filter-group">
        <h4 class="__lt-filter-title">Volume</h4>
        <div class="__lt-filter-checkbox-group">
            <?php foreach ( $volumes as $v ) { ?>
                <div class="__lt-checkbox">
                    <label>
                        <input name="volumes[]" type="checkbox" value="<?= $v ?>" />
                        <span><?= $v ?></span>
                    </label>
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="__lt-filter-group">
        <h4 class="__lt-filter-title">Hospitality skills</h4>
        <div class="__lt-filter-checkbox-group">
            <?php foreach ( $hospitality_skills as $v ) { ?>
                <div class="__lt-checkbox">
                    <label>
                        <input name="hospitality_skills[]" type="checkbox" value="<?= $v ?>" />
                        <span><?= $v ?></span>
                    </label>
                </div>
            <?php } ?>
        </div>
    </div>
	<?php
}

function lt_filter_modal() { ?>
    <div class="__lt-filter-modal" style="display: none">
        <div class="__lt-modal-backdrop"></div>
        <div class="__filter-popup-content">
            <div class="__filter-popup-inner">
                <div class="__lt-by"><span>Filter by<span class="__counter-result"></span></span>
                    <button type="button" class="__lt-modal-close">
                        <span class="sr-only">Close the dialog</span>
                        <span class="up-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" viewBox="0 0 14 14" role="img">
                                <polygon fill-rule="evenodd" points="12.524 0 7 5.524 1.476 0 0 1.476 5.524 7 0 12.524 1.476 14 7 8.476 12.524 14 14 12.524 8.476 7 14 1.476"></polygon>
                            </svg>
                        </span>
                    </button>
                </div>
                <div class="__filter-popup-group">
                    <?php lt_filter_group() ?>
                </div>
            </div>
        </div>
    </div>
	<?php
}

add_role( 'barista', __( 'Barista' ), array() );
add_role( 'business', __( 'Business' ), array() );
