<?php
/**
 * The template for displaying form to post a job
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="entry-header">
			<header>
				<h1><?php the_author(); ?></h1>
				<h2><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentytwelve' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php echo get_the_date(); ?></a></h2>
			</header>
			<?php echo get_avatar( get_the_author_meta( 'ID' ), apply_filters( 'twentytwelve_status_avatar', '48' ) ); ?>
		</div><!-- .entry-header -->

		<div class="entry-content">
		  <?php //the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentytwelve' ) ); ?>
		  <?php
		  if($_POST['post_job']) {?>
		  Yet to implement<br/>
		  Your job post created as follows: <br/> 
		  <?php $title_post = $_POST['company_name'].": ". $_POST['yoe']." years," . $_POST['nov']." pos, ". $_POST['job_title'];
					  $post_details = $_POST['job_description']; 
					  echo $title_post."<br/>";
					  echo $post_details."<br/>";
					  $my_post = array();
					  $my_post['post_title'] = $title_post;
					  $my_post['post_content'] = $post_details;
					  $my_post['post_status'] = 'publish';
					  $my_post['post_author'] = wp_get_current_user()->ID;
					  $post_id = wp_insert_post($my_post);
	   } else { ?>
		  <form  method="post" id="table_<?php the_ID();?>" action="<?php echo the_permalink();?>">
		    <table>
		      <tr> <td>
		    Name of company</td><td colspan="3"> <input type="text" name="company_name" size="55" value=""/></td> </tr>
		    <tr><td>Years of experience</td><td> <input type="text" name="yoe" value=""/></td>
		    <td> No. of vacancies</td><td><input type="text" name="nov" value="1" size="12"/></td>
		    </tr>
		    <tr><td colspan="4">Job heading </tr>
		    <tr><td colspan="4"><input type="text" size="71" name="job_title" value=""/></td></tr>
		   <tr><td colspan="4"> Job description </td></tr>
		   <tr><td colspan="4"><textarea name="job_description" cols="80" rows="20"></textarea></td></tr>
		   <tr><td colspan="4">
		     <input type="submit" name="post_job" value="Post Job"/>
		   </td></tr>
		 </table>
		  </form>
		    <?php } ?>
		</div><!-- .entry-content -->

		<footer class="entry-meta">
		</footer><!-- .entry-meta -->
	</article><!-- #post -->
