<?php
/**
 * Template Name: Register Barista Page
 * Created by NTC.
 */
if ( can_edit_barista_profile() ) {
	header( "Location: " . get_barista_profile_link() );
	exit();
}
acf_form_head();
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
					<div class="__lt-register-barista">
						<div class="__lt-inner-register __type-create">
							<form onsubmit="return false;">
								<div class="__ltrg-content">
									<div class="__ltrg-inner">
										<div class="__ltrg-item">
											<div class="__lt-input __active-code __required">
												<label>
													<span>Activate your listing with code <span
															class='__label-required'> *</span></span>
													<input name="active_code" type="text" required placeholder=""/>
												</label>
											</div>
										</div>
										<?php
										$all_fields = acf_get_field_groups();
										$field_key  = array_search( 'group_625cef11cef8a', array_column( $all_fields, 'key' ) );
										$acf_key    = $all_fields[ $field_key ]["key"];
										$fields     = acf_get_fields( $acf_key );

										foreach ( $fields as $field ) {
											if ( $field['name'] !== 'advanced_code' ) {
												render_tag_from_acf_fields( $field );
											}
										}
										?>
										<div class="__ltrg-item">
											<button type="submit" disabled class="__lt-btn-register">I AM READY FOR A
												JOB. LET'S GOOOOOO
											</button>
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
