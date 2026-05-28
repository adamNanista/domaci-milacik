document.addEventListener("DOMContentLoaded", function (event) {
	const leaderboardSliderElement = document.querySelector("#leaderboard-slider");

	if (!leaderboardSliderElement) return;

	const leaderboardSlider = new Swiper(leaderboardSliderElement, {
		slidesPerView: "auto",
		spaceBetween: 12,
		breakpoints: {
			768: {
				slidesPerView: "auto",
				spaceBetween: 16,
			},
		},
	});
});
