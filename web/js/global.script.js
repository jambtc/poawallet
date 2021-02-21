"use strict";

var spinner = '<div class="button-spinner spinner-border text-primary" role="status">'
                 +'<span class="sr-only">Loading...</span></div>';

$(document).ready(function(){

	$('.navi-menu-button').on('click', function(e){
		navMenuOpen();
	});

	$('.nav-menu').on('click', function(e){
		if ($(e.target).hasClass('nav-menu')){
			navMenuClose();
		}
	});

	$('nav.menu ul.main-menu>li>a').on('click', function(e){
		var that = $(this);
		if (that.parent().find('ul:first').length)
		{
			e.preventDefault();
			if (!that.parent().hasClass('active'))
			{
				$('nav.menu ul.main-menu ul').slideUp('fast',function(){
					$('nav.menu ul.main-menu > li').removeClass('active');
				});

				$('nav.menu ul li a span').removeClass('fa-angle-up').addClass('fa-angle-down');


				that.parent().find('ul:first').slideDown('fast',function(){
					that.parent().addClass('active');
				});

				that.find('span').removeClass('fa-angle-down').addClass('fa-angle-up');
			}
			else
			{

				that.parent().find('ul:first').slideUp('fast',function(){
					$(this).parent().removeClass('active');
				});
				that.find('span').removeClass('fa-angle-up').addClass('fa-angle-down');
			}
		}
		else
		{
			$('nav.menu ul.main-menu ul').slideUp('fast');
			$('nav.menu ul.main-menu > li').removeClass('active');
			that.parent().addClass('active');
		}
	});


	$('.tab-item .fix-width .menu-item').css({'width': 100/$('.tab-item .fix-width .menu-item').length+'%'});

	// if ($('.wizard').length)
	// {
	// 	wizardFixHeight();
	// 	$(window).resize();
	// }
	//
	// if ($('.wizard').length) {
	//     $(".wizard").Turbo({
	//     	items:1,
	//     	circular:false
	//     });
	// }

	if ($('.animated-text').length)
		animateText();

});


$(".wrapper-inline").on("scroll", function(e) {
	if (this.scrollTop > 50) {
		$('header.no-background').addClass("set-bg");
	} else {
		$('header.no-background').removeClass("set-bg");
	}

});

var navMenuOpen = function(){
	$('.navi-menu-button').addClass('focused');

	$('div.nav-menu').fadeIn(50,function(e){
		$('nav.menu').addClass('opened');
	});
}

var navMenuClose = function(){
	$('.navi-menu-button').removeClass('focused');

	$('nav.menu').removeClass('opened');
	$('div.nav-menu').fadeOut(200);
}

// var wizardFixHeight = function(){
// 	$(window).on('resize', function(e){
// 		$('.wizard .wizard-item').height($(window).height()-50);
// 	});
// }

var animateText = function(){
	$('.vertical-center').css({'margin-top':$(window).height()/2 - $('.vertical-center').height()/2});
	$('.animated-text').removeClass('zero-opacity');
	$('[data-transation]').each(function(e,i){
		var that = $(this);
		that.addClass('hide');

		var transation = that.attr('data-transation');
		if (transation == '')
			transation = 'fadeInDown';

		var startTime = parseInt(that.attr('data-start-time'));
		if (isNaN(startTime))
			startTime = 0;

		setTimeout(function(){
			that.addClass('animated '+transation);
		},startTime);
	})
}


/*sweet checkbox scripts*/
$('.sweet-check :checkbox:checked').each(function(e,i){
	$(this).parent().addClass('checked');
});


$(document).on('click', '.sweet-check', function(){
	if ($(this).hasClass('checked'))
	{
		$(this).removeClass('checked');
		$(this).find('input').prop('checked', false);
	}
	else
	{
		$(this).addClass('checked');
		$(this).find('input').prop('checked', true);
	}

	//console.log($(this).find('input').prop('checked'));
});

$(document).on('click','[data-loader]', function(){
	$('.sweet-loader').show().addClass('show');
});


/*expandable list scrips****/
$(document).on('click', '.expandable-item .expandable-header', function(){
	if ($(this).parent().hasClass('accordion'))
	{
		if ($(this).parent().hasClass('active'))
		{
			$(this).parent().removeClass('active');
			$(this).parent().find('.expandable-content').attr('style','');
		}
		else
		{
			var accordionGroup = $(this).parent().attr('data-group');
			$('[data-group="'+accordionGroup+'"]').removeClass('active');
			$('[data-group="'+accordionGroup+'"]').find('.expandable-content').attr('style','');
			$(this).parent().find('.expandable-content').css({'max-height':$(this).parent().find('.expandable-content')[0].scrollHeight});
			$(this).parent().addClass('active');
		}
	}
	else
	{
		if ($(this).parent().hasClass('active'))
			$(this).parent().find('.expandable-content').attr('style','');
		else
			$(this).parent().find('.expandable-content').css({'max-height':$(this).parent().find('.expandable-content')[0].scrollHeight});

		$(this).parent().toggleClass('active');
	}
});



$(document).on('click', '.tab-item .menu-item', function(e){
	e.preventDefault();
	var tabContentId = $(this).attr('data-content');

	$(this).parents('.tab-item').find('.menu-item').removeClass('active');
	$(this).addClass('active');

	$(this).parents('.tab-item').find('.content-item').removeClass('active');
	$('#'+tabContentId).addClass('active');
});


/*post item scripts **************/
$(document).on('click', '.post-item .post-share > i', function(e){
	e.preventDefault();
	$(this).parent().find('.social-links').fadeToggle('fast');
});


/*popup actions ******************/
$(document).on('click', '[data-dismiss="true"]', function(){
	$(this).parents('.popup-overlay').fadeOut('fast');
});

$(document).on('click', '[data-popup]', function(){
	var modalId = $(this).attr('data-popup');
	$('#'+modalId).fadeIn('fast');
});

$(document).on('click', '.popup-overlay', function(e){
	if ($(e.target).hasClass('popup-overlay'))
	{
		$(this).fadeOut('fast');
	}
});



/*search popup actions ************/

var openSearchPopup = function(){
	$('.search-form').fadeIn('fast');
	$('.search-form input').focus();
}

var closeSearchPopup = function(){
	$('.search-form').fadeOut('fast');
}

$(document).on('click', '[data-search="open"]', function(){
	openSearchPopup();
});

$(document).on('click', '[data-search="close"]', function(){
	closeSearchPopup();
});


// ------------------------------------------------------- //
// Swiper Slider
// ------------------------------------------------------ //
if($('.swiper-container').length || $('.swiper-recievers').length){
	var swiper = new Swiper('.swiper-container', {
		slidesPerView: 2,
		breakpoints: {
	        400: {
	            slidesPerView: 1
	        }
	    },
		pagination: {
		    el: '.swiper-pagination',
		},
	});

	var swiper = new Swiper('.swiper-recievers', {
		slidesPerView: 4,
		breakpoints: {
	        400: {
	            slidesPerView:3
	        }
	    }
	});
}
