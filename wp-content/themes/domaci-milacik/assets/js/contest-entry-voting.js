document.addEventListener("DOMContentLoaded", function () {
	// DOM
	const voteCount = document.querySelector("#contest-vote-count");
	const voteButton = document.querySelector("#contest-vote-button");
	const messages = document.querySelector("#contest-vote-messages");

	// Voting
	voteButton.addEventListener("click", async function (event) {
		event.preventDefault();

		const postId = btn.dataset.postId;

		clearMessages();

		setLoading(true);

		try {
			const response = await fetch(contest_entry_voting_ajax.ajax_url, {
				method: "POST",
				headers: {
					"Content-Type": "application/x-www-form-urlencoded",
				},
				body: new URLSearchParams({
					action: "contest_vote",
					nonce: contest_entry_voting_ajax.nonce,
					post_id: postId,
				}),
			});

			const result = await response.json();

			setLoading(false);

			if (result.success) {
				showSuccess(result.data.message);
			} else {
				showError(result.data.message || "Niečo sa pokazilo. Skúste to prosím znova.");
			}
		} catch (error) {
			setLoading(false);
			showError(error, "Vyskytla sa chyba siete. Skontrolujte svoje pripojenie a skúste to znova.");
		}
	});

	// Helpers
	function setLoading(isLoading) {
		const text = voteButton.querySelector("#contest-vote-button-text");
		const loader = voteButton.querySelector("#contest-vote-button-loading");

		if (isLoading) {
			voteButton.disabled = true;
			text.classList.add("hidden");
			loader.classList.remove("hidden");
		} else {
			voteButton.disabled = false;
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
