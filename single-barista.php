<?php
//session_start();
//
//if( isset( $_SESSION['barista_view'] ) ) {
//$_SESSION['barista_view'] += 1;
//}else {
//$_SESSION['barista_view'] = 1;
//}

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
					$phone_number = get_field( "your_phone_number" );
					$email        = get_field( "your_email" );
					$avatar       = get_field( "your_avatar" );
					?>
                    <div class="__lt-single-barista">
                        <div class="__wrap">
                            <div class="__head-wrap">
                                <div class="__avatar">
                                    <img src="<?= ! empty( $avatar ) ? wp_get_attachment_image_url( $avatar['ID'] ) : 'https://via.placeholder.com/120x120.png' ?>" alt="avatar">
                                </div>
                                <div class="__info-wrap">
                                    <div class="__info-left">
                                        <div class="__name"><?= get_field( "full_name" ) ?></div>
	                                    <?php
	                                    $certification = get_certification_by_barista( get_the_ID() );
	                                    if ( ! empty( $certification ) ) {
		                                    foreach ( $certification as $cer ) {
			                                    echo '<div class="__exp">🏅' . $cer . '</div>';
		                                    }
	                                    }
	                                    ?>
                                    </div>
                                    <div class="__info-right">
                                        <?= $phone_number ? '<div class="__contact-item"><span>Contacted:</span> <a href="tel:' . $phone_number . '">' . $phone_number . '</a></div>' : '' ?>
                                        <?= $email ? '<div class="__contact-item"><span>Email me at:</span> <a href="mailto:' . $email . '">' . $email . '</a></div>' : '' ?>
                                    </div>
                                </div>
                            </div>
                            <div class="__content">
                                <div class="__ct-left">
                                    <h3 class="__label">View profile</h3>
                                    <div class="__ct-info-item __ct-analytics">
                                        <div>This month, I have been</div>
                                        <div class="__analytics-wrap">
                                            <div class="__seen">
                                                <strong>Seen <?= get_barista_view( get_the_ID() ); ?> times</strong>
                                            </div>
                                            <div class="__contacted">
                                                <strong>Contacted <?= get_barista_contacted( get_the_ID() ); ?>
                                                    times</strong>
                                            </div>
                                        </div>
                                    </div>
	                                <?php if ( get_field( "preferred_name" ) ) { ?>
                                        <div class="__ct-info-item __ct-preferred-name">
                                            <h4>Preferred Name</h4>
                                            <div><?= get_field( "preferred_name" ) ?></div>
                                        </div>
	                                <?php } ?>
                                    <div class="__ct-info-item __ct-year-exp">
                                        <h4>Years Of Experience</h4>
                                        <div><?= get_field( "years_of_experience" )['label'] ?></div>
                                    </div>
                                    <div class="__ct-info-item __ct-year-exp-aus">
                                        <h4>Experience in Australia</h4>
                                        <div><?= get_field( "experience_in_australia" )['label'] ?></div>
                                    </div>
                                    <div class="__ct-info-item __ct-barista-skills">
                                        <h4>Barista Skills</h4>
                                        <div>
                                            <?php
                                            $barista_skills = get_field( 'barista_skills' );
                                            if ( $barista_skills ): ?>
                                                <ul>
                                                    <?php foreach ( $barista_skills as $skill ): ?>
                                                        <li><?= $skill; ?></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="__ct-info-item __ct-volume">
                                        <h4>Barista Skills</h4>
                                        <div>
                                            <?= get_field( 'volume_you_are_able_to_handle_solo' ); ?>
                                        </div>
                                    </div>
                                    <div class="__ct-info-item __ct-hospitality-skills">
                                        <h4>Hospitality Skills</h4>
                                        <div>
                                            <?php
                                            $hospitality_skills = get_field( 'hospitality_skills' );
                                            if ( $hospitality_skills ): ?>
                                                <ul>
                                                    <?php foreach ( $hospitality_skills as $skill ): ?>
                                                        <li><?= $skill; ?></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="__ct-info-item __ct-your_resume">
                                        <h4>Resume</h4>
                                        <div>
                                            <?php
                                            $resume = get_field( 'your_resume' );
                                            if ( $resume ): ?>
                                                <a target="_blank" href="<?php echo $resume['url']; ?>"><?php echo $resume['filename']; ?></a>
                                            <?php else:
	                                            echo "No Resume";
                                            endif;
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="__ct-right">
                                    <div class="__ct-des"><?= get_field( "describe_yourself_in_2_sentences" ) ?></div>
                                    <div class="__ct-video">
                                        <h4 class="__video-title">Watch me in action</h4>
                                        <div class="__video-iframe">
                                            <iframe width="1417" height="537" src="https://www.youtube.com/embed/<?= get_video_url( get_the_ID() ) ?>" title="Watch me in action" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                                        </div>
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
	<?php //do_action( 'ocean_after_primary' ); ?>
</div><!-- #content-wrap -->
<?php do_action( 'ocean_after_content_wrap' ); ?>
<?php get_footer(); ?>
