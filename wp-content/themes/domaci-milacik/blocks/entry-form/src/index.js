import { registerBlockType } from "@wordpress/blocks";
import { useBlockProps } from "@wordpress/block-editor";

registerBlockType("custom/entry-form", {
	edit() {
		return (
			<section {...useBlockProps({ className: "editor-entry-form" })}>
				<h1>Prihlasovací formulár</h1>
			</section>
		);
	},
	save() {
		return null;
	},
});
