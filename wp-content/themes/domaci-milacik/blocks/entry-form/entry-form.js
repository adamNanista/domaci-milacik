document.addEventListener("DOMContentLoaded", function () {
	const ALLOWED_HOSTS = ["youtube.com", "youtu.be", "vimeo.com"];

	// DOM
	const form = document.querySelector("#contest-entry-form");
	const messages = document.querySelector("#contest-entry-form-messages");
	const submitButton = document.querySelector("#contest-entry-form-submit");
	const photoUploadPanel = document.querySelector("#contest-entry-form-photo-panel");
	const photoUploadInput = document.querySelector("#contest-entry-form-photo");
	const videoToggles = document.querySelectorAll("input[name='contest-entry-form-video-type']");
	const videoUploadPanel = document.querySelector("#contest-entry-form-video-upload-panel");
	const videoUploadInput = document.querySelector("#contest-entry-form-video-upload");
	const videoUrlPanel = document.querySelector("#contest-entry-form-video-url-panel");
	const videoUrlInput = document.querySelector("#contest-entry-form-video-url");

	if (!form) return;

	// Dropzones
	createDropzone(photoUploadPanel, photoUploadInput);
	createDropzone(videoUploadPanel, videoUploadInput);

	// Validation & submission
	const validation = new JustValidate(form, {
		errorFieldStyle: {},
		errorFieldCssClass: "validation-error",
		errorLabelStyle: {},
		errorLabelCssClass: "error-message",
		focusInvalidField: false,
	});

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
		.addField(
			"#contest-entry-form-photo",
			[
				{
					rule: "minFilesCount",
					value: 1,
					errorMessage: "Fotografia je povinná.",
				},
				{
					rule: "maxFilesCount",
					value: 3,
					errorMessage: "Môžete nahrať maximálne 3 fotografie.",
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
			],
			{
				errorsContainer: ".dropzone",
			},
		)
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
		.addField(
			"#contest-entry-form-video-upload",
			[
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
			],
			{
				errorsContainer: ".dropzone",
			},
		)
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

					videoUrlPanel.classList.remove("hidden");
					videoUploadPanel.classList.add("hidden");

					const dropzoneFiles = document.querySelectorAll(".dropzone-file");

					if (dropzoneFiles.length) {
						dropzoneFiles.forEach((dropzoneFile) => {
							dropzoneFile.remove();
						});
					}
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

				console.error("AJAX form error:", error);

				if (window.turnstile) {
					turnstile.reset();
				}

				showError("Vyskytla sa chyba siete. Skontrolujte svoje pripojenie a skúste to znova.");
			}
		});

	// Video toggles
	videoToggles.forEach((radio) => {
		radio.addEventListener("change", function (event) {
			if (this.value === "upload") {
				videoUploadPanel.classList.remove("hidden");
				videoUrlPanel.classList.add("hidden");
				videoUrlInput.value = "";
			} else {
				videoUploadPanel.classList.add("hidden");
				videoUploadInput.value = "";
				videoUrlPanel.classList.remove("hidden");
			}
		});
	});

	// Helpers
	function setLoading(isLoading) {
		form.classList.toggle("loading", isLoading);

		submitButton.disabled = isLoading;
		submitButton.textContent = isLoading ? "Odosielam prihlášku" : "Odoslať prihlášku";
	}

	function clearMessages() {
		messages.parentElement.classList.add("hidden");
		messages.parentElement.classList.remove("success", "error");
		messages.textContent = "";
	}

	function showSuccess(message) {
		messages.parentElement.classList.remove("hidden", "error");
		messages.parentElement.classList.add("success");
		messages.textContent = message;
	}

	function showError(message) {
		messages.parentElement.classList.remove("hidden", "success");
		messages.parentElement.classList.add("error");
		messages.textContent = message;
	}

	function createDropzone(dropzone, input) {
		input.addEventListener("change", function (event) {
			renderFiles(dropzone, input);
		});

		dropzone.addEventListener("dragover", function (event) {
			event.preventDefault();
			dropzone.classList.add("dragover");
		});

		dropzone.addEventListener("dragleave", function (event) {
			event.preventDefault();
			dropzone.classList.remove("dragover");
		});

		dropzone.addEventListener("drop", function (event) {
			event.preventDefault();
			dropzone.classList.remove("dragover");
			const dataTransfer = new DataTransfer();

			const files = event.dataTransfer.files;
			if (!files.length) return;

			files.forEach((file) => dataTransfer.items.add(file));

			[...event.dataTransfer.files].forEach((file) => {
				if (dataTransfer.files.length < 3) dataTransfer.items.add(file);
			});

			input.files = dataTransfer.files;
			renderFiles(dropzone, input);
		});
	}

	function renderFiles(dropzone, input) {
		const dropzoneFile = dropzone.parentElement.querySelector(".dropzone-file");

		if (dropzoneFile) {
			dropzoneFile.remove();
		}

		[...input.files].forEach((file) => {
			const div = document.createElement("div");
			div.className = "dropzone-file";
			div.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-image-icon lucide-image"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg> ${
				file.name
			} [${Math.round((file.size / 1024 / 1024) * 100) / 100} MB]`;
			dropzone.after(div);
		});
	}
});
