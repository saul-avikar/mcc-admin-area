<?php
/**
 * Author: Saul Boyd (avikar.io)
 * License: MIT (https://opensource.org/licenses/MIT)
 */

$singular = isset( $singular ) ? $singular : false;
?>
<br />
<div class="MCCAdminArea-file-upload-container">
	<div class="MCCAdminArea-upload-images" data-singular="<?php echo $singular ? 'true' : 'false'; ?>">
		<?php
		if ( isset( $form_data['featured_image'] ) && $singular ) {
			?>
			<div
				data-id="<?php echo $form_data['featured_image']['id']; ?>"
				style='background-image: url("<?php echo $form_data['featured_image']['uri'] ?>");'
			>
				<div class="MCCAdminArea-image-gallery-title">
					<?php echo $form_data['featured_image']['title']; ?>
				</div>

				<div class="MCCAdminArea-image-gallery-remove">
					X
				</div>
			</div>
			<?php
		} elseif ( isset( $form_data['gallery'] ) && !$singular ) {
			foreach ( $form_data['gallery'] as $gal_item ) {
				?>
				<div
					data-id="<?php echo $gal_item['id']; ?>"
					style='background-image: url("<?php echo $gal_item['uri'] ?>");'
				>
					<div class="MCCAdminArea-image-gallery-title">
						<?php echo $gal_item['title']; ?>
					</div>

					<div class="MCCAdminArea-image-gallery-remove">
						X
					</div>
				</div>
				<?php
			}
		}
		?>
	</div>
	<div class="MCCAdminArea-upload-image-failure MCCAdminArea-hidden">
		<?php _e('Something went wrong.', 'mcc-admin-area'); ?>
	</div>
	<div class="MCCAdminArea-upload-image-size-failure MCCAdminArea-hidden">
		<?php _e('Image is too large to upload.', 'mcc-admin-area'); ?>
	</div>
	<div class="MCCAdminArea-upload-image-upload-progress MCCAdminArea-hidden"></div>
	<form class="MCCAdminArea-upload-image-form">
		<label class="MCCAdminArea-upload-image-form-label">
			<?php _e('Add image', 'mcc-admin-area'); ?>
			<input
				type="file"
				accept="image/*"
				name="image_single"
				class="MCCAdminArea-hidden MCCAdminArea-upload-image"
				data-singular="<?php echo $singular ? 'true' : 'false'; ?>"
			/>
		</label>
		<?php wp_nonce_field( 'image_single', 'image_single_nonce' ); ?>
	</form>
</div>
<?php
