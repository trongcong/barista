<?php
/**
 * Template Name: Listings Page
 * Created by NTC.
 */
get_header(); ?>
<?php do_action( 'ocean_before_content_wrap' ); ?>
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
					the_post();
					the_content();
					?>
                    <div class="__listings-wrap">
                        <div class="__lt-filter-top">
                            <button type="button" class="__show-filter-popup">
                                <span></span>
                                <span class="sr-only">Filters</span>
                                <span class="up-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" data-name="Layer 1" viewBox="0 0 14 14" role="img">
                                        <path d="M0 2.48v2h2.09a3.18 3.18 0 006.05 0H14v-2H8.14a3.18 3.18 0 00-6.05 0zm3.31 1a1.8 1.8 0 111.8 1.81 1.8 1.8 0 01-1.8-1.82zm2.2 6.29H0v2h5.67a3.21 3.21 0 005.89 0H14v-2h-2.29a3.19 3.19 0 00-6.2 0zm1.3.76a1.8 1.8 0 111.8 1.79 1.81 1.81 0 01-1.8-1.79z"></path>
                                    </svg>
                                </span>
                                <span>Filters</span>
                            </button>
                        </div>
						<div class="__lt-inner-wrap">
							<div class="__lt-filter-side">
                                <div class="__lt-by">Filter by</div>
                                <div class="__lt-filter-side-inner">
                                    <?php lt_filter_group() ?>
                                </div>
                            </div>
							<div class="__lt-content-side">
								<div class="__lt-items-wrap">
                                    <?php
                                    $query = create_query_barista();
                                    if ( $query->have_posts() ) {
	                                    while ( $query->have_posts() ) {
		                                    $query->the_post();
		                                    get_lt_item( get_the_ID() );
	                                    }
	                                    // Restore original Post Data
	                                    wp_reset_postdata();
                                    } else {
	                                    echo '<div class="__lt-item item-not-found">Sorry, no barista matched your criteria.</div>';
                                    }
                                    ?>
								</div>
							</div>
						</div>
                        <div class="__lt-filter-modal" style="display: none">
                            <div class="__lt-modal-backdrop"></div>
                            <div class="__filter-popup-content">
                                <div class="__filter-popup-inner">
                                    <div class="__lt-by">Filter by
                                    <button type="button" class="__lt-modal-close">
                                        <span class="sr-only">Close the dialog</span>
                                        <span class="up-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" viewBox="0 0 14 14" role="img">
                                                <polygon fill-rule="evenodd" points="12.524 0 7 5.524 1.476 0 0 1.476 5.524 7 0 12.524 1.476 14 7 8.476 12.524 14 14 12.524 8.476 7 14 1.476"></polygon>
                                            </svg>
                                        </span>
                                    </button>
                                    </div>
                                    <div class="__filter-popup-group">
                                        <?php lt_filter_group() ?>
                                    </div>
                                </div>
                            </div>
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
	<?php do_action( 'ocean_after_primary' ); ?>
	</div><!-- #content-wrap -->
<?php do_action( 'ocean_after_content_wrap' ); ?>
<?php get_footer(); ?>
