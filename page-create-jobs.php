<?php
/**
 * Template Name: Create Jobs Page
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
                    <div class="__lt-create-job">
                        <div class="__lt-inner-create-job __type-create">
                            <form onsubmit="return false;">
                                <div class="__ltrg-content">
                                    <div class="__ltrg-inner">
	                                    <?php
	                                    $all_fields = acf_get_field_groups();
	                                    $field_key  = array_search( 'group_63d9d3bccfabf', array_column( $all_fields, 'key' ) );
	                                    $acf_key    = $all_fields[ $field_key ]["key"];
	                                    $fields     = acf_get_fields( $acf_key );

	                                    $fields_required     = array_filter( $fields, function ( $field ) {
		                                    return $field["required"];
	                                    } );
	                                    $fields_not_required = array_filter( $fields, function ( $field ) {
		                                    return ! $field["required"];
	                                    } );

	                                    foreach ( $fields_required as $field ) {
		                                    render_tag_from_acf_fields( $field );
	                                    }
	                                    render_tag_from_job_taxonomies();
	                                    foreach ( $fields_not_required as $field ) {
		                                    render_tag_from_acf_fields( $field );
	                                    }
	                                    ?>
                                        <div class="__ltrg-item">
                                        <button type="submit" class="__lt-btn-create">WHERE ARE ALL THE GOOD BARISTAS AT?</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
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
