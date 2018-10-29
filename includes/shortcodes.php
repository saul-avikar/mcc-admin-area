<?php
/**
 * Author: Saul Boyd (avikar.io)
 * License: MIT (https://opensource.org/licenses/MIT)
 */
add_shortcode( 'login_form', 'loginForm_func' );
add_shortcode( 'post_form', 'postForm_func' );
add_shortcode( 'pending_posts', 'pendingPosts_func' );
add_shortcode( 'notice_board', 'noticeBoard_func' );

function js_redirect_home( $location = "/" ) {
	$string = '<script type="text/javascript">';
	$string .= 'if (window.location.pathname !== "' . $location . '")';
	$string .= 'window.location = "' . $location . '";';
	$string .= '</script>';

	echo $string;
}

// Login
function loginForm_func ( $atts ){
	ob_start();

	require_once( plugin_dir_path( __FILE__ ) . '../templates/login.php' );

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
