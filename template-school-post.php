<?php
/**
 * Template Name: Custom Post List
 * Author: Saul Boyd (avikar.io)
 * License: MIT (https://opensource.org/licenses/MIT)
 */

get_header();

if( function_exists( 'nicdark_single' ) ) {
	do_action( "nicdark_single_nd" );
} else{
?>
	<!--start section-->
	<div class="nicdark_section nicdark_bg_grey nicdark_border_bottom_1_solid_grey">
		<!--start nicdark_container-->
		<div class="nicdark_container nicdark_clearfix">
			<div class="nicdark_grid_12">
				<div class="nicdark_section nicdark_height_80"></div>

				<h1 class="nicdark_font_size_60 nicdark_font_size_40_all_iphone nicdark_line_height_40_all_iphone">
					<strong><?php the_title(); ?></strong>
				</h1>

				<div class="nicdark_section nicdark_height_80"></div>
			</div>
		</div>
		<!--end container-->
	</div>
	<!--end section-->

	<div class="nicdark_section nicdark_height_50"></div>

	<!--start nicdark_container-->
	<div class="nicdark_container nicdark_clearfix">
		<!--start all posts previews-->
		<div class="
			<?php
			echo is_active_sidebar( 'nicdark_sidebar' ) ?
				'nicdark_grid_8' : 'nicdark_grid_12'
			?>
		">
		<?php if ( have_posts() ) :
			while(have_posts()) : the_post(); ?>
				<!--#post-->
				<div class="nicdark_section nicdark_container_single_php" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<?php
					if ( has_post_thumbnail() ) {
						the_post_thumbnail();
					}
					?>
					<!--start content-->
					<?php the_content(); ?>
					<!--end content-->
				</div>

				<!--#post-->
				<div class="nicdark_section">
					<?php
						$args = array(
						'before'           => '<!--link pagination--><div id="nicdark_link_pages" class="nicdark_section"><p class="nicdark_margin_top_20 nicdark_first_font nicdark_color_greydark">',
						'after'            => '</p></div><!--end link pagination-->',
						'link_before'      => '',
						'link_after'       => '',
						'next_or_number'   => 'number',
						'nextpagelink'     => esc_html__('Next page', 'educationpack'),
						'previouspagelink' => esc_html__('Previous page', 'educationpack'),
						'pagelink'         => '%',
						'echo'             => 1
						);

					wp_link_pages( $args );

					if (has_tag()) {
						?>
						<!--tag-->
						<div id="nicdark_tags_list" class="nicdark_section">
						<?php esc_html_e('Tags : ','educationpack'); ?>
						<br/>
						<?php the_tags('','',''); ?>
						</div>
						<!--END tag-->
						<?php
					}
					?>

					<!--categories-->
					<div id="nicdark_categories_list" class="nicdark_section">
						<?php
						esc_html_e('Categories : ','educationpack');
						the_category();
						?>
					</div>

					<!--END categories-->
					<?php comments_template(); ?>
				</div>
			<?php endwhile; ?>
		<?php endif; ?>
		</div>

		<!--sidebar-->
		<?php if ( is_active_sidebar( 'nicdark_sidebar' ) ) { ?>
			<div class="nicdark_grid_4">
				<?php if ( ! get_sidebar( 'nicdark_sidebar' ) ) { ?>
					<div class="nicdark_section nicdark_height_50"></div>
				<?php } ?>
			</div>
		<?php } ?>
		<!--end sidebar-->
	</div>
	<!--end container-->


	<div class="nicdark_section nicdark_height_60"></div>

	<?php
}

get_footer();
