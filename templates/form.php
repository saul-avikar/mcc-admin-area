<?php
/**
 * Author: Saul Boyd (avikar.io)
 * License: MIT (https://opensource.org/licenses/MIT)
 */

?>
<form method="post" enctype="multipart/form-data" class="MCCAdminArea-post-form">
	<!-- Author -->
	<label class="MCCAdminArea-post-form-author">
		<?php _e('Name', 'mcc-admin-area'); ?>
		<input
			type="text"
			name="author_name"
			value="<?php echo isset($form_data) ? $form_data['author'] : '' ?>"
		/>
	</label>

	<!-- Title -->
	<label class="MCCAdminArea-post-form-title">
		<?php _e('Post Title', 'mcc-admin-area'); ?>
		<input
			type="text"
			name="title"
			value="<?php echo isset($form_data) ? $form_data['title'] : '' ?>"
		/>
	</label>

	<!-- Date -->
	<?php if ( user_can( $current_user, 'mccadminarea_teacher') ) { ?>
		<label class="MCCAdminArea-post-form-release-date">
			<?php _e('Post Date', 'mcc-admin-area'); ?>
			<input
				type="date"
				name="release_date"
				value="<?php echo isset($form_data) ? $form_data['release_date'] : '' ?>"
			/>
		</label>
	<?php } ?>

	<!-- content -->
	<label class="MCCAdminArea-post-form-content">
		<?php _e('Post Content', 'mcc-admin-area'); ?>
		<textarea name="content"><?php echo isset($form_data) ? $form_data['content'] : '' ?></textarea>
	</label>

	<!-- Categories -->
	<?php
	if ( user_can( $current_user, 'mccadminarea_teacher') ) {
		$term = get_term_by( 'name', 'School Posts', 'category' );

		$children = get_terms( $term->taxonomy, [
			'parent'    => $term->term_id,
			'hide_empty' => false
		] );

		if ( $children ) {
			foreach( $children as $subcat ) {
				$has_cat = false;

				if ( isset( $form_data ) ) {
					foreach ( $form_data['categories'] as $form_cat ) {
						if ( $form_cat === $subcat->term_id ) {
							$has_cat = true;
						}
					}
				}
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
						<?php echo $has_cat ? 'checked' : ''; ?>
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
