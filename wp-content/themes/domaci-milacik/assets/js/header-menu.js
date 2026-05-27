document.addEventListener("DOMContentLoaded", function () {
	const headerNav = document.querySelector("#header-nav");
	const headerToggle = document.querySelector("#header-toggle");
	const headerMenu = document.querySelector("#header-menu");

	if (!headerNav) return;

	headerToggle.addEventListener("click", function (event) {
		event.preventDefault();

		headerToggle.classList.toggle("open");
		headerMenu.classList.toggle("hidden");
		document.body.classList.toggle("overflow-hidden");
	});
});
