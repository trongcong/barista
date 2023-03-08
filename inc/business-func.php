<?php


//function um_custom_validate_username_nickname( $args ) {
//	if ( isset( $args['business_name'] ) && ! $args['business_name'] ) {
//		UM()->form()->add_error( 'business_name', 'Your username and nickname can not be equal.' );
//	}
//}
//
//add_action( 'um_submit_form_errors_hook_', 'um_custom_validate_username_nickname', 999, 1 );

//add_action( 'um_custom_field_validation__business_name', 'fs_custom_validate_business_name', 999, 3 );
//function fs_custom_validate_business_name( $key, $array, $args ) {
//	var_dump($key);
//	if ( isset( $args[ $key ] ) && ! preg_match( '/^[6-9]\d{9}$/', $args[ $key ] ) ) {
//		UM()->form()->add_error( $key, __( 'Please enter valid Mobile Number.', 'ultimate-member' ) );
//	}
//}

add_action( 'um_after_account_general', 'fs_show_um_extra_fields', 100 );
function fs_show_um_extra_fields() {
	if ( current_user_can( fs_role_business() ) ) {
		$id     = um_user( 'ID' );
		$output = '';
		$names  = array( 'phone_number', 'business_name' );

		$fields = array();
		foreach ( $names as $name ) {
			$fields[ $name ] = UM()->builtin()->get_specific_field( $name );
		}
		$fields = apply_filters( 'um_account_secure_fields', $fields, $id );
		foreach ( $fields as $key => $data ) {
			$output .= UM()->fields()->edit_field( $key, $data, true );
		}

		echo $output;
	}
}

add_action( 'um_account_pre_update_profile', 'fs_save_um_form_data', 100 );
function fs_save_um_form_data() {
	if ( current_user_can( fs_role_business() ) ) {
		$id    = um_user( 'ID' );
		$names = array( 'phone_number', 'business_name' );

		foreach ( $names as $name ) {
			update_user_meta( $id, $name, $_POST[ $name ] );
		}
	}
}

function fs_get_business_name_by_post_id( $post_id ) {
	$user_id      = get_post_field( 'post_author', $post_id );
	$display_name = get_the_author_meta( 'display_name', $user_id );
	um_fetch_user( $user_id );
	$role = um_profile( 'role' );

	switch ( $role ) {
		case fs_role_barista():
		case fs_role_admin():
			$business_name = $display_name;
			break;
		case fs_role_business():
			$business_name = um_profile( 'business_name' );
			if ( empty( $business_name ) ) {
				$business_name = $display_name;
			}
			break;
		default:
			$business_name = 'Unknown';
			break;
	}
	$business_name = empty( $business_name ) ? 'Unknown' : $business_name;

	echo $business_name;
	um_reset_user();
}

function fs_get_business_avatar_uri_by_post_id( $post_id ) {
	$author_id = get_post_field( 'post_author', $post_id );
	//	$user_email = get_the_author_meta( 'user_email', $author_id );
	//	$default    = um_get_user_avatar_url( $author_id, 120 );
	//	$rands      = [ 'mp', 'identicon', 'monsterid', 'wavatar', 'retro', 'robohash' ];
	//	$key        = array_rand( $rands );
	//	$hash       = md5( strtolower( trim( $user_email ) ) );
	//	$avatar_uri = 'https://www.gravatar.com/avatar/' . $hash . '?s=120&d=' . $rands[ $key ];

	echo um_get_user_avatar_url( $author_id, 120 );
}
