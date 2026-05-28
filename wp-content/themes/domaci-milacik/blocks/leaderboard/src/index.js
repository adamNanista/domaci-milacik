import { registerBlockType } from "@wordpress/blocks";
import { useBlockProps } from "@wordpress/block-editor";

registerBlockType("custom/leaderboard", {
	edit() {
		return (
			<section {...useBlockProps({ className: "editor-leaderboard" })}>
				<h1>Top miláčikovia</h1>
				<p>Top 10 miláčikov podľa počtu hlasov</p>
			</section>
		);
	},
	save() {
		return null;
	},
});
