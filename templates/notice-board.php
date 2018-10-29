<?php
/**
 * Author: Saul Boyd (avikar.io)
 * License: MIT (https://opensource.org/licenses/MIT)
 */
?>

<div class="MCCAdminArea-notice-board">
	<?php
	if ( isset($atts) && isset($atts["category"]) ) {
		$posts = get_posts( [
			'post_status' => 'publish',
			'category_name' => $atts["category"]
		] );

		foreach ($posts as $post) {
			// Retrieve date
			$post_date = get_post_meta( $post->ID, 'release_date', true);
			$post_date = $post_date ? $post_date : $post->post_date;

			// convert month number to name
			$dateObj = DateTime::createFromFormat('!m', substr( $post_date, 5, 2 ) );
			?>
			<div class="MCCAdminArea-notice-board-element">
				<div class="MCCAdminArea-notice-board-date">
					<div class="MCCAdminArea-notice-board-day">
						<?php echo substr( $post_date, 8, 2 ); ?>
					</div>
					<div class="MCCAdminArea-notice-board-month">
						<?php echo $dateObj->format('F'); ?>
					</div>
				</div>

				<div class="MCCAdminArea-notice-board-content">
					<div class="MCCAdminArea-notice-board-title">
						<?php echo $post->post_title; ?>
					</div>
					<div class="MCCAdminArea-notice-board-content">
						<?php echo strip_shortcodes( $post->post_content ); ?>
					</div>
				</div>
			</div>
			<?php
		}
	}
	?>
</div>
<?php
