(function ($) {
	"use strict";

	$(function () {
		var $form = $("#contest-entry-form");
		var $messages = $("#contest-entry-form-messages");
		var $submit = $("#contest-entry-form-submit");
		var $videoToggle = $("input[name='contest-entry-form-video-type']");

		$videoToggle.on("change", function () {
			if ($(this).val() === "upload") {
				$("#contest-entry-form-video-upload-panel").removeClass("hidden");
				$("#contest-entry-form-video-url-panel").addClass("hidden");
				$("#contest-entry-form-video-url").val("");
			} else {
				$("#contest-entry-form-video-upload-panel").addClass("hidden");
				$("#contest-entry-form-video-upload").val("");
				$("#contest-entry-form-video-url-panel").removeClass("hidden");
			}
		});

		$form.on("submit", function (e) {
			e.preventDefault();

			clearMessages();

			var errors = [];
			if (!$.trim($("#contest-entry-form-owner-name").val())) errors.push("Name is required.");
			if (!$.trim($("#contest-entry-form-owner-email").val())) errors.push("Email is required.");
			if (!$.trim($("#contest-entry-form-pet-name").val())) errors.push("Pet name is required.");
			if (!$.trim($("#contest-entry-form-pet-description").val())) errors.push("Pet description is required.");

			var photoFiles = $("#contest-entry-form-photo")[0].files;
			if (!photoFiles || photoFiles.length === 0) errors.push("A photo is required.");

			if (!$("#contest-entry-form-consent-combined").is(":checked")) errors.push("Consent is required.");

			if (errors.length) {
				showError(errors.join(" "));
				return;
			}

			setLoading(true);

			var formData = new FormData($form[0]);
			formData.append("action", "contest_entry_form_submit_entry");
			formData.append("nonce", contest_entry_form_ajax.nonce);

			$.ajax({
				url: contest_entry_form_ajax.ajax_url,
				type: "POST",
				data: formData,
				processData: false,
				contentType: false,
				success: function (response) {
					setLoading(false);
					if (response.success) {
						showSuccess(response.data.message);
						$form[0].reset();
						$("#contest-entry-form-video-upload-panel").removeClass("hidden");
						$("#contest-entry-form-video-url-panel").addClass("hidden");
						$("html, body").animate({ scrollTop: $messages.offset().top - 40 }, 400);
					} else {
						showError(response.data.message || "Something went wrong. Please try again.");
					}
				},
				error: function () {
					setLoading(false);
					showError("A network error occurred. Please check your connection and try again.");
				},
			});
		});

		function setLoading(isLoading) {
			if (isLoading) {
				$submit.prop("disabled", true);
				$submit.find("#contest-entry-form-submit-text").addClass("hidden");
				$submit.find("#contest-entry-form-submit-loading").removeClass("hidden");
			} else {
				$submit.prop("disabled", false);
				$submit.find("#contest-entry-form-submit-text").removeClass("hidden");
				$submit.find("#contest-entry-form-submit-loading").addClass("hidden");
			}
		}

		function clearMessages() {
			$messages.removeClass("contest-entry-form-success contest-entry-form-error").text("");
		}

		function showSuccess(msg) {
			$messages.removeClass("contest-entry-form-error").addClass("contest-entry-form-success").text(msg);
		}

		function showError(msg) {
			$messages.removeClass("contest-entry-form-success").addClass("contest-entry-form-error").text(msg);
		}
	});
})(jQuery);
