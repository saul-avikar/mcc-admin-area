<?php
/**
 * Author: Saul Boyd (avikar.io)
 * License: MIT (https://opensource.org/licenses/MIT)
 */
global $current_user;
get_currentuserinfo();

if ( user_can( $current_user, 'mccadminarea_student') ) {
	?>
	<h1><?php _e('Posting as a student', 'mcc-admin-area'); ?></h1>
	<?php
} elseif ( user_can( $current_user, 'mccadminarea_teacher') ) {
	?>
	<h1><?php _e('Posting as a teacher', 'mcc-admin-area'); ?></h1>
	<?php
}
?>

<form method="post" enctype="multipart/form-data" id="MCCAdminArea-post-form">
	<label>
		<?php _e('Your Name', 'mcc-admin-area'); ?>
		<input type="text" name="author_name" />
	</label>

	<label>
		<?php _e('Post Title', 'mcc-admin-area'); ?>
		<input type="text" name="title" />
	</label>

	<label>
		<?php _e('Post Content', 'mcc-admin-area'); ?>
		<textarea name="content"></textarea>
	</label>

	<?php
	if ( user_can( $current_user, 'mccadminarea_teacher') ) {
		$term = get_term_by( 'name', 'School Posts', 'category' );

		$children = get_terms( $term->taxonomy, [
			'parent'    => $term->term_id,
			'hide_empty' => false
		] );

		if ( $children ) {
			foreach( $children as $subcat ) {
				?>
				<label for="mcc_<?php echo $subcat->slug; ?>">
					<?php echo $subcat->name; ?>
					<input
						name="mcc_<?php echo $subcat->slug; ?>"
						type="checkbox"
						value="mcc_<?php echo $subcat->term_id; ?>"
						id="mcc_<?php echo $subcat->slug; ?>"
					/>
				</label>
				<?php
			}
		}
	}
	?>

	<label>
		<?php _e('Featured Image', 'mcc-admin-area'); ?>
		<input type="file" accept="image/*" name="image" />
	</label>
	<?php wp_nonce_field( 'image', 'image_nonce' ); ?>

	<label>
		<?php _e('Gallery', 'mcc-admin-area'); ?>
		<input type="file" accept="image/*" multiple name="gallery[]" />
	</label>
	<?php wp_nonce_field( 'gallery', 'gallery_nonce' ); ?>
	<br />
	<input id="MCCAdminArea-post-submit" name="submit" type="submit" value="<?php _e('Submit', 'mcc-admin-area'); ?>" />
</form>

<div class="MCCAdminArea-post-success-message MCCAdminArea-hidden">
	<?php _e('Success!', 'mcc-admin-area'); ?>
</div>
<?php
