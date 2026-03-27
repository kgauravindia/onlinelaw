(function($) {

	'use strict';

	//Cache jQuery Selector
	var $window     		= $(window),
		$header     		= $('#page-header'),
	    $brand				= $('.partner-slider'),		
		$dropdown   		= $('.dropdown-toggle'),	
		$attorneies 		= $('.attorney-items'),
		$member_list_view	= $('.member-list-view'),
		$attorneies2 		= $('.talented-attorney'),
		$latest_blog_post	= $('.latest-blog-post'),	
		$about_processes	= $('.about-process-slider'),	
		$testimonials		= $('.testimonial-1'),		
		$testimonials_2		= $('.testimonials-2'),		
		$testimonials_3		= $('.testimonial-3'),		
		$awards_block_1		= $('.award-block-1'),		
		$service_slider		= $('.service-slider'),
		$contact			= $('#contact-form');
	
	
	
	// Running the code when the document is ready
    $(document).ready(function() {
		$('#slider').layerSlider({
			sliderVersion: '6.0.0',
			type: 'fullwidth',
			responsiveUnder: 0,
			layersContainer: 1200,
			maxRatio: 1,
			parallaxScrollReverse: true,
			hideUnder: 0,
			hideOver: 100000,
			skin: 'outline',
			showBarTimer: true,
			thumbnailNavigation: 'disabled',
			allowRestartOnResize: true,
			skinsPath: 'skins/',
			height: 800
		});
		
		$('#slider1').layerSlider({
			sliderVersion: '6.5.0b1',
			type: 'fullsize',
			allowFullscreen: false,
			maxRatio: 1,
			parallaxCenterLayers: 'top',
			hideUnder: 567,
			skin: 'v6',
			globalBGImage: 'images/attorny.jpg',
			navPrevNext: false,
			hoverPrevNext: false,
			navStartStop: false,
			navButtons: false,
			showCircleTimer: false,
			allowRestartOnResize: true,
			skinsPath: 'skins/'
		});
	});
	
	
	
	// Update Header Style + Scroll to Top
	function headerStyle() {
		if($header.length){
			var windowpos = $window.scrollTop();
			if (windowpos >= 100) {
				$header.addClass('fixed-header');
			} else {
				$header.removeClass('fixed-header');
			}
		}
	}
	
	
	
	// Dropdown submenu on hover in desktopand dropdown sub menu on click in mobile	
	$('#bs-example-navbar-collapse-1').each(function() {
		$dropdown.on('click', function(e){
			if($window.width() < 1100){
				if($(this).parent('.dropdown').hasClass('visible')){
				//	$(this).parent('.dropdown').children('.dropdown-menu').first().stop(true, true).slideUp(300);
				//	$(this).parent('.dropdown').removeClass('visible');
					window.location = $(this).attr('href');
				}
				else{
					e.preventDefault();
					$(this).parent('.dropdown').siblings('.dropdown').children('.dropdown-menu').slideUp(300);
					$(this).parent('.dropdown').siblings('.dropdown').removeClass('visible');
					$(this).parent('.dropdown').children('.dropdown-menu').slideDown(300);
					$(this).parent('.dropdown').addClass('visible');
				}
				e.stopPropagation();
			}
		});
		
		$('body').on('click', function(e){
			$dropdown.parent('.dropdown').removeClass('visible');
		});
		
		$window.on('resize', function(){
			if($window.width() > 991){
				$('.dropdown-menu').removeAttr('style');
				$('.dropdown ').removeClass('visible');
			}
		});
		
	});
	
	
		
	// Account dropdown submenu on hover in desktopand dropdown sub menu on click in mobile
	$('#bs-example-navbar-collapse-1').each(function() {	
		$('.account-dropdown').click(function(e) {
			if($window.width() > 992){
				e.stopPropagation();
			}
		});		
	});
	
	
	
	// Auto active class adding with navigation
	$window.on('load', function(){
		var current = location.pathname;
		var $path = current.substring(current.lastIndexOf('/') + 1);
		$('.navbar-nav li a').each(function(e){
			var $this = $(this);
			// if the current path is like this link, make it active
			if($path == $this.attr('href')){
				$this.parent('li').addClass('active');
			}
			else if($path == ''){
				$('.navbar-nav li.first').addClass('active');
			}
		})
	})
	
	
	
	
	// Click Search Icon and Open Search Field	
	var $srcicon = $('.toogle-search'),
		$srcfield = $('.search-content');
		$srcicon.on('click', function(event){
		event.preventDefault();
		$srcfield.addClass('active');
		event.stopPropagation();
	});
	
	$('.src-close').on('click', function(){
		$srcfield.removeClass('active');
	});
	
	$srcfield.on('click', function(event){
		event.stopPropagation();
	});
	
	$window.on('click', function(e){
		$srcfield.removeClass('active');
	});
	
	
	
	
	// Fact Counter For Achivement Counting
	function factCounter() {
		if($('.fact-counter').length){
			$('.fact-counter .count.animated').each(function() {
				var $t = $(this),
				 n = $t.find(".count-num").attr("data-stop"),
				 r = parseInt($t.find(".count-num").attr("data-speed"), 10);
				 
				if (!$t.hasClass("counted")) {
				 $t.addClass("counted");
				 $({
				  countNum: $t.find(".count-text").text()
				 }).animate({
				  countNum: n
				 }, {
				  duration: r,
				  easing: "linear",
				  step: function() {
				   $t.find(".count-num").text(Math.floor(this.countNum));
				  },
				  complete: function() {
				   $t.find(".count-num").text(this.countNum);
				  }
				 });
				}

				//set skill building height
				 var size = $(this).children('.progress-bar').attr('aria-valuenow');
				 $(this).children('.progress-bar').css('width', size+'%');

			});
		}
	}
	
	

	// Use for Accordion Box
	if($('.faq-item').length){
		$('.question-toogle').on('click', function() {
			if($(this).hasClass('active')){
				$(this).addClass('active');			
			}
			else{
				$('.question-toogle').removeClass('active');
				$('.faq-answer').slideUp(300);
				$(this).addClass('active');
				$(this).next('.faq-answer').slideDown(300);	
			}
		});	
	}
	
	
	
	// Gallery With Filters List
	if($('.filter-list').length){
		$('.filter-list').mixItUp({});
	}
	
	
	
	// Testimonials Slider Auto
	if ($testimonials.length) {
		$testimonials.owlCarousel({
			loop:true,
			margin:30,
			nav:false,
			dots: true,
			smartSpeed: 500,
			autoplay: false,
			responsive:{
				0:{
					items:1
				},
				480:{
					items:1
				},
				600:{
					items:1
				},
				800:{
					items:1
				},
				1200:{
					items:1
				}
			}
		});    		
	}
	
	
	
	
	// Testimonials Slider Auto
	if ($testimonials_2.length) {
		$testimonials_2.owlCarousel({
			loop:true,
			margin:30,
			nav:false,
			dots: true,
			smartSpeed: 500,
			autoplay: false,
			responsive:{
				0:{
					items:1
				},
				480:{
					items:1
				},
				600:{
					items:1
				},
				800:{
					items:2
				},
				1200:{
					items:2
				}
			}
		});    		
	}
	
	
	
	
	// Testimonials Slider Auto
	if ($testimonials_3.length) {
		$testimonials_3.owlCarousel({
			loop:true,
			margin:30,
			nav:false,
			dots: true,
			smartSpeed: 500,
			autoplay: false,
			responsive:{
				0:{
					items:1
				}
			}
		});    		
	}
	
	
	
	
	// Award-block One Slider Auto
	if ($awards_block_1.length) {
		$awards_block_1.owlCarousel({
			loop:true,
			margin:30,
			nav:false,
			dots: true,
			smartSpeed: 500,
			autoplay: false,
			responsive:{
				0:{
					items:1
				}
			}
		});    		
	}
	
	
	
	
	// Service Slider Auto
	if ($service_slider.length) {
		$service_slider.owlCarousel({
			loop:true,
			margin:30,
			nav:false,
			dots: true,
			smartSpeed: 500,
			autoplay: 4000,
			responsive:{
				0:{
					items:1
				},
				480:{
					items:1
				},
				600:{
					items:1
				},
				800:{
					items:1
				},
				1200:{
					items:1
				}
			}
		});    		
	}
	
	
	
	
	// Our Partner Logos Slider Auto
	if ($brand.length) {
		$brand.owlCarousel({
			loop:true,
			margin:30,
			nav:true,
			dots: false,
			smartSpeed: 500,
			autoplay: 4000,
			responsive:{
				0:{
					items:1
				},
				480:{
					items:2
				},
				600:{
					items:3
				},
				800:{
					items:4
				},
				1200:{
					items:6
				}
			}
		});    		
	}
	
	
	
	// Attorney Logos Slider Auto
	if ($attorneies.length) {
		$attorneies.owlCarousel({
			loop:true,
			margin:30,
			nav:true,
			dots: false,
			smartSpeed: 500,
			autoplay: 4000,
			autoplayHoverPause:true,
			responsive:{
				0:{
					items:1
				},
				480:{
					items:2
				},
				700:{
					items:2
				},
				991:{
					items:2
				},
				1024:{
					items:3
				},
				1200:{
					items:4
				}
			}
		});    		
	}
	
	
	
	// Attorney Logos Slider Auto
	if ($member_list_view.length) {
		$member_list_view.owlCarousel({
			loop:true,
			margin:30,
			nav:true,
			dots: true,
			smartSpeed: 500,
			autoplay: 4000,
			autoplayHoverPause:true,
			responsive:{
				0:{
					items:1
				},
				480:{
					items:1
				},
				600:{
					items:1
				},
				991:{
					items:1
				},
				1200:{
					items:1
				}
			}
		});    		
	}
	
	
	
	// Talented Attorney Slider Auto
	if ($attorneies2.length) {
		$attorneies2.owlCarousel({
			loop:true,
			margin:30,
			nav:true,
			dots: false,
			smartSpeed: 500,
			autoplay: 4000,
			responsive:{
				0:{
					items:1
				},
				480:{
					items:2
				},
				600:{
					items:2
				},
				800:{
					items:2
				},
				1200:{
					items:3
				}
			}
		});    		
	}
	
	
	
	
	// Latest Blog Post Slider Auto
	if ($latest_blog_post.length) {
		$latest_blog_post.owlCarousel({
			loop:true,
			margin:30,
			nav:true,
			dots: false,
			smartSpeed: 500,
			autoplay: 4000,
			responsive:{
				0:{
					items:1
				},
				480:{
					items:1
				},
				600:{
					items:2
				},
				800:{
					items:2
				},
				1200:{
					items:2
				}
			}
		});    		
	}
	

	
	// Elements Animation
	if($('.wow').length){
		var wow = new WOW(
		  {
			boxClass:     'wow',      // animated element css class (default is wow)
			animateClass: 'animated', // animation css class (default is animated)
			offset:       0,          // distance to the element when triggering the animation (default is 0)
			mobile:       true,       // trigger animations on mobile devices (default is true)
			live:         true       // act on asynchronously loaded content (default is true)
		  }
		);
		wow.init();
	}



    // When document is Scrollig, do
	$(window).on('scroll', function() {
		factCounter();
		headerStyle();
	});
	
	
	
	// Cases column chart is Scrollig, do	
	function viewGraph(){
		$('.column').css('height','0');
		$('table tr').each(function(index) {
		var ha = $(this).children('td').eq(1).text();
		$('#col'+index).animate({height: ha}, 1500).html("<div>"+ha+"</div>");
		});
		}
		$(document).ready(function(){
		viewGraph();
	});
	
	
	
	
	// Contact Form Validation
	if($contact.length){
		$contact.validate({  //#contact-form contact form id
			rules: {
				firstname: {
					required: true    // Field name here
				},
				email: {
					required: true, // Field name here
					email: true
				},
				subject: {
					required: true
				},
				message: {
					required: true
				}
			},
			
			messages: {
                firstname: "Please enter your First Name", //Write here your error message that you want to show in contact form
                email: "Please enter valid Email", //Write here your error message that you want to show in contact form
                subject: "Please enter your Subject", //Write here your error message that you want to show in contact form
				message: "Please write your Message" //Write here your error message that you want to show in contact form
            },

            submitHandler: function (form) {
                $('#send').attr({'disabled' : 'true', 'value' : 'Sending...' });
                $.ajax({
                    type: "POST",
                    url: "email.php",
                    data: $(form).serialize(),
                    success: function () {
                        $('#send').removeAttr('disabled').attr('value', 'Send');
                        $( "#success").slideDown( "slow" );
                        setTimeout(function() {
                        $( "#success").slideUp( "slow" );
                        }, 5000);
                        form.reset();
                    },
                    error: function() {
                        $('#send').removeAttr('disabled').attr('value', 'Send');
                        $( "#error").slideDown( "slow" );
                        setTimeout(function() {
                        $( "#error").slideUp( "slow" );
                        }, 5000);
                    }
                });
                return false; // required to block normal submit since you used ajax
            }

		});
	}
	
	
	
	
	// Youtube and Vimeo video popup control
	jQuery(function(){
		$.fn.YouTubePopUp = function(options) {
			var YouTubePopUpOptions = $.extend({
					autoplay: 1
			}, options );

			$(this).on('click', function (e) {

				var youtubeLink = $(this).attr("href");

				if( youtubeLink.match(/(youtube.com)/) ){
					var split_c = "v=";
					var split_n = 1;
				}

				if( youtubeLink.match(/(youtu.be)/) || youtubeLink.match(/(vimeo.com\/)+[0-9]/) ){
					var split_c = "/";
					var split_n = 3;
				}

				if( youtubeLink.match(/(vimeo.com\/)+[a-zA-Z]/) ){
					var split_c = "/";
					var split_n = 5;
				}

				var getYouTubeVideoID = youtubeLink.split(split_c)[split_n];

				var cleanVideoID = getYouTubeVideoID.replace(/(&)+(.*)/, "");

				if( youtubeLink.match(/(youtu.be)/) || youtubeLink.match(/(youtube.com)/) ){
					var videoEmbedLink = "https://www.youtube.com/embed/"+cleanVideoID+"?autoplay="+YouTubePopUpOptions.autoplay+"";
				}

				if( youtubeLink.match(/(vimeo.com\/)+[0-9]/) || youtubeLink.match(/(vimeo.com\/)+[a-zA-Z]/) ){
					var videoEmbedLink = "https://player.vimeo.com/video/"+cleanVideoID+"?autoplay="+YouTubePopUpOptions.autoplay+"";
				}

				$("body").append('<div class="YouTubePopUp-Wrap YouTubePopUp-animation"><div class="YouTubePopUp-Content"><span class="YouTubePopUp-Close"></span><iframe src="'+videoEmbedLink+'" allowfullscreen></iframe></div></div>');

				if( $('.YouTubePopUp-Wrap').hasClass('YouTubePopUp-animation') ){
					setTimeout(function() {
						$('.YouTubePopUp-Wrap').removeClass("YouTubePopUp-animation");
					}, 600);
				}

				$(".YouTubePopUp-Wrap, .YouTubePopUp-Close").click(function(){
					$(".YouTubePopUp-Wrap").addClass("YouTubePopUp-Hide").delay(515).queue(function() { $(this).remove(); });
				});

				e.preventDefault();

			});

			$(document).keyup(function(e) {

				if ( e.keyCode == 27 ){
					$('.YouTubePopUp-Wrap, .YouTubePopUp-Close').click();
				}

			});

		};
		
		jQuery("a.video-popup").YouTubePopUp();
		//jQuery("a.video-popup").YouTubePopUp( { autoplay: 0 } ); // Disable autoplay
	});
	
	
	
	// Template Settings Right Side Option
	jQuery(function(){
		$('.color-panel').each(function(){
			$('.on-panel').on('click', function(){
				$('.color-panel').toggleClass('open');
			});
			
			$('.color-box li').on('click', function(){
				$('.color-box li').removeClass('active');
				$(this).addClass('active');
				 var path = $(this).data('path');
				 var logo1 = $(this).data('image');
				 var logo2 = $(this).data('target');
				 $('#color-change').attr('href', path);
				 $('.nav-logo').attr('src', logo1);
				 $('.logo-bottom').attr('src', logo2);
			});	
			
			// Template color change
			$(".color-box li").each( function() {		
				if ($.cookie('unilex-color') && $.cookie('unilex-color') == $(this).attr('class')) {
					$(this).addClass('active');
					var path = $(this).data('path');
					var logo1 = $(this).data('image');
					var logo2 = $(this).data('target');
					$('#color-change').attr('href', path);
					$('.nav-logo').attr('src', logo1);
					$('.logo-bottom').attr('src', logo2);
				}
			});
			
			$(".color-box li").on('click',function() {
				var file_name = $(this).attr('data-name');
				$.cookie('unilex-color', file_name, { path: '/', expires: 365 });
			});
			
			// Layout select with slide button
			$("#layout-type").each( function() {
				var name = $(this).attr('name');
				if ($.cookie(name) && $.cookie(name) == "boxLayout") {
					$(this).prop('checked', true);
					$('#page-wrapper').addClass('box-layout');
				}
			});
			
			$("#layout-type").change(function() {
				var name = $(this).attr('name');
				if($(this).prop('checked')){
					$.cookie(name, 'boxLayout', { path: '/', expires: 365 });
				}
				else {
					$.cookie(name, '', { path: '/', expires: 365 });
				}
			});
			
			$('#layout-type').on('click', function(){	
				if($(this).is(':checked')){
					$('#page-wrapper').addClass('box-layout');
					location.reload();
				}
				else {
					$('#page-wrapper').removeClass('box-layout');
					location.reload();
				}
			});
			
			
			// Background select with check
			$(".box-bg-style li input[type='radio']").on('click', function(){
				$('body').removeAttr('class');
				if($(this).is(':checked')){
					var class_nm = $(this).attr('value');
					$('body').addClass(class_nm);
				}
				var name = $("#bg-over").attr('name');
				if ($.cookie(name) && $.cookie(name) == "true") {
					$(this).prop('checked', $.cookie(name));
					$('body').addClass('body-overlay');
				}
			});
			
			$(".box-bg-style li input[type='radio']").each( function() {		
				if ($.cookie('bg-layout') && $.cookie('bg-layout') == $(this).attr('value')) {
					$(this).prop('checked', true);
					$('body').addClass($.cookie('bg-layout'));
				}
			});
			
			$(".box-bg-style li input[type='radio']").change(function() {
				var name = $(this).attr('value');
				if($(this).prop('checked')){
					$.cookie('bg-layout', name, { path: '/', expires: 365 });
				}
			});
			
			// Background Overly settinhs
			$("#bg-over").each( function() {
				var name = $(this).attr('name');
				if ($.cookie(name) && $.cookie(name) == "true") {
					$(this).prop('checked', $.cookie(name));
					$('body').addClass('body-overlay');
				}
			});
			
			$("#bg-over").change(function() {
				var name = $(this).attr('name');
				$.cookie(name, $(this).prop('checked'), { path: '/', expires: 365 });
			});
			
			$('#bg-over').on('click', function(){	
				if($(this).is(':checked')){
					$('body').addClass('body-overlay');
				}
				else {
					$('body').removeClass('body-overlay');
				}
			});
			
		});
		
	})
	
	
})(jQuery);