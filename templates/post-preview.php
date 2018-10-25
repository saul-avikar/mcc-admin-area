<?php
/**
 * Author: Saul Boyd (avikar.io)
 * License: MIT (https://opensource.org/licenses/MIT)
 */
?>
<div class="MCCAdminArea-post-preview">
	<?php if (! isset( $form_data ) ) { ?>
		<?php if ( isset( $form_data['featured_image'] ) ) { ?>
			<img src="<?php echo $form_data['featured_image']['uri']; ?>" />
		<?php } ?>

		<div>
			<?php echo $form_data['date']; ?>
		</div>

		<div>
			<?php echo $form_data['content']; ?>
			<br />
			<br />
			<?php echo do_shortcode($form_data['gallery_shortcode']); ?>
		</div>
		<button class="MCCAdminArea-edit-post"><?php _e('Edit Post', 'mcc-admin-area'); ?></button>
	<?php } else { ?>
		Error: form is not specified.
	<?php } ?>
</div>
<?php
