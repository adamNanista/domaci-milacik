async function copyToClipboard(text) {
	if (navigator.clipboard && window.isSecureContext) {
		try {
			await navigator.clipboard.writeText(text);
			return true;
		} catch (error) {}
	}

	const textarea = document.createElement("textarea");
	textarea.value = text;

	textarea.style.position = "fixed";
	textarea.style.top = "-9999px";
	textarea.setAttribute("readonly", "");

	document.body.appendChild(textarea);

	textarea.select();
	textarea.focus();

	const success = document.execCommand("copy");
	textarea.remove();

	return success;
}
