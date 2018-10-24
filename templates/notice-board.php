<?php
/**
 * Author: Saul Boyd (avikar.io)
 * License: MIT (https://opensource.org/licenses/MIT)
 */
?>

<div>
	<?php
	if ( isset($atts) && isset($atts["category"]) ) {
		$posts = get_posts( [
			'post_status' => 'publish',
			'category_name' => $atts["category"]
		] );

		foreach ($posts as $post) {
			_e('Date:', 'mcc-admin-area');
			echo $post->post_date;
			echo '<br />';

			_e('Title:', 'mcc-admin-area');
			echo $post->post_title;
			echo '<br />';

			_e('Content:', 'mcc-admin-area');
			echo $post->post_content;
			echo '<br />';
		}
	}
	?>
</div>

<?php
