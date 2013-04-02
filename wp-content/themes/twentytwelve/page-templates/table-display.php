<?php
/**
 * Template Name: Display Data from table, no side bar
 *
 * Description: This is enhancement to Twenty Twelve theme. It is mean
 * for fetching information from database and displaying on the page.
 * Content of the page shall guide this template to select the fields
 * and any search that needs to be done.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since 2013 (customized by Vineet Maheshwari)
 */

get_header(); ?>

	<div id="primary" class="site-content">
		<div id="content" role="main">
			<?php while ( have_posts() ) : the_post(); ?>
			<?php get_template_part( 'content', 'table' ); ?>
			<?php //comments_template( '', true ); ?>
			<?php endwhile; // end of the loop. ?>
		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>