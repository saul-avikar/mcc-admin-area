<?php
/**
 * Author: Saul Boyd (avikar.io)
 * License: MIT (https://opensource.org/licenses/MIT)
 */
?>

<div class="wrap">
	<h1><?php _e( 'Admin Settings:', 'mccadminarea' ); ?></h1>
	<form method="post" action="options.php">
		<div>
			<?php settings_fields( 'mccadminarea_settings' ); ?>

			<label for="mccadminarea_postLabel"><?php _e( 'Post Menu Label:', 'mccadminarea' ); ?></label>
			<input type="text" id="mccadminarea_postLabel" name="mccadminarea_postLabel" value="<?php echo get_option( 'mccadminarea_postLabel' ); ?>" />

			<label for="mccadminarea_loginLabel"><?php _e( 'Login Menu Label:', 'mccadminarea' ); ?></label>
			<input type="text" id="mccadminarea_loginLabel" name="mccadminarea_loginLabel" value="<?php echo get_option( 'mccadminarea_loginLabel' ); ?>" />
		</div>
		<?php  submit_button(); ?>
	</form>
</div>

<script>
</script>

<style>
</style>

<?php
