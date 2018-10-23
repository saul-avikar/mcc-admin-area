<?php
/**
 * Author: Saul Boyd (avikar.io)
 * License: MIT (https://opensource.org/licenses/MIT)
 */

global $current_user;
get_currentuserinfo();

if ( user_can( $current_user, 'mccadminarea_student') ) {
	?>
	<h1>Posting as a student</h1>
	<?php
} elseif ( user_can( $current_user, 'mccadminarea_teacher') ) {
	?>
	<h1>Posting as a teacher</h1>
	<?php
}
?>

<form method="post" enctype="multipart/form-data" id="form">
	<label for="name">Your Name</label>
	<input type="text" id="name" name="author_name" />

	<label for="title">Post Title</label>
	<input type="text" id="title" name="title" />

	<label for="mcc_content">Post Content</label>
	<textarea id="content" name="content"></textarea>

	<?php
	if ( user_can( $current_user, 'mccadminarea_teacher') ) {
		$term = get_term_by( 'name', 'School Posts', 'category' );

		$children = get_terms( $term->taxonomy, array(
			'parent'    => $term->term_id,
			'hide_empty' => false
		) );

		if ( $children ) {
			foreach( $children as $subcat ) {
				?>
				<label for="mcc_<?php echo $subcat->slug; ?>">
					<?php echo $subcat->name; ?>
					<input
						name="mcc_<?php echo $subcat->slug; ?>"
						type="checkbox"
						value="mcc_<?php echo $subcat->term_id; ?>"
						id="mcc_<?php echo $subcat->slug; ?>"
					/>
				</label>
				<?php
			}
		}
	}
	?>

	<label for="image">Featured Image</label>
	<input type="file" accept="image/*" id="image" name="image" />
	<?php wp_nonce_field( 'image', 'image_nonce' ); ?>

	<label for="gallery">Gallery</label>
	<input type="file" accept="image/*" multiple id="gallery" name="gallery[]" />
	<?php wp_nonce_field( 'gallery', 'gallery_nonce' ); ?>
	<br />
	<input id="submit" name="submit" type="submit" value="Submit" />
</form>

<div class="success-message" style="display: none;">
	Success!
</div>

<script>
	(function ($) {
		$("#submit").click(function (e) {
			e.preventDefault();

			var data = $.ajax({
				url: '/mccadminarea_post',
				type: 'POST',
				data: new FormData($('form')[0]),
				processData: false,
				contentType: false,
			}).always(function (data) {
				var response = null;

				try {
					response = JSON.parse(data.responseText);
				} catch (e) {
					try {
						response = JSON.parse(data);
					} catch (e) {
						console.error(data);
					}
				}

				if (response.error) {
					console.log(response.msg);
				} else{
					$("form").hide(100);
					$(".success-message").show(100);
				}
			});
		});
	}(jQuery));
</script>
<?php
