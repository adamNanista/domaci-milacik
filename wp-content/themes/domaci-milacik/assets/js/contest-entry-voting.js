document.addEventListener("DOMContentLoaded", function () {
	// DOM
	const voteCount = document.querySelector("#contest-vote-count");
	const voteButton = document.querySelector("#contest-vote-button");
	const messages = document.querySelector("#contest-vote-messages");

	// Voted
	if (sessionStorage.getItem("contest_voted_" + postId)) {
		setVoted();
	}

	// Voting
	voteButton.addEventListener("click", async function (event) {
		event.preventDefault();

		const postId = voteButton.dataset.postId;

		clearMessages();
		setLoading(true);

		try {
			const response = await fetch(contest_entry_voting_ajax.ajax_url, {
				method: "POST",
				headers: { "Content-Type": "application/x-www-form-urlencoded" },
				body: new URLSearchParams({
					action: "contest_vote",
					nonce: contest_entry_voting_ajax.nonce,
					post_id: postId,
				}),
			});

			const result = await response.json();

			setLoading(false);

			if (result.success) {
				voteCount.textContent = result.data.votes;
				sessionStorage.setItem("voted_" + postId, "1");
				setVoted();
				showSuccess(result.data.message);
			} else {
				showError(result.data.message || "Niečo sa pokazilo. Skúste to prosím znova.");
			}
		} catch (error) {
			setLoading(false);
			showError("Vyskytla sa chyba siete. Skontrolujte svoje pripojenie a skúste to znova.");
		}
	});

	// Helpers
	function setLoading(isLoading) {
		const text = voteButton.querySelector("#contest-vote-button-text");
		const loader = voteButton.querySelector("#contest-vote-button-loading");
		voteButton.disabled = isLoading;
		text.classList.toggle("hidden", isLoading);
		loader.classList.toggle("hidden", !isLoading);
	}

	function setVoted() {
		voteButton.disabled = true;
		const text = voteButton.querySelector("#contest-vote-button-text");
		const loader = voteButton.querySelector("#contest-vote-button-loading");
		const voted = voteButton.querySelector("#contest-vote-button-loading");
		text.classList.add("hidden");
		loader.classList.add("hidden");
		voted.classList.remove("hidden");
	}

	function clearMessages() {
		messages.classList.remove("contest-vote-success", "contest-vote-error");
		messages.textContent = "";
	}

	function showSuccess(message) {
		messages.classList.remove("contest-vote-error");
		messages.classList.add("contest-vote-success");
		messages.textContent = message;
	}

	function showError(message) {
		messages.classList.remove("contest-vote-success");
		messages.classList.add("contest-vote-error");
		messages.textContent = message;
	}
});
