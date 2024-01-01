<?php
/**
 * Created by NTC
 */
add_action( 'wp_enqueue_scripts', 'lt_enqueue_scripts' );
function lt_enqueue_scripts() {
	wp_enqueue_style( 'lt-style', get_stylesheet_directory_uri() . '/inc/assets/css/lt-main.min.css', array(), WP_DEBUG ? rand() : "1.2" );
	wp_enqueue_script( 'lt-script', get_stylesheet_directory_uri() . '/inc/assets/js/lt-main.min.js', array(
		'jquery',
	), WP_DEBUG ? rand() : "1.3", true );

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

function fs_role_barista() {
	return 'um_barista';
}

function fs_role_business() {
	return 'um_business';
}

function fs_role_admin() {
	return 'administrator';
}

function get_lt_item( $id ) {
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
						<a href="<?= get_the_permalink( $id ) ?>">
							<img src="<?= get_barista_avatar( $id ) ?>" alt="avatar">
						</a>
					</div>
					<div class="__name-wrap">
						<div class="__published">Activated: <?= get_the_date( 'd M Y', $id ) ?></div>
						<div class="__name"><a href="<?= get_the_permalink( $id ) ?>"><?= get_the_title( $id ) ?></a>
						</div>
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
						Barista (Years):
						<strong><?= get_field( "years_of_experience", $id )['value'] ?></strong>
					</div>
					<div class="__viewed">
						Viewed: <strong><?= get_barista_view( $id ) ?></strong>
					</div>
					<div class="__num-exp-aus">
						Retail or Hospo (Years):
						<strong><?= get_field( "experience_in_australia", $id )['value'] ?></strong>
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
						echo "This barista might have already got a job 🤝";
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

function get_video_url( $postID ) {
	$video_url = get_field( "upload_your_video", $postID );
	$patterns  = [
		'/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/',
		'/(?:youtube\.com\/shorts\/|youtu\.be\/)([a-zA-Z0-9_-]{11})/',
	];
	$videoId   = null;

	foreach ( $patterns as $pattern ) {
		preg_match( $pattern, $video_url, $matches );
		if ( isset( $matches[1] ) ) {
			$videoId = $matches[1];
			break;
		}
	}

	return $videoId;
}

/**
 * @param $name
 * @param $tmp_name
 *
 * @return int|WP_Error|null
 */
function upload_file_to_media( $name, $tmp_name ) {
	$fileName      = preg_replace( '/\s+/', '-', $name );
	$fileName      = preg_replace( '/[^A-Za-z0-9.\-]/', '', $fileName );
	$upload        = wp_upload_bits( $fileName, null, file_get_contents( $tmp_name ) );
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

	return $attachment_id;
}

/**
 * @param $field
 */
function render_tag_from_acf_fields( $field ) {
	$label_required = $field['required'] ? "<span class='__label-required'> *</span>" : "";
	$class_required = $field['required'] ? "__required" : "";
	echo '<div class="__ltrg-item __field-' . $field['type'] . '">';
	if ( $field['type'] == "checkbox" ) { ?>
		<div class="__lt-checkbox-group <?= $class_required ?>">
			<span><?= $field['label'] ?><?= $label_required ?></span>
			<?php
			foreach ( $field['choices'] as $key => $choice ) { ?>
				<div class="__lt-checkbox">
					<label>
						<input name="<?= $field['name'] ?>[]" type="checkbox" value="<?= $key ?>"/>
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
				<textarea maxlength="<?= $field['maxlength'] ?>"
				          name="<?= $field['name'] ?>" <?= $field['required'] ? 'required' : '' ?>></textarea>
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
				<input <?= $mime_types ?> name="<?= $field['name'] ?>" type="<?= $type ?>"
				                          placeholder="" <?= $field['required'] ? 'required' : '' ?>/>
			</label>
		</div>
		<?php
	} elseif ( $field['type'] == "repeater" && ( $field['name'] == 'your_photos' || $field['name'] == 'upload_your_image' ) ) { ?>
		<div class="__lt-input <?= $class_required ?>">
			<label>
				<span><?= $field['label'] ?><?= $label_required ?></span>
				<input multiple accept="image/*" name="<?= $field['name'] ?>[]" type="file"
				       placeholder="" <?= $field['required'] ? 'required' : '' ?>/>
			</label>
		</div>
		<?php
	} elseif ( $field['type'] == "date_picker" ) { ?>
		<div class="__lt-input <?= $class_required ?>">
			<label>
				<span><?= $field['label'] ?><?= $label_required ?></span>
				<input name="<?= $field['name'] ?>" type="date"
				       placeholder="" <?= $field['required'] ? 'required' : '' ?>/>
			</label>
		</div>
		<?php
	} else {
		echo "<!-- " . $field['type'] . " -->";
	}
	echo '</div>';
}

function render_tag_from_job_taxonomies() {
	$taxonomies = get_object_taxonomies( 'job', 'objects' );
	echo '<div class="job-tax-wrap">';
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
								<input name="<?= $tax ?>[]" type="checkbox" value="<?= $key_term ?>"/>
								<span><?= $term ?></span>
							</label>
						</div>
					<?php } ?>
				</div>
			</div>
			<?php
		}
	}
	echo '</div>';
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
	$barista_skills          = acf_get_field( "field_625cf654af1dc" )["choices"];
	$volumes                 = acf_get_field( "field_62887394b3a58" )["choices"];
	$hospitality_skills      = acf_get_field( "field_625cf65daf1de" )["choices"];
	$training_certifications = acf_get_field( "field_6583018dc732d" )['choices'];
	?>
	<div class="__lt-filter-group">
		<h4 class="__lt-filter-title">First Shot Barista Training Certification</h4>
		<div class="__lt-filter-checkbox-group">
			<?php foreach ( $training_certifications as $cert ) { ?>
				<div class="__lt-checkbox">
					<label>
						<input name="training_certification[]" type="checkbox" value="<?= trim( $cert ) ?>"/>
						<span><?= trim( $cert ) ?></span>
					</label>
				</div>
			<?php } ?>
		</div>
	</div>

	<div class="__lt-filter-group">
		<h4 class="__lt-filter-title">Barista Experience (Years)</h4>
		<div class="__lt-range-slider">
			<input name="year_exp_min" type="range" min="0.5" max="10" step="0.5" value="0.5"
			       class="__lt-range-slider__input"/>
			<input name="year_exp_max" type="range" min="0.5" max="10" step="0.5" value="10"
			       class="__lt-range-slider__input"/>
			<div class="__lt-range-slider__display">
				<!-- This node is optional and only used to display the current values -->
			</div>
		</div>
	</div>

	<div class="__lt-filter-group">
		<h4 class="__lt-filter-title">Retail or Hospitality Experience</h4>
		<div class="__lt-range-slider">
			<input name="year_exp_aus_min" type="range" min="0.5" max="10" step="0.5" value="0.5"
			       class="__lt-range-slider__input"/>
			<input name="year_exp_aus_max" type="range" min="0.5" max="10" step="0.5" value="10"
			       class="__lt-range-slider__input"/>
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
						<input name="barista_skills[]" type="checkbox" value="<?= trim( $skill ) ?>"/>
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
						<input name="volumes[]" type="checkbox" value="<?= $v ?>"/>
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
						<input name="hospitality_skills[]" type="checkbox" value="<?= $v ?>"/>
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
                                <polygon fill-rule="evenodd"
                                         points="12.524 0 7 5.524 1.476 0 0 1.476 5.524 7 0 12.524 1.476 14 7 8.476 12.524 14 14 12.524 8.476 7 14 1.476"></polygon>
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
