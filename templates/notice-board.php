<?php
/**
 * Author: Saul Boyd (avikar.io)
 * License: MIT (https://opensource.org/licenses/MIT)
 */
?>

<div>
	<?php
	$posts = get_posts(array (
		'post_status' => 'publish',
		'category_name' => $atts["category"]
	) );

	foreach ($posts as $post) {
		?>
		Date: <?php echo $post->post_date; ?><br />
		Title: <?php echo $post->post_title; ?><br />
		Content: <?php echo $post->post_content; ?><br />
		<?php
	}
	?>
</div>

<?php
