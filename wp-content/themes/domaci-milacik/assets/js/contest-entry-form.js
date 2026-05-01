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

	// Validation & submission
	const validation = new window.JustValidate(form);

	validation
		.addField("#contest-entry-form-owner-name", [
			{
				rule: "required",
				errorMessage: "Meno je povinné.",
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
		])
		.addField("#contest-entry-form-pet-name", [
			{
				rule: "required",
				errorMessage: "Meno miláčika je povinné.",
			},
		])
		.addField("#contest-entry-form-pet-description", [
			{
				rule: "required",
				errorMessage: "Popis miláčika je povinný.",
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
						extensions: ["jpeg", "jpg", "png"],
						maxSize: 2000000,
						types: ["image/jpeg", "image/jpg", "image/png"],
					},
				},
				errorMessage: "Fotografia musí mať menej ako 5 MB.",
			},
		])
		.addField("#contest-entry-form-video-upload", [
			{
				rule: "files",
				value: {
					files: {
						extensions: ["mp4"],
						maxSize: 30000000,
						types: ["video/mp4"],
					},
				},
				errorMessage: "Video musí mať menej ako 30 MB.",
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

				if (result.success) {
					showSuccess(result.data.message);
					form.reset();

					uploadPanel.classList.remove("hidden");
					urlPanel.classList.add("hidden");
				} else {
					if (result.data.message) {
						showError(result.data.message || "Niečo sa pokazilo. Skúste to prosím znova.");
					}
					if (Object.keys(result.data.fields).length) {
						const fieldErrors = {};

						Object.entries(result.data.fields).forEach(([id, message]) => {
							fieldErrors[`#${id}`] = message;
						});

						validation.showErrors(fieldErrors);

						console.log(result.data.fields);
					}
				}
			} catch (error) {
				setLoading(false);
				showError(error, "Vyskytla sa chyba siete. Skontrolujte svoje pripojenie a skúste to znova.");
			}
		});

	// Helpers
	function setLoading(isLoading) {
		const text = submitButton.querySelector("#contest-entry-form-submit-text");
		const loader = submitButton.querySelector("#contest-entry-form-submit-loading");

		if (isLoading) {
			submitButton.disabled = true;
			text.classList.add("hidden");
			loader.classList.remove("hidden");
		} else {
			submitButton.disabled = false;
			text.classList.remove("hidden");
			loader.classList.add("hidden");
		}
	}

	function clearMessages() {
		messages.classList.remove("contest-entry-form-success", "contest-entry-form-error");
		messages.textContent = "";
	}

	function showSuccess(message) {
		messages.classList.remove("contest-entry-form-error");
		messages.classList.add("contest-entry-form-success");
		messages.textContent = message;
	}

	function showError(message) {
		messages.classList.remove("contest-entry-form-success");
		messages.classList.add("contest-entry-form-error");
		messages.textContent = message;
	}
});
