<?php
/**
 * Author: Saul Boyd (avikar.io)
 * License: MIT (https://opensource.org/licenses/MIT)
 */
?>
<div class="MCCAdminArea-post-preview">
	<?php if ( isset( $form_data ) ) { ?>
		<?php if ( isset( $form_data['featured_image'] ) ) { ?>
			<img
				src="<?php echo $form_data['featured_image']['uri']; ?>"
				class="MCCAdminArea-post-preview-feature"
			/>
		<?php } ?>

		<div class="MCCAdminArea-post-preview-date">
			<?php echo $form_data['date']; ?>
		</div>

		<div class="MCCAdminArea-post-preview-content">
			<?php echo $form_data['content']; ?>
		</div>

		<div class="MCCAdminArea-post-preview-gallery">
			<?php
			if ( isset( $form_data['gallery_shortcode'] ) ) {
				echo do_shortcode($form_data['gallery_shortcode']);
			}
			?>
		</div>
	<?php } else { ?>
		Error: form is not specified.
	<?php } ?>
</div>
<?php
