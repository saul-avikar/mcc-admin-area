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

<div>
	<?php require( plugin_dir_path( __FILE__ ) . './form.php' ); ?>
	<button class="MCCAdminArea-post-submit">
		<?php _e('Submit', 'mcc-admin-area'); ?>
	</button>
	<div class="MCCAdminArea-success-message">
		Success!
	</div>
	<div class="MCCAdminArea-failure-message">
		Fail!
	</div>
</div>

<div class="MCCAdminArea-post-success-message MCCAdminArea-hidden">
	<?php _e('Success!', 'mcc-admin-area'); ?>
</div>
<?php
