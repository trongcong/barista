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
					$query = create_query_barista();
					?>
                    <div class="__lt-filter-content">
                        <div class="__listings-wrap">
                            <div class="__lt-filter-top">
                                <div class="__lt-filter-button-wrap">
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
                                    <span class="__counter-result"></span>
                                </div>
                            </div>
                            <div class="__lt-inner-wrap">
                                <div class="__lt-filter-side">
                                    <div class="__lt-by">
                                        Filter by
                                        <span class="__counter-result">
                                            <?= $query->found_posts ? "(" . ( $query->found_posts . " barista" ) . ( $query->found_posts > 1 ? "s)" : ")" ) : ""; ?>
                                        </span>
                                    </div>
                                    <div class="__lt-filter-side-inner">
                                        <?php lt_filter_group() ?>
                                    </div>
                                </div>
                                <div class="__lt-content-side">
                                    <div class="__lt-items-wrap">
                                        <?php
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
                        </div>
						<?php lt_filter_modal(); ?>
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
