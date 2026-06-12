document.addEventListener("DOMContentLoaded", function () {
	const latestArticlesSwiperElement = document.querySelector("#latest-articles-slider");

	if (!latestArticlesSwiperElement) return;

	const latestArticlesSwiper = new Swiper(latestArticlesSwiperElement, {
		slidesPerView: 1,
		spaceBetween: 16,
		autoplay: {
			delay: 5000,
			pauseOnMouseEnter: true,
		},
		navigation: {
			prevEl: "#latest-articles-button-prev",
			nextEl: "#latest-articles-button-next",
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
