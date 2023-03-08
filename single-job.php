<?php
get_header();

do_action( 'ocean_before_content_wrap' );
fs_job_header( get_the_ID() );
?>
<div id="content-wrap" class="container clr">
    <?php do_action( 'ocean_before_primary' ); ?>
    <div id="primary" class="content-area clr">
        <?php do_action( 'ocean_before_content' ); ?>
        <div id="content" class="site-content clr">
            <?php do_action( 'ocean_before_content_inner' ); ?>
			<?php
			// Elementor `single` location.
			if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'single' ) ) {
				// Start loop.
				while ( have_posts() ) :
					the_post(); ?>
                    <div <?php post_class(); ?>>
                        <div class="job-content-inner">
                            <?php fs_job_overview( get_the_ID() ); ?>
                            <?php fs_job_contact( get_the_ID() ); ?>
                            <div class="job_content">
                                <h3 class="description-title">Job Description</h3>
		                        <div class="__content">
                                    <?php the_content(); ?>
                                </div>
                            </div>
	                        <?php fs_job_photos(get_the_ID());?>
	                        <?php //fs_job_related(get_the_ID()); ?>
                        </div>
                    </div>
				<?php
				endwhile;
			}
			?>
			<?php do_action( 'ocean_after_content_inner' ); ?>
			</div><!-- #content -->
		<?php do_action( 'ocean_after_content' ); ?>
		</div><!-- #primary -->
	<?php //do_action( 'ocean_after_primary' ); ?>
</div><!-- #content-wrap -->
<?php do_action( 'ocean_after_content_wrap' ); ?>
<?php get_footer(); ?>
