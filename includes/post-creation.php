<?php

function error( $msg ) {
	die('{"error": true, "msg": "' . $msg . '"}');
}

function success( $msg = 'Success.') {
	die('{"error": false, "msg": "' . $msg . '"}');
}

function add_gallery ( $post_id ) {
	if ( isset( $_POST['gallery'] ) && $_POST['gallery'] ) {
		wp_update_post( [
			'ID' => $post_id,
			'post_content' => $_POST['content'] . '[gallery include="' . $_POST['gallery'] . '"]'
		] );
	}
}

function handle_single_image_upload ( $image_name ) {
	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	require_once( ABSPATH . 'wp-admin/includes/media.php' );

	$thumbnail_id = media_handle_upload( $image_name, 0 );

	if ( !is_wp_error( $thumbnail_id ) ) {
		$response = [
			'error' => false,
			'id' => $thumbnail_id,
			'src' => wp_get_attachment_image_src( $thumbnail_id )[0],
			'title' => get_the_title( $thumbnail_id )
		];

		die( json_encode( $response ) );
	} else {
		error("Failed to upload image.");
	}
}

if ( is_user_logged_in() ) {
	if (
		isset( $_POST['image_single_nonce'], $_FILES["image_single"] ) &&
		wp_verify_nonce( $_POST['image_single_nonce'], 'image_single' )
	){
		// Gallery or feature image 'form'
		handle_single_image_upload( 'image_single' );
	} elseif (
		(
			isset(
				$_POST['author_name'],
				$_POST['title'],
				$_POST['content']
			) &&
			$_POST['author_name'] &&
			$_POST['title'] &&
			$_POST['content']
		) ||
		(
			isset( $_POST['post_id'] ) &&
			$_POST['post_id']
		)
	) {
		// Post submission form -alters an existing post if $_POST['post_id'] is set
		global $current_user;
		get_currentuserinfo();

		$is_editing = isset( $_POST['post_id'] ) && $_POST['post_id'];

		if ( $is_editing && ! user_can( $current_user, 'mccadminarea_teacher') ) {
			error('Missing required permissions.');
		}

		// Post
		$post_args = [
			'comment_status' => 'closed',
			'ping_status' => 'closed',
			'post_status' => user_can( $current_user, 'mccadminarea_teacher') ? 'publish' : 'draft',
		];

		if ( isset( $_POST['title'] ) && $_POST['title'] ) {
			$post_args['post_title'] = $_POST['title'];
		}

		if ( isset( $_POST['content'] ) && $_POST['content'] ) {
			$post_args['post_content'] = $_POST['content'];
		}

		if ( $is_editing ) {
			$post_args['ID'] = $_POST['post_id'];
			$post_id = wp_update_post( $post_args );
		} else {
			$post_id = wp_insert_post( $post_args );
		}

		if ( isset( $post_id ) && !is_wp_error( $post_id ) ) {
			// Categories

			// Remove all existing categories
			wp_set_post_categories( $post_id, [], false);

			if ( user_can( $current_user, 'mccadminarea_teacher') ) {
				foreach ($_POST as $key => $value) {
					if (substr($key, 0, 4) === 'mcc_') {
						$cat_id = substr($value, 4, strlen($value));

						wp_set_post_categories( $post_id, $cat_id, true );
					}
				}
			} else {
				// kids zone cat
				$term = get_term_by( 'name', 'Kids Zone', 'category' );
				$cat_id = $term->term_id;

				if ( $cat_id ) {
					wp_set_post_categories( $post_id, $cat_id, true );
				}
			}

			// Name
			if ( isset( $_POST['author_name'] ) && $_POST['author_name'] ) {
				// If name is already set update it
				if ( ! add_post_meta( $post_id, 'author_name', $_POST['author_name'], true ) ) {
					update_post_meta( $post_id, 'author_name', $_POST['author_name'] );
				}
			}

			// Date
			if ( isset( $_POST['release_date'] ) && $_POST['release_date'] ) {
				// If date is already set update it
				if ( ! add_post_meta( $post_id, 'release_date', $_POST['release_date'], true ) ) {
					update_post_meta( $post_id, 'release_date', $_POST['release_date'] );
				}
			}

			// Feature image
			if ( isset( $_POST['feature'] ) && $_POST['feature'] ) {
				set_post_thumbnail( $post_id,  $_POST['feature'] );
			}

			// Gallery
			add_gallery ( $post_id );

			success();
		} else {
			error('Failed to create/update post.');
		}
	} else {
		error('Missing required parameters.');
	}
} else {
	error('You need to be logged in to post content.');
}
