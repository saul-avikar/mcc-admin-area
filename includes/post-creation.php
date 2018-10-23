<?php
// add_post_meta($post_id, '_thumbnail_id', $attach_id);

function error( $msg ) {
	die('{"error": true, "msg": "' . $msg . '"}');
}

function success( $msg = 'Success.') {
	die('{"error": false, "msg": "' . $msg . '"}');
}

if ( is_user_logged_in() ) {
	if (
		isset(
			$_POST['author_name'],
			$_POST['title'],
			$_POST['content'],
			$_POST['image_nonce']
		) &&
		$_POST['author_name'] &&
		$_POST['title'] &&
		$_POST['content'] &&
		wp_verify_nonce( $_POST['image_nonce'], 'image' ) &&
		!isset( $_POST['post_id'] )
	) {
		// Create Post

		global $current_user;
		get_currentuserinfo();

		// Post
		$post_id = wp_insert_post( array (
			'comment_status' => 'closed',
			'ping_status' => 'closed',
			'post_status' => user_can( $current_user, 'mccadminarea_teacher') ? 'publish' : 'draft',
			'post_title' => $_POST['title'],
			'post_content' => $_POST['content']
		) );

		if ( !is_wp_error( $post_id ) ) {
			// Categories
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
			add_post_meta($post_id, 'author_name', $_POST['author_name']);

			// Feature Image
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/media.php' );

			$thumbnail_id = media_handle_upload( 'image', $post_id );

			if ( !is_wp_error( $thumbnail_id ) ) {
				set_post_thumbnail( $post_id, $thumbnail_id );
			}

			// Gallery
			if (
				isset( $_POST['gallery_nonce'], $_FILES["gallery"] ) &&
				wp_verify_nonce( $_POST['gallery_nonce'], 'gallery' )
			) {
				$files = $_FILES["gallery"];
				$images_id = array();

				foreach ($files['name'] as $key => $value) {
					if ($files['name'][$key]) {
						$file = array(
							'name' => $files['name'][$key],
							'type' => $files['type'][$key],
							'tmp_name' => $files['tmp_name'][$key],
							'error' => $files['error'][$key],
							'size' => $files['size'][$key]
						);

						$_FILES = array ("upload_file" => $file);

						foreach ($_FILES as $file => $array) {
							$images_id[] = media_handle_upload( $file, $post_id );
						}
					}
				}

				add_post_meta($post_id, 'gallery', $images_id);

				wp_update_post( array (
					'ID' => $post_id,
					'post_content' => $_POST['content'] . '[gallery include="' . implode(",", $images_id) . '"]'
				) );
			}

			success();
		} else {
			error('Failed to create post.');
		}
	} elseif ( isset( $_POST['post_id'] ) && $_POST['post_id'] ) {
		// Aprove post
		if ( user_can( $current_user, 'mccadminarea_teacher') ) {
			$post_args = array(
				'ID' => $_POST['post_id'],
				'post_status' => 'publish'
			);

			if ( isset( $_POST['title'] ) && $_POST['title'] ) {
				$post_args['post_title'] = $_POST['title'];
			}
			if ( isset( $_POST['content'] ) && $_POST['content'] ) {
				$post_args['post_content'] = $_POST['content'];
			}
			$post_id = wp_update_post( $post_args );

			if ( is_wp_error( $post_id ) ) {
				error('Failed to update post.');
			}

			if ( isset( $_POST['author_name'] ) && $_POST['author_name'] ) {
				update_post_meta( $post_id, 'author_name', $_POST['author_name'] );
			}

			success();
		} else {
			error('Missing required permissions.');
		}
	} else {
		error('Missing required parameters.');
	}
}
