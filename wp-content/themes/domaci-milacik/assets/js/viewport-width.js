function calculateViewportWidth() {
	document.documentElement.style.setProperty("--viewport-width", document.documentElement.clientWidth + "px");
}

window.addEventListener("resize", calculateViewportWidth);
document.addEventListener("DOMContentLoaded", calculateViewportWidth);
