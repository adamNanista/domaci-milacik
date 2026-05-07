document.addEventListener("DOMContentLoaded", function () {
	const latestArticlesSwiperElement = document.querySelector("#latest-articles-slider");

	if (!latestArticlesSwiperElement) return;

	const latestArticlesSwiper = new Swiper(latestArticlesSwiperElement, {
		slidesPerView: 1,
		spaceBetween: 16,
		loop: true,
		autoplay: {
			delay: 5000,
			pauseOnMouseEnter: true,
		},
		breakpoints: {
			768: {
				slidesPerView: 2,
				spaceBetween: 16,
			},
			1025: {
				slidesPerView: 3,
				spaceBetween: 16,
			},
		},
	});
});
