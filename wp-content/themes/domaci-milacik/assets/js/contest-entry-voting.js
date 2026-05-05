document.addEventListener("DOMContentLoaded", function () {
	// Turnstile
	let turnstileWidgetId = null;
	let pendingVote = false;

	// DOM
	const voteCount = document.querySelector("#contest-vote-count");
	const voteButton = document.querySelector("#contest-vote-button");
	const messages = document.querySelector("#contest-vote-messages");

	const postId = voteButton.dataset.postId;

	// Voted
	checkVoteStatus();

	// Voting
	voteButton.addEventListener("click", function (event) {
		event.preventDefault();
		clearMessages();
		submitVote();
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
		const voted = voteButton.querySelector("#contest-vote-button-voted");
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

	function initTurnstile() {
		if (turnstileWidgetId !== null) return;

		turnstileWidgetId = turnstile.render("#contest-entry-vote-turnstile", {
			sitekey: "0x4AAAAAADI3OolGYd09Ili5",
			callback: onTurnstileSuccess,
			"error-callback": onTurnstileError,
		});
	}

	function onTurnstileSuccess(token) {
		if (!pendingVote) return;

		pendingVote = false;
		handleVote(token);
	}

	function onTurnstileError() {
		pendingVote = false;
		showError("Overenie zlyhalo, skúste to znova.");
	}

	async function handleVote(turnstileToken = "") {
		setLoading(true);

		const body = {
			action: "contest_vote",
			nonce: contest_entry_voting_ajax.nonce,
			post_id: postId,
		};

		if (turnstileToken) {
			body.turnstile_token = turnstileToken;
		}

		const response = await fetch(contest_entry_voting_ajax.ajax_url, {
			method: "POST",
			headers: { "Content-Type": "application/x-www-form-urlencoded" },
			body: new URLSearchParams(body),
		});

		const result = await response.json();
		setLoading(false);

		if (result.success) {
			voteCount.textContent = result.data.votes;
			setVoted();
			showSuccess(result.data.message);

			if (turnstileWidgetId !== null) {
				turnstile.reset(turnstileWidgetId);
			}

			return;
		}

		if (result.data?.require_turnstile) {
			showError(result.data.message || "Overujem, že nie ste robot...");
			pendingVote = true;
			turnstile.execute(turnstileWidgetId);
			return;
		}

		showError(result.data?.message || "Niečo sa pokazilo.");
	}

	async function checkVoteStatus() {
		try {
			const response = await fetch(contest_entry_voting_ajax.ajax_url, {
				method: "POST",
				headers: { "Content-Type": "application/x-www-form-urlencoded" },
				body: new URLSearchParams({
					action: "contest_vote_status",
					nonce: contest_entry_voting_ajax.nonce,
					post_id: postId,
				}),
			});

			const result = await response.json();

			if (result.success && result.data.can_vote === false) {
				setVoted();
				if (result.data.next_vote_in) {
					const nextVoteIn = Math.floor(result.data.next_vote_in / 60);
					showError(`Hlasovať môžete o ${nextVoteIn} minút.`);
				}
			}
		} catch (error) {
			console.warn("Vyskytla sa chyba siete. Skontrolujte svoje pripojenie a skúste to znova.", error);
		}
	}

	initTurnstile();
});
