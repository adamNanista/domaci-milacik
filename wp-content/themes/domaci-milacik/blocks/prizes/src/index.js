import { registerBlockType } from "@wordpress/blocks";
import { RichText, InnerBlocks, useBlockProps } from "@wordpress/block-editor";

registerBlockType("custom/prizes", {
	edit({ attributes, setAttributes }) {
		const { title } = attributes;

		return (
			<section {...useBlockProps({ className: "editor-prizes" })}>
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

				<InnerBlocks allowedBlocks={["custom/prize"]} template={[["custom/prize"]]} orientation="vertical" />
			</section>
		);
	},
	save() {
		return <InnerBlocks.Content />;
	},
});
