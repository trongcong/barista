<?php
/**
 * The template for displaying the footer.
 *
 * @package OceanWP WordPress theme
 */

?>

	</main><!-- #main -->

	<?php do_action( 'ocean_after_main' ); ?>

	<?php do_action( 'ocean_before_footer' ); ?>

	<?php
	// Elementor `footer` location.
	if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) {
		?>

		<?php do_action( 'ocean_footer' ); ?>

	<?php } ?>

	<?php do_action( 'ocean_after_footer' ); ?>

</div><!-- #wrap -->

<?php do_action( 'ocean_after_wrap' ); ?>

</div><!-- #outer-wrap -->

<?php do_action( 'ocean_after_outer_wrap' ); ?>

<?php
// If is not sticky footer.
if ( ! class_exists( 'Ocean_Sticky_Footer' ) ) {
	get_template_part( 'partials/scroll-top' );
}
?>

<?php
// Search overlay style.
if ( 'overlay' === oceanwp_menu_search_style() ) {
	get_template_part( 'partials/header/search-overlay' );
}
?>

<?php
// If sidebar mobile menu style.
if ( 'sidebar' === oceanwp_mobile_menu_style() ) {

	// Mobile panel close button.
	if ( get_theme_mod( 'ocean_mobile_menu_close_btn', true ) ) {
		get_template_part( 'partials/mobile/mobile-sidr-close' );
	}
	?>

	<?php
	// Mobile Menu (if defined).
	get_template_part( 'partials/mobile/mobile-nav' );
	?>

	<?php
	// Mobile search form.
	if ( get_theme_mod( 'ocean_mobile_menu_search', true ) ) {
		ob_start();
		get_template_part( 'partials/mobile/mobile-search' );
		echo ob_get_clean();
	}
}
?>

<?php
// If full screen mobile menu style.
if ( 'fullscreen' === oceanwp_mobile_menu_style() ) {
	get_template_part( 'partials/mobile/mobile-fullscreen' );
}
?>

<?php wp_footer(); ?>
<script src="<?php echo get_stylesheet_directory_uri() ?>/js/owl.carousel.min.js"></script>
<script>
	jQuery('.team_slider').owlCarousel({
	    loop:true,
	    center:true,
	    margin:0,
	    dots: true,
	    nav:false,
	    smartSpeed: 900,
	    stagePadding: 180,
	    responsive:{
	        0:{
	        	margin:0,
	            items:1,
	            stagePadding: 0,
	            // autoHeight:true
	        },
	        600:{
	        	margin:0,
	            items:1,
	            stagePadding: 0,
	            // autoHeight:false,
	        },
	        1025:{
	            items:1,
	            stagePadding: 190,
	        },
	        1199:{
	        	items:1,
	        	stagePadding: 220,
	        },
	        1599:{
	        	items:1,
	        	stagePadding: 180,
	        }

	    }
	});

	jQuery('.team_slider .owl-item.active.center').next().addClass('next-item');
	jQuery('.team_slider .owl-item.active.center').prev().addClass('prev-item');
	jQuery('.team_slider').on('changed.owl.carousel', function(e) {
		jQuery('.team_slider .owl-item.active.center').next().removeClass('next-item');
		jQuery('.team_slider .owl-item.active.center').prev().removeClass('prev-item');
		setTimeout(function(){ 
			jQuery('.team_slider .owl-item.active.center').next().addClass('next-item');
			jQuery('.team_slider .owl-item.active.center').prev().addClass('prev-item');
		}, 50);
		
	});

	// Footer Js
		// var checkWidth = jQuery(window).width();
		// if (checkWidth <= 767) {
		//     // run the accordion
		//   var allPanels = jQuery('.footer_content').hide();
		//   var heads = jQuery('.footer_title');
		//   jQuery(heads).on('click', function() {
		//       $this = jQuery(this);
		//       $target =  $this.parent().find('.footer_content');
		//       if(!$target.hasClass('active')){
		//           heads.removeClass('selected');
		//           $this.addClass('selected');
		//           allPanels.removeClass('active').slideUp();
		//           $target.addClass('active').slideDown();
		//       }
		//   });
	 //    };

	    var checkWidth = jQuery(window).width();
	    if (checkWidth <= 1024) {

		    jQuery('.footer_content').addClass('active').next().show();

			jQuery('.footer_title').click(function(){
			if( jQuery(this).next().is(':hidden') ) {
	          jQuery('.footer_title').removeClass('active').next().slideUp();
	          jQuery(this).toggleClass('active').next().slideDown();
			}
	          
	        
	        else{
	          jQuery('.footer_title').removeClass('active').next().slideUp();
			}  
	          
	          
			return false;
			});
		};
	//Our Case Slider
	jQuery(".experience_inner_sec > .elementor-container > .elementor-row").addClass("experience_slide owl-carousel owl-theme");
	jQuery('.experience_slide').owlCarousel({
		autoplay: false,
		dots: false,
		margin: 10, 
		nav: true,
		items: 3,
		loop:true,
		navText: ['<i class="fa fa-angle-left" aria-hidden="true"></i>', '<i class="fa fa-angle-right" aria-hidden="true"></i>'],
		responsive:{
	        0:{
	            items:1,
				dots: true,
				nav: false
	        },
	        600:{
	            items:2,
				dots: false,
				nav: true
	        },
	        1025:{
	            items:3
	        }
	    }
	});

//Our Case Slider
	jQuery(".explore_slide > .elementor-column-wrap > .elementor-widget-wrap").addClass("explore_slider owl-carousel owl-theme");
	jQuery('.explore_slider').owlCarousel({
		autoplay: false,
		dots: true,
		margin: 0, 
		nav: false,
		items: 1,
		loop:true,
	});
	
	//Our Testimonial
	jQuery(".testimonial_slider_col > .elementor-column-wrap > .elementor-widget-wrap").addClass("testimonial_slider owl-carousel owl-theme");
	jQuery('.testimonial_slider').owlCarousel({
		autoplay:true,
		dots: true,
		margin: 0, 
		nav: false,
		items: 1,
		smartSpeed: 200,
		autoplaySpeed: 2000,
		loop:true,
		navText: ['<i class="fa fa-angle-left" aria-hidden="true"></i>', '<i class="fa fa-angle-right" aria-hidden="true"></i>'],
	});
</script>
</body>
</html>
