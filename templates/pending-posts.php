<?php
/**
 * Author: Saul Boyd (avikar.io)
 * License: MIT (https://opensource.org/licenses/MIT)
 */

global $current_user;
get_currentuserinfo();

function setFormData( $post ) {
	$form_data = [
		'title' => $post->post_title,
		'date' => $post->post_date,
		'content' => $post->post_content,
		'featured_image' => isset( $featured_image ) ? $featured_image : NULL,
		'gallery' => [],
		'categories' => wp_get_post_categories( $post->ID )
	];

	// Set the author name for display
	$form_data['author'] = get_post_meta( $post->ID, 'author_name' );

	if ( count( $form_data['author'] ) !== 0 ) {
		$form_data['author'] = $form_data['author'][0];
	} else {
		$form_data['author'] = __('Author was not specified', 'mcc-admin-area');
	}

	// Set the release date for display
	$form_data['release_date'] = get_post_meta( $post->ID, 'release_date' );

	if ( count( $form_data['release_date'] ) !== 0 ) {
		$form_data['release_date'] = $form_data['release_date'][0];
	}

	// Set the featured image data for display
	if ( has_post_thumbnail( $post->ID )) {
		$thumbnail_id = get_post_thumbnail_id( $post->ID );

		$form_data['featured_image'] = [];
		$form_data['featured_image']['uri'] = wp_get_attachment_image_src( $thumbnail_id )[0];
		$form_data['featured_image']['title'] = get_the_title( $thumbnail_id );
		$form_data['featured_image']['id'] = $thumbnail_id;
	}

	// Set the gallery images for display
	if ( substr($post->post_content, -1) === ']' ) {
		$start_pos = strrpos($post->post_content, '[gallery include="');

		$gal_string = substr($post->post_content, $start_pos + 18);
		$gal_string = rtrim($gal_string, '"]');

		$gal_ids = explode(',', $gal_string);

		$form_data['gallery_shortcode'] = substr($post->post_content, $start_pos, strlen($post->post_content));

		foreach ($gal_ids as $gal_id) {
			$form_data['gallery'][] = [
				'id' => $gal_id,
				'title' => get_the_title($gal_id),
				'uri' => wp_get_attachment_image_src($gal_id)[0]
			];
		}

		$form_data['content'] = str_replace($form_data['gallery_shortcode'], '', $post->post_content); // remove shortcode
	}

	return $form_data;
}

if ( !user_can( $current_user, 'mccadminarea_teacher' ) ) {
	_e('You must be a teacher to approve posts.', 'mcc-admin-area');
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
			// set the form data varible for the form and preview
			$form_data = setFormData( $post );
			?>
			<li class="MCCAdminArea-pending-post">
				<div class="MCCAdminArea-post-title MCCAdminArea-pointer">
					<?php echo $form_data['author']; ?> - <?php echo $form_data['title']; ?>
				</div>
				<div class="MCCAdminArea-hidden">
					<!-- Display a preview of the post -->
					<?php require( plugin_dir_path( __FILE__ ) . './post-preview.php' ); ?>

					<!-- a form with all the post data for a teacher to edit -->
					<div class="MCCAdminArea-post-form-container MCCAdminArea-hidden">
						<?php require( plugin_dir_path( __FILE__ ) . './form.php' ); ?>
					</div>

					<button class="MCCAdminArea-edit-post"><?php _e('Edit', 'mcc-admin-area'); ?></button>
					<button class="MCCAdminArea-post-submit" data-id="<?php echo $post->ID; ?>"><?php _e('Approve Post', 'mcc-admin-area'); ?></button>

					<div class="MCCAdminArea-failure-message MCCAdminArea-hidden">
						<?php _e('Something went wrong.', 'mcc-admin-area'); ?>
					</div>
				</div>
			</li>
		<?php } ?>
	</ul>
<?php }
