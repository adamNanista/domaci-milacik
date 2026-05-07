document.addEventListener("DOMContentLoaded", function () {
	const ALLOWED_HOSTS = ["youtube.com", "youtu.be", "vimeo.com"];

	// DOM
	const form = document.querySelector("#contest-entry-form");
	const messages = document.querySelector("#contest-entry-form-messages");
	const submitButton = document.querySelector("#contest-entry-form-submit");
	const videoToggles = document.querySelectorAll("input[name='contest-entry-form-video-type']");
	const uploadPanel = document.querySelector("#contest-entry-form-video-upload-panel");
	const uploadInput = document.querySelector("#contest-entry-form-video-upload");
	const urlPanel = document.querySelector("#contest-entry-form-video-url-panel");
	const urlInput = document.querySelector("#contest-entry-form-video-url");

	if (!form) return;

	// Validation & submission
	const validation = new window.JustValidate(form);

	validation
		.addField("#contest-entry-form-owner-name", [
			{
				rule: "required",
				errorMessage: "Meno je povinné.",
			},
			{
				rule: "maxLength",
				value: 100,
				errorMessage: "Meno môže mať maximálne 100 znakov.",
			},
		])
		.addField("#contest-entry-form-owner-email", [
			{
				rule: "required",
				errorMessage: "Email je povinný.",
			},
			{
				rule: "email",
				errorMessage: "Neplatná emailová adresa.",
			},
			{
				rule: "maxLength",
				value: 100,
				errorMessage: "Email môže mať maximálne 100 znakov.",
			},
		])
		.addField("#contest-entry-form-pet-name", [
			{
				rule: "required",
				errorMessage: "Meno miláčika je povinné.",
			},
			{
				rule: "maxLength",
				value: 100,
				errorMessage: "Meno miláčika môže mať maximálne 100 znakov.",
			},
		])
		.addField("#contest-entry-form-pet-description", [
			{
				rule: "required",
				errorMessage: "Popis miláčika je povinný.",
			},
			{
				rule: "maxLength",
				value: 2000,
				errorMessage: "Popis miláčika môže mať maximálne 2 000 znakov.",
			},
		])
		.addField("#contest-entry-form-photo", [
			{
				rule: "minFilesCount",
				value: 1,
				errorMessage: "Fotografia je povinná.",
			},
			{
				rule: "files",
				value: {
					files: {
						maxSize: 5242880,
					},
				},
				errorMessage: "Fotografia musí mať menej ako 5 MB.",
			},
			{
				rule: "files",
				value: {
					files: {
						extensions: ["jpeg", "jpg", "png"],
						types: ["image/jpeg", "image/jpg", "image/png"],
					},
				},
				errorMessage: "Fotografia musí byť vo formáte JPG alebo PNG.",
			},
		])
		.addField("#contest-entry-form-video-upload", [
			{
				rule: "files",
				value: {
					files: {
						maxSize: 31457280,
					},
				},
				errorMessage: "Video musí mať menej ako 30 MB.",
			},
			{
				rule: "files",
				value: {
					files: {
						extensions: ["mp4"],
						types: ["video/mp4"],
					},
				},
				errorMessage: "Video musí byť vo formáte MP4.",
			},
		])
		.addField("#contest-entry-form-video-url", [
			{
				validator: (value) => {
					if (!value) return true;
					try {
						const url = new URL(value);
						return url.protocol === "http:" || url.protocol === "https:";
					} catch {
						return false;
					}
				},
				errorMessage: "Zadajte platnú URL adresu.",
			},
			{
				validator: (value) => {
					if (!value) return true;
					try {
						const url = new URL(value);
						const bare = url.hostname.replace(/^www\./, "").toLowerCase();
						return ALLOWED_HOSTS.includes(bare);
					} catch {
						return false;
					}
				},
				errorMessage: "Povolené sú iba odkazy na YouTube alebo Vimeo.",
			},
		])
		.addField("#contest-entry-form-consent-combined", [
			{
				rule: "required",
				errorMessage: "Súhlas je povinný.",
			},
		])
		.onSuccess(async (event) => {
			clearMessages();

			const token = form.querySelector("[name='cf-turnstile-response']");

			if (!token || !token.value) {
				showError("Overenie zlyhalo. Skúste to znova.");
				return;
			}

			setLoading(true);

			const formData = new FormData(form);
			formData.append("action", "contest_entry_form_submit_entry");
			formData.append("nonce", contest_entry_form_ajax.nonce);

			try {
				const response = await fetch(contest_entry_form_ajax.ajax_url, {
					method: "POST",
					body: formData,
				});

				const result = await response.json();

				setLoading(false);

				if (window.turnstile) {
					turnstile.reset();
				}

				if (result.success) {
					form.reset();

					if (result.data.message) {
						showSuccess(result.data.message);
					}

					uploadPanel.classList.remove("hidden");
					urlPanel.classList.add("hidden");
				} else {
					if (result.data.message) {
						showError(result.data.message || "Niečo sa pokazilo. Skúste to prosím znova.");
					}

					if (result.data.fields && typeof result.data.fields === "object") {
						const fieldErrors = {};

						Object.entries(result.data.fields).forEach(([id, message]) => {
							fieldErrors[`#${id}`] = message;
						});

						validation.showErrors(fieldErrors);
					}
				}
			} catch (error) {
				setLoading(false);

				if (window.turnstile) {
					turnstile.reset();
				}

				showError("Vyskytla sa chyba siete. Skontrolujte svoje pripojenie a skúste to znova.");
			}
		});

	// Video toggles
	videoToggles.forEach((radio) => {
		radio.addEventListener("change", function () {
			if (this.value === "upload") {
				uploadPanel.classList.remove("hidden");
				urlPanel.classList.add("hidden");
				urlInput.value = "";
			} else {
				uploadPanel.classList.add("hidden");
				uploadInput.value = "";
				urlPanel.classList.remove("hidden");
			}
		});
	});

	// Helpers
	function setLoading(isLoading) {
		submitButton.disabled = isLoading;
		submitButton = isLoading ? "Odosielam prihlášku" : "Odoslať prihlášku";
	}

	function clearMessages() {
		messages.parentElement.classList.add("hidden");
		messages.parentElement.classList.remove("messages--success", "messages--error");
		messages.textContent = "";
	}

	function showSuccess(message) {
		messages.parentElement.classList.remove("hidden");
		messages.parentElement.classList.add("messages--success");
		messages.textContent = message;
	}

	function showError(message) {
		messages.parentElement.classList.remove("hidden");
		messages.parentElement.classList.add("messages--error");
		messages.textContent = message;
	}
});
