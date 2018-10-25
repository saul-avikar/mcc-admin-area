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
	// retrieve all unapproved posts
	$posts = get_posts([
		'post_status' => 'draft'
	]);

	?>
	<ul class="MCCAdminArea-pending-post-list">
		<?php

		// Loop thorugh all these posts for display
		foreach ($posts as $post) {
			// Set the author name for display
			$author_name = get_post_meta( $post->ID, 'author_name' );

			if ( count( $author_name ) !== 0 ) {
				$author_name = $author_name[0];
			} else {
				$author_name = __('Author was not specified', 'mcc-admin-area');
			}

			// Set the featured image data for display
			if ( has_post_thumbnail( $post->ID )) {
				$thumbnail_id = get_post_thumbnail_id( $post->ID );

				$featured_image = [];
				$featured_image['uri'] = wp_get_attachment_image_src( $thumbnail_id )[0];
				$featured_image['title'] = get_the_title( $thumbnail_id );
			}

			// Set thepost content for display
			$post_content = $post->post_content;

			// Set the gallery images for display
			$images = [];

			if ( substr($post_content, -1) === ']' ) {
				$start_pos = strrpos($post_content, '[gallery include="');
				$gal_string = substr($post_content, $start_pos + 18);

				$gal_string = rtrim($gal_string, '"]');
				$gal_ids = explode(',', $gal_string);

				foreach ($gal_ids as $gal_id) {
					$images[] = $gal_id;
				}
			}

			// Shortcode for displaying gallery
			$shortcode = '[gallery include="' . implode(",", $images) . '"]';

			// Update the content to not include the shortcode as we will have it seperate
			$post_content = str_replace($shortcode, '', $post_content);
			?>
			<li class="MCCAdminArea-pending-post">
				<div class="MCCAdminArea-post-title MCCAdminArea-pointer">
					<?php echo $author_name; ?> - <?php echo $post->post_title; ?>
				</div>
				<div class="MCCAdminArea-hidden">
					<!-- Display a preview of the post -->
					<div class="MCCAdminArea-static">
						<?php if ( isset( $featured_image ) ) { ?>
							<img src="<?php echo $featured_image['uri']; ?>" />
						<?php } ?>

						<div>
							<?php echo $post->post_date; ?>
						</div>

						<div>
							<?php echo $post_content; ?>
							<br />
							<br />
							<?php echo do_shortcode($shortcode); ?>
						</div>
						<button class="MCCAdminArea-edit-post"><?php _e('Edit Post', 'mcc-admin-area'); ?></button>
					</div>

					<!-- a form with all the post data for a teacher to edit -->
					<form class="MCCAdminArea-dynamic MCCAdminArea-hidden">
						<label>
							<?php _e('Name', 'mcc-admin-area'); ?>
							<input
								type="text"
								name="author_name"
								value="<?php echo $author_name; ?>"
							/>
						</label>

						<label>
							<?php _e('Title', 'mcc-admin-area'); ?>
							<input
								type="text"
								id="title"
								name="title"
								value="<?php echo $post->post_title; ?>"
							/>
						</label>

						<label>
							<?php _e('Content', 'mcc-admin-area'); ?>
							<textarea id="content" name="content">
								<?php echo $post_content; ?>
							</textarea>
						</label>

						<?php
						_e('Featured Image', 'mcc-admin-area');
						if ( isset( $featured_image ) ) {
							echo $featured_image['title'];
						}
						?>
						<input type="file" accept="image/*" name="image" />
						<?php wp_nonce_field( 'image', 'image_nonce' ); ?>

						<?php
						_e('Gallery', 'mcc-admin-area');
						foreach ($images as $gal_image) {
							?>
							<div name="gal_<?php echo $gal_image ?>" style="background-image: url('<?php echo wp_get_attachment_image_src($gal_image)[0]; ?>');">
								<?php echo get_the_title($gal_image); ?> <span class="MCCAdminArea-image-gallery-remove">(X)</span><br />
							</div>
							<?php
						}
						?>

						<input type="file" accept="image/*" multiple id="gallery" name="gallery[]" />
						<?php wp_nonce_field( 'gallery', 'gallery_nonce' ); ?>

						<button class="MCCAdminArea-edit-post"><?php _e('Cancel editing', 'mcc-admin-area'); ?></button>
					</form>
					<button class="MCCAdminArea-approve-post" name="mcc_<?php echo $post->ID; ?>"><?php _e('Approve Post', 'mcc-admin-area'); ?></button>
				</div>
			</li>
		<?php } ?>
	</ul>
<?php }
