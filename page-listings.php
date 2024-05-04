<?php
/**
 * Template Name: Listings Page
 * Created by NTC.
 */
$classLayoutActive = 'active';
$layout            = $_GET['layout'] ?? 'grid';
$is_grid           = $layout === 'grid';
$is_map            = $layout === 'map';
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
					$query = create_query_barista( $layout );
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
	                                <div class="__content-inner-wrap">
		                                <div class="items-style">
			                                <span>Choose your view</span>
			                                <div>
				                                <a href="<?= esc_url( add_query_arg( 'layout', 'grid' ) ) ?>"
				                                   class="<?= $is_grid ? $classLayoutActive : '' ?>" title="Listing view">
					                                <svg aria-hidden="true" focusable="false"
					                                     data-prefix="fas" data-icon="grid-2"
					                                     role="img"
					                                     xmlns="http://www.w3.org/2000/svg"
					                                     viewBox="0 0 512 512">
						                                <path
							                                fill="currentColor"
							                                d="M224 80c0-26.5-21.5-48-48-48H80C53.5 32 32 53.5 32 80v96c0 26.5 21.5 48 48 48h96c26.5 0 48-21.5 48-48V80zm0 256c0-26.5-21.5-48-48-48H80c-26.5 0-48 21.5-48 48v96c0 26.5 21.5 48 48 48h96c26.5 0 48-21.5 48-48V336zM288 80v96c0 26.5 21.5 48 48 48h96c26.5 0 48-21.5 48-48V80c0-26.5-21.5-48-48-48H336c-26.5 0-48 21.5-48 48zM480 336c0-26.5-21.5-48-48-48H336c-26.5 0-48 21.5-48 48v96c0 26.5 21.5 48 48 48h96c26.5 0 48-21.5 48-48V336z"></path>
					                                </svg>
				                                </a>
				                                <a href="<?= esc_url( add_query_arg( 'layout', 'map' ) ) ?>"
				                                   class="<?= $is_map ? $classLayoutActive : '' ?>" title="Maps view">
					                                <svg xmlns="http://www.w3.org/2000/svg"
					                                     viewBox="0 0 576 512">
						                                <path
							                                fill="currentColor"
							                                d="M565.6 36.2C572.1 40.7 576 48.1 576 56V392c0 10-6.2 18.9-15.5 22.4l-168 64c-5.2 2-10.9 2.1-16.1 .3L192.5 417.5l-160 61c-7.4 2.8-15.7 1.8-22.2-2.7S0 463.9 0 456V120c0-10 6.1-18.9 15.5-22.4l168-64c5.2-2 10.9-2.1 16.1-.3L383.5 94.5l160-61c7.4-2.8 15.7-1.8 22.2 2.7zM48 136.5V421.2l120-45.7V90.8L48 136.5zM360 422.7V137.3l-144-48V374.7l144 48zm48-1.5l120-45.7V90.8L408 136.5V421.2z"/>
					                                </svg>
				                                </a>
			                                </div>
		                                </div>
		                                <?php if ( $is_grid ) { ?>
			                                <div class="__lt-items-wrap">
				                                <?php
				                                if ( $query->have_posts() ) {
					                                while ( $query->have_posts() ) {
						                                $query->the_post();
						                                get_lt_item2( get_the_ID() );
					                                }
					                                // Restore original Post Data
					                                wp_reset_postdata();
				                                } else {
					                                echo '<div class="__lt-item item-not-found">Sorry, no barista matched your criteria.</div>';
				                                }
				                                ?>
			                                </div>
		                                <?php } else {
			                                wp_enqueue_script( 'fs-map-listing-script' );
			                                ?>
			                                <div id="barista-map"></div>
		                                <?php } ?>
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
