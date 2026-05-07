document.addEventListener("DOMContentLoaded", function () {
	// Turnstile
	let turnstileWidgetId = null;
	let pendingVote = false;

	// DOM
	const voteCount = document.querySelector("#contest-vote-count");
	const voteButton = document.querySelector("#contest-vote-button");
	const messages = document.querySelector("#contest-vote-messages");
	const countdown = document.querySelector("#contest-vote-countdown");

	if (!voteButton) return;

	const postId = voteButton.dataset.postId;

	// Voted
	checkVoteStatus();

	// Voting
	voteButton.addEventListener("click", function (event) {
		event.preventDefault();
		clearMessages();
		handleVote();
	});

	// Turnstile
	function initTurnstile() {
		if (turnstileWidgetId !== null) return;

		turnstileWidgetId = turnstile.render("#contest-vote-turnstile", {
			sitekey: "0x4AAAAAADI3OolGYd09Ili5",
			size: "invisible",
			callback: onTurnstileSuccess,
			"error-callback": onTurnstileError,
			"expired-callback": () => {
				turnstile.reset(turnstileWidgetId);
			},
		});
	}

	function onTurnstileSuccess(turnstileToken) {
		if (!pendingVote) return;

		pendingVote = false;
		handleVote(turnstileToken);
	}

	function onTurnstileError() {
		pendingVote = false;
		setLoading(false);
		showError("Overenie zlyhalo, skúste to znova.");
	}

	async function handleVote(turnstileToken = "") {
		setLoading(true);

		if (!turnstileToken && turnstileWidgetId !== null) {
			turnstileToken = turnstile.getResponse(turnstileWidgetId);
		}

		const body = {
			action: "contest_vote",
			nonce: contest_entry_voting_ajax.nonce,
			post_id: postId,
		};

		if (turnstileToken) {
			body.turnstile_token = turnstileToken;
		}

		try {
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
				return;
			} else {
				if (result.data?.require_turnstile) {
					showError(result.data.message);
					pendingVote = true;

					if (turnstileWidgetId === null) {
						initTurnstile();
					} else {
						turnstile.reset(turnstileWidgetId);
					}

					return;
				}

				showError(result.data?.message || "Niečo sa pokazilo.");
			}
		} catch {
			setLoading(false);
			showError("Vyskytla sa chyba siete. Skontrolujte svoje pripojenie a skúste to znova.");
		}
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
					const nextVoteIn = parseInt(result.data.next_vote_in, 10);
					startCountdown(nextVoteIn, countdown);
				}
			}
		} catch {}
	}

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
		voteButton.querySelector("#contest-vote-button-text").classList.add("hidden");
		voteButton.querySelector("#contest-vote-button-loading").classList.add("hidden");
		voteButton.querySelector("#contest-vote-button-voted").classList.remove("hidden");
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

	function startCountdown(durationSeconds, element) {
		element.parentElement.classList.remove("hidden");

		const endTime = Date.now() + durationSeconds * 1000;

		function updateCountdown() {
			const now = Date.now();
			const remainingMs = Math.max(0, endTime - now);
			const remainingSeconds = Math.round(remainingMs / 1000);

			if (remainingSeconds <= 0) {
				clearInterval(interval);
				element.parentElement.classList.add("hidden");
				window.location.reload();
				return;
			}

			const minutes = Math.floor(remainingSeconds / 60);
			const seconds = remainingSeconds % 60;

			element.textContent = `Hlasovať môžete o ${minutes}:${String(seconds).padStart(2, "0")}`;
		}

		updateCountdown();

		const interval = setInterval(updateCountdown, 1000);
	}
});
