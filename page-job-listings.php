<?php
/**
 * Template Name: Job Listings Page
 * Created by NTC.
 */

$job_location      = isset( $_GET['job-location'] ) ? intval( $_GET['job-location'] ) : '';
$compensation_type = isset( $_GET['job-compensation-types'] ) ? intval( $_GET['job-compensation-types'] ) : '';
$job_type          = isset( $_GET['job-type'] ) ? intval( $_GET['job-type'] ) : '';
$job_experience    = isset( $_GET['job-experience'] ) ? intval( $_GET['job-experience'] ) : '';
$title             = isset( $_GET['filter-title'] ) ? ( $_GET['filter-title'] ) : '';
$order_by          = isset( $_GET['order-by'] ) ? ( $_GET['order-by'] ) : '';

$current_link  = get_the_permalink();
$selected_link = fs_build_remove_selected_link( $current_link, [
	'job-location'           => $job_location,
	'job-compensation-types' => $compensation_type,
	'job-type'               => $job_type,
	'job-experience'         => $job_experience,
	'filter-title'           => $title,
	'order-by'               => $order_by
] );

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
					$query = fs_create_query_jobs( $job_location, $compensation_type, $job_type, $job_experience, $title, $order_by );
					?>
                    <div class="__job-filter-content">
                        <div class="__job-filter-wrap">
                            <div class="__job-filter-top">
                                <div class="container">
                                    <?php fs_jobs_filter_top( $current_link, $job_location, $compensation_type, $job_type, $job_experience, $title ) ?>
                                </div>
                            </div>
                            <div class="__job-filter-inner-wrap max-960">
                                <div class="__job-filter-side">
                                    <div class="results-filter-wrapper" style="display: <?=$selected_link?'block':'none'?>">
                                        <h3 class="title">Your Selected</h3>
                                        <div class="inner">
                                            <ul class="results-filter">
                                                <?= $selected_link ?>
                                            </ul>
                                            <a href="<?= $current_link ?>">Clear all</a>
                                        </div>
                                    </div>
                                    <div class="wrapper-fillter">
                                        <div class="results-count">
                                            Showing <?= $query->found_posts ?> results
                                        </div>
                                        <div class="jobs-ordering-wrapper">
                                            <select name="order-by" class="orderby">
                                                <option value="">Sort by (Default)</option>
                                                <option value="newest" <?= $order_by == 'newest' ? 'selected' : '' ?>>Newest</option>
                                                <option value="oldest" <?= $order_by == 'oldest' ? 'selected' : '' ?>>Oldest</option>
                                                <option value="random" <?= $order_by == 'random' ? 'selected' : '' ?>>Random</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="__job-filter-content-side">
                                    <div class="__job-filter-items-wrap">
                                        <?php
                                        if ( $query->have_posts() ) {
	                                        while ( $query->have_posts() ) {
		                                        $query->the_post();
		                                        fs_get_job_item( get_the_ID() );
	                                        }
	                                        // Restore original Post Data
	                                        wp_reset_postdata();
                                        } else {
	                                        echo '<div class="__job-item item-not-found">Sorry, no jobs matched your criteria.</div>';
                                        }
                                        ?>
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
