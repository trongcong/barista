<?php
/**
 * Template Name: Listings Page
 * Created by NTC.
 */
$barista_skills     = [
	"Prepare Australian coffees",
	"Coffee extraction / Dial-in",
	"Latte art (heart, tulip or rosetta)",
	"Alternative brewing",
	"Advanced latte art (signature designs)",
	"Able to describe coffee",
	"Cleaning and maintenance",
	"Coffee tasting"
];
$volumes            = [
	"Less than 3 kgs",
	"More than 3 kgs",
	"More than 4 kgs",
	"More than 5 kgs",
	"More than 6 kgs",
	"More than 7 kgs",
	"More than 8 kgs"
];
$hospitality_skills = [
	"POS",
	"Handling transactions",
	"Waitering",
	"Preparing light food",
	"Preparing cold drinks",
	"Cleaning",
	"Customer service"
];

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
					//get_template_part( 'partials/page/layout' );
					the_content();
					//					var_dump(get_field("active_code", 42));
					?>
                    <div class="__listings-wrap">
                        <div class="__lt-filter-top">

                        </div>
						<div class="__lt-inner-wrap">
							<div class="__lt-filter-side">
                                <div class="__lt-by">Filter by</div>

                                <div class="__lt-filter-group">
                                    <h4 class="__lt-filter-title">First Shot Barista Training Certification</h4>
                                    <div class="__lt-filter-checkbox-group">
                                        <div class="__lt-checkbox">
                                            <label>
                                                <input name="training_certification[]" type="checkbox" value="Professional Training" />
                                                <span>Professional Training</span>
                                            </label>
                                        </div>
                                        <div class="__lt-checkbox">
                                            <label>
                                                <input name="training_certification[]" type="checkbox" value="Basic Sensory" />
                                                <span>Basic Sensory</span>
                                            </label>
                                        </div>
                                        <div class="__lt-checkbox">
                                            <label>
                                                <input name="training_certification[]" type="checkbox" value="Advanced Sensory" />
                                                <span>Advanced Sensory</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="__lt-filter-group">
                                    <h4 class="__lt-filter-title">Years of experience</h4>
                                    <div class="__lt-filter-input">
                                        <div class="__lt-input">
                                            <label>
                                                <input name="year_exp" type="number" min="0.5" max="20" placeholder="0.5 - 10" />
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="__lt-filter-group">
                                    <h4 class="__lt-filter-title">Experience in Australia </h4>
                                    <div class="__lt-filter-input">
                                        <div class="__lt-input">
                                            <label>
                                                <input name="year_exp_aus" type="number" min="0.5" max="20" placeholder="0.5 - 10" />
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="__lt-filter-group">
                                    <h4 class="__lt-filter-title">Barista skills</h4>
                                    <div class="__lt-filter-checkbox-group">
                                        <?php foreach ( $barista_skills as $skill ) { ?>
                                            <div class="__lt-checkbox">
                                                <label>
                                                    <input name="barista_skills[]" type="checkbox" value="<?= $skill ?>" />
                                                    <span><?= $skill ?></span>
                                                </label>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="__lt-filter-group">
                                    <h4 class="__lt-filter-title">Volume</h4>
                                    <div class="__lt-filter-checkbox-group">
                                        <?php foreach ( $volumes as $v ) { ?>
                                            <div class="__lt-checkbox">
                                                <label>
                                                    <input name="volumes[]" type="checkbox" value="<?= $v ?>" />
                                                    <span><?= $v ?></span>
                                                </label>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="__lt-filter-group">
                                    <h4 class="__lt-filter-title">Hospitality skills</h4>
                                    <div class="__lt-filter-checkbox-group">
                                        <?php foreach ( $hospitality_skills as $v ) { ?>
                                            <div class="__lt-checkbox">
                                                <label>
                                                    <input name="hospitality_skills[]" type="checkbox" value="<?= $v ?>" />
                                                    <span><?= $v ?></span>
                                                </label>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>

                            </div>
							<div class="__lt-content-side">
								<div class="__lt-items-wrap">
                                    <?php for ( $i = 1; $i <= 10; $i ++ ) {
	                                    get_lt_item( $i );
                                    } ?>
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
