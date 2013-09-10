$('a[rel=tooltip]').tooltip({
	'placement': 'bottom'
});


$('.navbar a, .subnav a').smoothScroll();


(function ($) {

	$(function(){

		// fix sub nav on scroll
		var $win = $(window),
				$body = $('body'),
				$nav = $('.subnav'),
				navHeight = $('.navbar').first().height(),
				subnavHeight = $('.subnav').first().height(),
				subnavTop = $('.subnav').length && $('.subnav').offset().top - navHeight,
				marginTop = parseInt($body.css('margin-top'), 10);
				isFixed = 0;

		processScroll();

		$win.on('scroll', processScroll);

		function processScroll() {
			var i, scrollTop = $win.scrollTop();

			if (scrollTop >= subnavTop && !isFixed) {
				isFixed = 1;
				$nav.addClass('subnav-fixed');
				$body.css('margin-top', marginTop + subnavHeight + 'px');
			} else if (scrollTop <= subnavTop && isFixed) {
				isFixed = 0;
				$nav.removeClass('subnav-fixed');
				$body.css('margin-top', marginTop + 'px');
			}
		}

        window.prettyPrint && prettyPrint();
		
		jQuery('.thumbnails .span2').fadeTo('slow', 0.9);
		jQuery('.thumbnails .span2').hover(function(){
			jQuery(this).fadeTo('fast', 1);
		},function(){
			jQuery(this).fadeTo('fast', 0.8); 
		});
		
		var $container = $('.thumbnails');

		$container.imagesLoaded( function(){
			$container.masonry({
				itemSelector : 'li.span2',
				isAnimated: true
			});
		});

		jQuery('.thumbnails .span2').hover(function(){
			jQuery(this).css('z-index','100');
		},function(){
			jQuery(this).css('z-index','10');
		});
		
		jQuery('.close').click(function () {
			jQuery('.alert').fadeOut('slow');
		});	

	});

})(window.jQuery);