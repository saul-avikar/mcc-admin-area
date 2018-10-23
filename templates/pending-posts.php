<?php
/**
 * Author: Saul Boyd (avikar.io)
 * License: MIT (https://opensource.org/licenses/MIT)
 */

global $current_user;
get_currentuserinfo();

if ( !user_can( $current_user, 'mccadminarea_teacher' ) ) {
	?>
		You must be a teacher to approve posts.
	<?php
} else {
	$posts = get_posts(array (
		'post_status' => 'draft'
	) );

	foreach ($posts as $post) {
		$author_name = get_post_meta( $post->ID, 'author_name' );

		if ( count( $author_name ) !== 0 ) {
			$author_name = $author_name[0];
		} else {
			$author_name = __("Author was not specified");
		}

		$image_uri = "";

		if ( has_post_thumbnail( $post->ID )) {
			$image_uri = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' )[0];
		}
		$post_content = $post->post_content;
		$images = array();

		if ( substr($post_content, -1) === ']' ) {
			$start_pos = strrpos($post_content, '[gallery include="');
			$gal_string = substr($post_content, $start_pos + 18);

			$gal_string = rtrim($gal_string, '"]');
			$gal_ids = explode(',', $gal_string);

			foreach ($gal_ids as $gal_id) {
				$images[] = $gal_id;
			}
		}

		$shortcode = '[gallery include="' . implode(",", $images) . '"]';
		$post_content = str_replace($shortcode, '', $post_content);
		?>
		<ul>
			<li>
				<div class="post-title">
					<?php echo $author_name; ?> - <?php echo $post->post_title; ?>
				</div>
				<div class="post-content-container">
					<div class="static">
						<img class="post-image" src="<?php echo $image_uri; ?>" />
						<div class="post-date">
							<?php
							echo $post->post_date;
							?>
						</div>
						<div class="post-content">
							<?php
							echo $post_content;
							echo '<br /><br />';
							echo do_shortcode($shortcode);
							?>
						</div>
						<button class="edit-post">Edit Post</button>
					</div>
					<form class="dynamic">
						<label>
							Name <input type="text" name="author_name" value="<?php echo $author_name; ?>" />
						</label>


						<label>
							Title <input type="text" id="title" name="title" value="<?php echo $post->post_title; ?>" />
						</label>

						<label>
							Content

							<textarea id="content" name="content"><?php echo $post_content; ?></textarea>
						</label>

						Featured Image
						<?php
						if ( get_post_thumbnail_id( $post->ID ) ) {
							echo get_the_title( get_post_thumbnail_id( $post->ID ) );
							?>
							<br />
							<?php
						}
						?>
						<input type="file" accept="image/*" class="featured-image-input" name="image" />
						<?php wp_nonce_field( 'image', 'image_nonce' ); ?>

						Gallery
						<?php
						foreach ($images as $gal_image) {
							?>
							<div class="existing-image-gallery-items" name="gal_<?php echo $gal_image ?>" style="background-image: url('<?php echo wp_get_attachment_image_src($gal_image)[0]; ?>');">
								<?php echo get_the_title($gal_image); ?> <span class="image-gallery-remove">(X)</span><br />
							</div>
							<?php
						}
						?>

						<input type="file" accept="image/*" multiple id="gallery" name="gallery[]" />
						<?php wp_nonce_field( 'gallery', 'gallery_nonce' ); ?>

						<button class="edit-post">Cancel editing</button>
					</form>
					<button class="approve-post" name="mcc_<?php echo $post->ID; ?>">Approve Post</button>
				</div>
			</li>
		</ul>
		<?php
	}
}
?>
<style>
	.post-title {
		cursor: pointer;
	}

	.post-content-container {
		display: none;
	}

	.dynamic {
		display: none;
	}
</style>
<script>
	(function ($) {
		$(".image-gallery-remove").click(function () {
			$(this).parent().hide(0);
		});

		$(".post-title").click(function () {
			$(this).next().toggle(100);
		});

		$(".edit-post").click(function (e) {
			e.preventDefault();
			$(this).parent().parent().find(".dynamic").toggle(100);
			$(this).parent().parent().find(".static").toggle(100);
		});

		$(".approve-post").click(function () {
			// change state of this post
			var postContainer = $(this).parent().parent();
			var postId = $(this).attr("name");
			var form = new FormData();

			postId = postId.substr(4, postId.length);

			if ($(this).parent().find(".dynamic").is(":visible")) {
				form = new FormData($(this).parent().find("form")[0]);

				var existingImages = $(this).parent().find(".dynamic").children(".existing-image-gallery-items");

				if (existingImages.length > 0) {
					var oldGallery = [];

					for (var existingImage of existingImages) {
						if ($(existingImage).is(":visible")) {
							var id = $(existingImage).attr("name");

							id = parseInt(id.slice(4));

							oldGallery.push(id);
						}
					}

					form.append("old_gallery", oldGallery);
				}
			}

			form.append("post_id", postId)

			var data = $.ajax({
				url: '/mccadminarea_post',
				type: 'POST',
				data: form,
				processData: false,
				contentType: false
			}).always(function (data) {
				var response = null;

				try {
					response = JSON.parse(data.responseText);
				} catch (e) {
					try {
						response = JSON.parse(data);
					} catch (e) {
						console.error(data);
					}
				}

				if (response.error) {
					console.log(response.msg);
				} else {
					postContainer.hide(100);
				}
			});
		});
	}(jQuery))
</script>
<?php
