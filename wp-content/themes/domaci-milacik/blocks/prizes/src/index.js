import { registerBlockType } from "@wordpress/blocks";
import { InnerBlocks, useBlockProps } from "@wordpress/block-editor";

registerBlockType("custom/prizes", {
	edit() {
		return (
			<section {...useBlockProps({ className: "editor-prizes" })}>
				<h1>O čo hráme</h1>
				<InnerBlocks allowedBlocks={["custom/prize"]} template={[["custom/prize"]]} orientation="vertical" />
			</section>
		);
	},
	save() {
		return <InnerBlocks.Content />;
	},
});
