<?php
/**
 * Template for the account page
 *
 * This template can be overridden by copying it to yourtheme/ultimate-member/templates/account.php
 *
 * Page: "Account"
 *
 * @version 2.8.0
 *
 * @var string $mode
 * @var int    $form_id
 * @var array  $args
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$role = um_profile( 'role' );

if ( $role === fs_role_business() ) {
	require get_stylesheet_directory() . '/ultimate-member/templates/_account-business.php';
} elseif ( $role === fs_role_barista() ) {
	require get_stylesheet_directory() . '/ultimate-member/templates/_account-barista.php';
} else {
	require get_stylesheet_directory() . '/ultimate-member/templates/_account.php';
}
