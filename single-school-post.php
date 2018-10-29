<?php
/**
 * Template Name: School Post
 * Template Post Type: post
 * Author: Saul Boyd (avikar.io)
 * License: MIT (https://opensource.org/licenses/MIT)
 */

get_header();
?>
<!--start nicdark_container-->
<div class="nd_options_container nd_options_clearfix">
	<div class="nd_options_section nd_options_box_sizing_border_box nd_options_padding_15">
		<!--start all posts previews-->
		<div class="nicdark_grid_12">
			<?php if ( have_posts() ) :
				while(have_posts()) : the_post(); ?>
					<!--#post-->
					<div class="nicdark_section nicdark_container_single_php" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<h1><strong><?php the_title(); ?></strong></h1>
						<div class="nd_options_section nd_options_height_20"></div>
						<?php
						if ( has_post_thumbnail() ) {
							the_post_thumbnail();
						}
						?>
						<div class="MCCAdminArea-author"><?php echo get_post_meta( get_the_ID(), 'author_name', true ); ?></div>
						<!--start content-->
						<?php the_content(); ?>
						<!--end content-->
					</div>
					<!--#post-->

					<!--START  for post-->
					<style type="text/css">
						/*font and color*/
						.nd_options_comments_ul li .comment-author .fn,
						.nd_options_comments_ul li .comment-author .fn a { color: #727475; }
						.nd_options_comments_ul li .comment-author .fn,
						.nd_options_comments_ul li .comment-author .fn a { font-family: 'Montserrat', sans-serif; }
						.nd_options_comments_ul li .reply a.comment-reply-link { background-color: #000; }
						#nd_options_comments_form input[type='submit'] { background-color: #000; }
						#nd_options_comments_form #commentform.comment-form input[type='submit'] { background-color: #000; }


						/*compatibility for nd-learning*/
						#nd_learning_single_course_comments .nd_options_comments_ul li .comment-author .fn,
						#nd_learning_single_course_comments .nd_options_comments_ul li .comment-author .fn a { color: #727475; }
						#nd_learning_single_course_comments .nd_options_comments_ul li .comment-author .fn,
						#nd_learning_single_course_comments .nd_options_comments_ul li .comment-author .fn a { font-family: 'Montserrat', sans-serif; }
					</style>
					<!--END css for post-->
				<?php endwhile; ?>
			<?php endif; ?>
		</div>
	</div>
</div>
<!--end container-->

<div class="nicdark_section nicdark_height_60"></div>
<?php

get_footer();
