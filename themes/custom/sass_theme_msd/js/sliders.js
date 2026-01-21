(function ($, Drupal) {
	Drupal.behaviors.backgroundSlider = {
		attach: function (context, settings) {

			// Generic helper
			function initSlick($carousel, options) {
				if (!$carousel.length) return; // ✅ ensure element exists
				if ($carousel.hasClass('slick-initialized')) {
					$carousel.slick('unslick');
				}
				try {
					$carousel.slick(options);
				} catch (err) {
					console.error('Slick init failed for:', $carousel, err);
				}
			}

			once('mySlickReinit', '[dir="rtl"] .slider-v1', context).forEach((el) => {
				initSlick($(el), {
					slidesToShow: 9,
					slidesToScroll: 1,
					arrows: true,
					infinite: true,
					autoplay: true,
					rtl: true,
					speed: 500,
					responsive: [
						{ breakpoint: 1440, settings: { slidesToShow: 2 } },
						{ breakpoint: 780, settings: { slidesToShow: 1 } }
					]
				});
			});
			once('mySlickReinit', '[dir="ltr"] .slider-v1', context).forEach((el) => {
				initSlick($(el), {
					slidesToShow: 9,
					slidesToScroll: 1,
					arrows: true,
					infinite: true,
					autoplay: true,
					speed: 500,
					responsive: [
						{ breakpoint: 1280, settings: { slidesToShow: 2 } },
						{ breakpoint: 768, settings: { slidesToShow: 1 } }
					]
				});
			});


			once('mySlickReinit', '[dir="rtl"] .slider-v2', context).forEach((el) => {
				initSlick($(el), {
					slidesToShow: 3,
					slidesToScroll: 1,
					arrows: false,
					infinite: true,
					autoplay: true,
					rtl: true,
					speed: 500,
					responsive: [
						{ breakpoint: 1440, settings: { slidesToShow: 2 } },
						{ breakpoint: 780, settings: { slidesToShow: 1, arrows: false } }
					]
				});
			});
			once('mySlickReinit', '[dir="ltr"] .slider-v2', context).forEach((el) => {
				initSlick($(el), {
					slidesToShow: 3,
					slidesToScroll: 1,
					arrows: false,
					infinite: true,
					autoplay: false,
					speed: 500,
					responsive: [
						{ breakpoint: 1440, settings: { slidesToShow: 2 } },
						{ breakpoint: 780, settings: { slidesToShow: 1, arrows: false } }
					]
				});
			});


			once('mySlickReinit', '[dir="rtl"] .slider-v3', context).forEach((el) => {
				initSlick($(el), {
					slidesToShow: 3,
					slidesToScroll: 1,
					arrows: false,
					infinite: true,
					autoplay: false,
					dots: true,
					rtl: true,
					speed: 500,
					responsive: [
						{ breakpoint: 1440, settings: { slidesToShow: 2 } },
						{ breakpoint: 780, settings: { slidesToShow: 1, arrows: false } }
					]
				});
			});
			once('mySlickReinit', '[dir="ltr"] .slider-v3', context).forEach((el) => {
				initSlick($(el), {
					slidesToShow: 3,
					slidesToScroll: 1,
					arrows: false,
					infinite: true,
					autoplay: false,
					dots: true,
					speed: 500,
					centerMode: true,
					centerPadding: "120px", // adjust padding until HALF card shows
					responsive: [
						{ breakpoint: 1440, settings: { slidesToShow: 2, centerPadding: "80px" } },
						{ breakpoint: 780, settings: { slidesToShow: 1, centerPadding: "40px", arrows: false } }
					]
				});

			});

		}
	};

})(jQuery, Drupal);
