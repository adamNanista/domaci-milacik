import { registerBlockType } from "@wordpress/blocks";
import { RichText, InnerBlocks, useBlockProps } from "@wordpress/block-editor";

registerBlockType("custom/steps", {
	edit({ attributes, setAttributes }) {
		const { title } = attributes;

		return (
			<section {...useBlockProps({ className: "editor-steps" })}>
				<RichText
					tagName="h1"
					placeholder="Zadajte nadpis"
					value={title}
					onChange={(value) => {
						setAttributes({
							title: value,
						});
					}}
				/>

				<div class="editor-steps-list">
					<InnerBlocks allowedBlocks={["custom/step"]} template={[["custom/step"]]} orientation="horizontal" />
				</div>
			</section>
		);
	},
	save() {
		return <InnerBlocks.Content />;
	},
});
