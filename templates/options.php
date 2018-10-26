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

			<label for="mccadminarea_loginLabel">
				<?php _e( 'Login Menu Label:', 'mccadminarea' ); ?>
				<input type="text" id="mccadminarea_loginLabel" name="mccadminarea_loginLabel" value="<?php echo get_option( 'mccadminarea_loginLabel' ); ?>" />
			</label>
			<br />

			<label for="mccadminarea_teacherpostLabel">
				<?php _e( 'Teacher Admin Menu Label:', 'mccadminarea' ); ?>
				<input type="text" id="mccadminarea_teacherpostLabel" name="mccadminarea_teacherpostLabel" value="<?php echo get_option( 'mccadminarea_teacherpostLabel' ); ?>" />
			</label>
			<br />

			<label for="mccadminarea_studentpostLabel">
				<?php _e( 'Student Admin Menu Label:', 'mccadminarea' ); ?>
				<input type="text" id="mccadminarea_studentpostLabel" name="mccadminarea_studentpostLabel" value="<?php echo get_option( 'mccadminarea_studentpostLabel' ); ?>" />
			</label>
		</div>
		<?php submit_button(); ?>
	</form>
</div>
<?php
