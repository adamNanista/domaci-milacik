document.addEventListener("DOMContentLoaded", function (event) {
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
