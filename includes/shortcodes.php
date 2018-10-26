<?php
/**
 * Author: Saul Boyd (avikar.io)
 * License: MIT (https://opensource.org/licenses/MIT)
 */
add_shortcode( 'login_form', 'loginForm_func' );
add_shortcode( 'post_form', 'postForm_func' );
add_shortcode( 'pending_posts', 'pendingPosts_func' );
add_shortcode( 'notice_board', 'noticeBoard_func' );

function js_redirect_home() {
	$string = '<script type="text/javascript">';
    $string .= 'if (window.location.pathname !== "/")';
    $string .= 'window.location = "/";';
    $string .= '</script>';

    echo $string;
}

// Login
function loginForm_func ( $atts ){
	ob_start();

	if ( is_user_logged_in() ) {
		js_redirect_home();
	} else {
		?>
		<div class="MCCAdminArea-login-fail MCCAdminArea-hidden">
			Login Failed.
		</div>
		<?php
		wp_login_form();
	}

	return ob_get_clean();
}

// Posts
function postForm_func ( $atts ){
	ob_start();

	if ( is_user_logged_in() ) {
		require_once( plugin_dir_path( __FILE__ ) . '../templates/post-form.php' );
	} else {
		js_redirect_home();
	}

	return ob_get_clean();
}

// Pending Posts
function pendingPosts_func ( $atts ){
	ob_start();

	if ( is_user_logged_in() ) {
		require_once( plugin_dir_path( __FILE__ ) . '../templates/pending-posts.php' );
	} else {
		js_redirect_home();
	}

	return ob_get_clean();
}

// Upcoming events widget
function noticeBoard_func ( $atts ){
	ob_start();

	require_once( plugin_dir_path( __FILE__ ) . '../templates/notice-board.php' );

	return ob_get_clean();
}
