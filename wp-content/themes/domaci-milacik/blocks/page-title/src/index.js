import { registerBlockType } from "@wordpress/blocks";
import { RichText, useBlockProps } from "@wordpress/block-editor";

registerBlockType("custom/page-title", {
	edit({ attributes, setAttributes }) {
		const { title, subtitle } = attributes;

		return (
			<section {...useBlockProps({ className: "editor-page-title" })}>
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

				<RichText
					tagName="p"
					placeholder="Zadajte podnadpis"
					value={subtitle}
					onChange={(value) => {
						setAttributes({
							subtitle: value,
						});
					}}
					allowedFormats={["core/bold", "custom/highlight"]}
				/>
			</section>
		);
	},

	save() {
		return null;
	},
});
