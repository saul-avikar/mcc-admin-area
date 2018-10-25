<?php
/**
 * Author: Saul Boyd (avikar.io)
 * License: MIT (https://opensource.org/licenses/MIT)
 */

?>
<form method="post" enctype="multipart/form-data" class="MCCAdminArea-post-form">
	<label class="MCCAdminArea-post-form-author">
		<?php _e('Name', 'mcc-admin-area'); ?>
		<input
			type="text"
			name="author_name"
			value="<?php echo isset($form_data) ? $form_data['author'] : '' ?>"
		/>
	</label>

	<label class="MCCAdminArea-post-form-title">
		<?php _e('Post Title', 'mcc-admin-area'); ?>
		<input
			type="text"
			name="title"
			value="<?php echo isset($form_data) ? $form_data['title'] : '' ?>"
		/>
	</label>

	<label class="MCCAdminArea-post-form-content">
		<?php _e('Post Content', 'mcc-admin-area'); ?>
		<textarea name="content"><?php echo isset($form_data) ? $form_data['content'] : '' ?></textarea>
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
				<label
					for="mcc_<?php echo $subcat->slug; ?>"
					class="MCCAdminArea-post-form-categories"
				>
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
</form>

<span>
	<?php _e('Featured Image', 'mcc-admin-area'); ?>
</span>
<?php
	$singular = true;
	require( plugin_dir_path( __FILE__ ) . './upload-image.php' );
?>

<span>
	<?php _e('Gallery', 'mcc-admin-area'); ?>
</span>
<?php
	$singular = false;
	require( plugin_dir_path( __FILE__ ) . './upload-image.php' );
?>
