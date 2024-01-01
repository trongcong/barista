<?php
//session_start();
//
//if( isset( $_SESSION['barista_view'] ) ) {
//$_SESSION['barista_view'] += 1;
//}else {
//$_SESSION['barista_view'] = 1;
//}
global $post;
//current_user_can( 'barista' ) &&
$is_author_can_edit = is_user_logged_in() && $post->post_author == get_current_user_id();
$is_edit_barista    = isset( $_GET['edit'] ) && $is_author_can_edit;
$title              = "THANK YOU FOR SEEING MY PROFILE";
if ( $is_edit_barista ) {
	acf_form_head();
	$title = "UPDATE PROFILE";
}
get_header();

do_action( 'ocean_before_content_wrap' );
lt_add_page_header( $title );
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
					the_post();
					$phone_number = get_field( "your_phone_number" );
					$email        = get_field( "your_email" );
					$avatar       = get_field( "your_avatar" );
					$video_url    = get_video_url( get_the_ID() );
					if ( $is_edit_barista ) { ?>
						<div class="__lt-single-update">
							<h6><a href="<?= get_barista_profile_link( false ) ?>"><< Back to profile</a></h6>
							<?php acf_form( array(
								'post_id'      => get_the_ID(),
								'return'       => get_the_permalink(),
								'uploader'     => 'basic',
								'submit_value' => "Update profile",
							) ); ?>
						</div>
					<?php } else { ?>
						<div class="__lt-single-barista">
							<div class="__wrap">
								<?= $is_author_can_edit ? '<div class="__edit-profile-wrap">
                                    <a href="' . get_the_permalink() . '?edit" title="Edit profile">
                                        <span>Update my profile</span>
                                        <span class="__edit-profile">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 700 700">
                                          <path d="M355.6 252.56a154.47 154.47 0 0 1-85.68 25.76c-31.36 0-61.04-9.52-85.68-25.76a107.64 107.64 0 0 0-92.96 106.96v69.44c0 6.16 5.04 11.2 11.2 11.2H310.8l127.12-127.12c-15.12-31.92-45.92-54.88-82.32-60.48zM378 121.52c0 59.38-48.14 107.52-107.52 107.52-59.38 0-107.52-48.14-107.52-107.52C162.96 62.14 211.1 14 270.48 14 329.86 14 378 62.14 378 121.52m-44.8 351.12c-1.68 1.68-2.24 3.36-2.8 5.04l-10.64 56c-1.12 7.28 5.04 13.44 11.76 11.76l56-10.64c2.24-.56 3.92-1.12 5.04-2.8l10.64-10.64L343.84 462zm37.79-37.57 125.52-125.52 59.8 59.8-125.53 125.51zM604.8 301.84l-41.44-41.44a13.19 13.19 0 0 0-18.48 0l-21.84 21.84 59.36 59.92 21.84-21.84a12.7 12.7 0 0 0 .56-18.48z" />
                                        </svg>
                                        </span>
                                    </a>
                                    </div>' : '' ?>
								<div class="__head-wrap">
									<div class="__avatar">
										<img
											src="<?= ! empty( $avatar ) ? wp_get_attachment_image_url( $avatar['ID'] ) : 'https://via.placeholder.com/120x120.png' ?>"
											alt="avatar">
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
											<?= $phone_number ? '<div class="__contact-item __tell"><a href="tel:' . $phone_number . '">' . $phone_number . '</a></div>' : '' ?>
											<?= $email ? '<div class="__contact-item __email"><a href="mailto:' . $email . '">' . $email . '</a></div>' : '' ?>
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
											<h4>Barista Experience (Years)</h4>
											<div><?= get_field( "years_of_experience" )['label'] ?></div>
										</div>
										<div class="__ct-info-item __ct-year-exp-aus">
											<h4>Retail or Hospitality Experience</h4>
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
													<a target="_blank"
													   href="<?php echo $resume['url']; ?>"><?php echo $resume['filename']; ?></a>
												<?php else:
													echo "No Resume";
												endif;
												?>
											</div>
										</div>
									</div>
									<div class="__ct-right">
										<div
											class="__ct-des"><?= get_field( "describe_yourself_in_2_sentences" ) ?></div>
										<?php
										if ( $video_url ) { ?>
											<div class="__ct-item __ct-video">
												<h4 class="__ct-title __video-title">Watch me in action</h4>
												<div class="__video-iframe">
													<iframe width="1417" height="537"
													        src="https://www.youtube.com/embed/<?= $video_url ?>"
													        title="Watch me in action" frameborder="0"
													        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
													        allowfullscreen></iframe>
												</div>
											</div>
										<?php } ?>

										<div class="__ct-item __ct-photos">
											<h4 class="__ct-title __photo-title">My Photos</h4>
											<div class="__photos-grid">
												<?php
												if ( have_rows( 'your_photos' ) ):
													while ( have_rows( 'your_photos' ) ) : the_row();
														$photo = get_sub_field( 'photo_item' );
														echo '<div class="photo-item"><a target="_blank" href="' . $photo . '"><img src="' . $photo . '" alt="photo"></a></div>';
													endwhile;
												else :
													echo '<p>Does not have any photos.</p>';
												endif;
												?>
											</div>
										</div>

									</div>
								</div>
							</div>
						</div>
					<?php }
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
