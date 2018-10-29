<?php
/**
 * Author: Saul Boyd (avikar.io)
 * License: MIT (https://opensource.org/licenses/MIT)
 */

if ( is_user_logged_in() ) {
	global $current_user;
	get_currentuserinfo();

	if ( user_can( $current_user, 'mccadminarea_teacher' ) ) {
		js_redirect_home( get_option( 'mccadminarea_teacherloginredirect' ) );
	} else {
		js_redirect_home( get_option( 'mccadminarea_studentloginredirect' ) );
	}
} else {
	?>
	<div class="MCCAdminArea-login-fail MCCAdminArea-hidden">
		Login Failed.
	</div>
	<?php
	wp_login_form();
}
