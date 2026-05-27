import { registerBlockType } from "@wordpress/blocks";

registerBlockType("custom/contest-leaderboard", {
	edit() {
		return (
			<section>
				<strong>Contest Leaderboard</strong>
				<p>Top 10 entries by votes (ACF)</p>
			</section>
		);
	},
	save() {
		return null;
	},
});
