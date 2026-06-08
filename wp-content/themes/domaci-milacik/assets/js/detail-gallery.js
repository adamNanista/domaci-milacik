document.addEventListener("DOMContentLoaded", function (event) {
	const detailSliderElement = document.querySelector("#detail-slider");

	if (!detailSliderElement) return;

	const detailSlider = new Swiper(detailSliderElement, {
		slidesPerView: 1,
		spaceBetween: 16,
		navigation: {
			prevEl: "#detail-button-prev",
			nextEl: "#detail-button-next",
		},
	});

	Fancybox.bind("[data-fancybox]", {
		Carousel: {
			Toolbar: {
				display: {
					left: [],
					middle: [],
					right: ["close"],
				},
			},
		},
	});
});
