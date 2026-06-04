import { registerBlockType } from "@wordpress/blocks";
import { InnerBlocks, useBlockProps } from "@wordpress/block-editor";

registerBlockType("custom/cards", {
	edit() {
		return (
			<section {...useBlockProps({ className: "editor-cards" })}>
				<div class="editor-cards-list">
					<InnerBlocks allowedBlocks={["custom/card"]} template={[["custom/card"]]} orientation="horizontal" />
				</div>
			</section>
		);
	},
	save() {
		return <InnerBlocks.Content />;
	},
});
