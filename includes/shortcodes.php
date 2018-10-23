<?php
/**
 * Author: Saul Boyd (avikar.io)
 * License: MIT (https://opensource.org/licenses/MIT)
 */
add_shortcode( 'login_form', 'loginForm_func' );
add_shortcode( 'post_form', 'postForm_func' );
add_shortcode( 'pending_posts', 'pendingPosts_func' );
add_shortcode( 'notice_board', 'noticeBoard_func' );

// Login
function loginForm_func ( $atts ){
	ob_start();

	if ( is_user_logged_in() ) {
		?>
			<!-- You must be logged out to log in. -->
		<?php
	} else {
		// require_once( plugin_dir_path( __FILE__ ) . '../templates/login.php' );
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
		?>
			<!-- You must be logged in to post pages. -->
		<?php
	}

	return ob_get_clean();
}

// Pending Posts
function pendingPosts_func ( $atts ){
	ob_start();

	if ( is_user_logged_in() ) {
		require_once( plugin_dir_path( __FILE__ ) . '../templates/pending-posts.php' );
	} else {
		?>
			<!-- You must be logged in to approve posts. -->
		<?php
	}

	return ob_get_clean();
}

// Upcoming events widget
function noticeBoard_func ( $atts ){
	ob_start();

	require_once( plugin_dir_path( __FILE__ ) . '../templates/notice-board.php' );

	return ob_get_clean();
}
