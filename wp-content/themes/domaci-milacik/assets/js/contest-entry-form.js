document.addEventListener("DOMContentLoaded", function () {
	"use strict";

	const form = document.getElementById("contest-entry-form");
	const messages = document.getElementById("contest-entry-form-messages");
	const submitBtn = document.getElementById("contest-entry-form-submit");
	const videoToggles = document.querySelectorAll("input[name='contest-entry-form-video-type']");

	// Video toggles
	videoToggles.forEach((radio) => {
		radio.addEventListener("change", function () {
			const uploadPanel = document.getElementById("contest-entry-form-video-upload-panel");
			const urlPanel = document.getElementById("contest-entry-form-video-url-panel");
			const urlInput = document.getElementById("contest-entry-form-video-url");
			const uploadInput = document.getElementById("contest-entry-form-video-upload");

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

	// Validation
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
				validator: (value, fields) => {
					const files = fields["#contest-entry-form-photo"].elem.files;
					return files.length > 0;
				},
				errorMessage: "Fotografia je povinná.",
			},
			{
				rule: "files",
				value: {
					extensions: ["jpeg", "jpg", "png"],
					maxSize: 5000000,
					types: ["image/jpeg", "image/jpg", "image/png"],
				},
				errorMessage: "Fotografia musí mať menej ako 5 MB.",
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

					document.getElementById("contest-entry-form-video-upload-panel").classList.remove("hidden");
					document.getElementById("contest-entry-form-video-url-panel").classList.add("hidden");

					window.scrollTo({
						top: messages.offsetTop - 40,
						behavior: "smooth",
					});
				} else {
					showError(result.data.message || "Niečo sa pokazilo. Skúste to prosím znova.");
				}
			} catch (error) {
				setLoading(false);
				showError("Vyskytla sa chyba siete. Skontrolujte svoje pripojenie a skúste to znova.");
			}
		});

	function setLoading(isLoading) {
		const text = submitBtn.querySelector("#contest-entry-form-submit-text");
		const loader = submitBtn.querySelector("#contest-entry-form-submit-loading");

		if (isLoading) {
			submitBtn.disabled = true;
			text.classList.add("hidden");
			loader.classList.remove("hidden");
		} else {
			submitBtn.disabled = false;
			text.classList.remove("hidden");
			loader.classList.add("hidden");
		}
	}

	function clearMessages() {
		messages.classList.remove("contest-entry-form-success", "contest-entry-form-error");
		messages.textContent = "";
	}

	function showSuccess(msg) {
		messages.classList.remove("contest-entry-form-error");
		messages.classList.add("contest-entry-form-success");
		messages.textContent = msg;
	}

	function showError(msg) {
		messages.classList.remove("contest-entry-form-success");
		messages.classList.add("contest-entry-form-error");
		messages.textContent = msg;
	}
});
