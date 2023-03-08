<?php if ( ! defined( 'ABSPATH' ) ) {
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
