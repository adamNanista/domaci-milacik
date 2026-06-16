document.addEventListener("DOMContentLoaded", function () {
	const subheaderNav = document.querySelector("#subheader-nav");
	const subheaderToggle = document.querySelector("#subheader-toggle");
	const subheaderMenu = document.querySelector("#subheader-menu");

	const headerNav = document.querySelector("#header-nav");
	const headerToggle = document.querySelector("#header-toggle");
	const headerMenu = document.querySelector("#header-menu");

	function updateBodyScroll() {
		const anyMenuOpen = (headerMenu && !headerMenu.classList.contains("hidden")) || (subheaderMenu && !subheaderMenu.classList.contains("hidden"));

		document.body.classList.toggle("overflow-hidden", anyMenuOpen);
	}

	if (headerNav && headerToggle && headerMenu) {
		headerToggle.addEventListener("click", function (event) {
			event.preventDefault();

			subheaderMenu?.classList.add("hidden");
			headerToggle.classList.toggle("open");
			headerMenu.classList.toggle("hidden");

			updateBodyScroll();
		});
	}

	if (subheaderNav && subheaderToggle && subheaderMenu) {
		subheaderToggle.addEventListener("click", function (event) {
			event.preventDefault();

			headerMenu?.classList.add("hidden");
			subheaderToggle.classList.toggle("open");
			subheaderMenu.classList.toggle("hidden");

			updateBodyScroll();
		});
	}
});
