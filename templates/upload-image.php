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
				<?php echo $form_data['featured_image']['title']; ?>
				<span class="MCCAdminArea-image-gallery-remove">(X)</span>
			</div>
			<?php
		} elseif ( isset( $form_data['gallery'] ) && !$singular ) {
			foreach ( $form_data['gallery'] as $gal_item ) {
				?>
				<div
					data-id="<?php echo $gal_item['id']; ?>"
					style='background-image: url("<?php echo $gal_item['uri'] ?>");'
				>
					<?php echo $gal_item['title']; ?>
					<span class="MCCAdminArea-image-gallery-remove">(X)</span>
				</div>
				<?php
			}
		}
		?>
	</div>
	<form class="MCCAdminArea-upload-image-form">
		<label>
			<?php _e('Change this to make it look like it wasnt made in the 90s', 'mcc-admin-area'); ?>
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
	<div class="MCCAdminArea-upload-image-failure MCCAdminArea-hidden">
		<?php _e('Something went wrong.', 'mcc-admin-area'); ?>
	</div>
</div>
<?php
