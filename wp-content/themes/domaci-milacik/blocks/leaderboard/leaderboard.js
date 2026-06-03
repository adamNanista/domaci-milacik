document.addEventListener("DOMContentLoaded", function (event) {
	const leaderboardSliderElement = document.querySelector("#leaderboard-slider");

	if (!leaderboardSliderElement) return;

	const leaderboardSlider = new Swiper(leaderboardSliderElement, {
		slidesPerView: "auto",
		spaceBetween: 16,
		watchSlidesProgress: true,
		navigation: {
			prevEl: "#leaderboard-button-prev",
			nextEl: "#leaderboard-button-next",
		},
	});
});
