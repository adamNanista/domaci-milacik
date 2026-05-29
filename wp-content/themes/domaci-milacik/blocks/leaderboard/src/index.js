import { registerBlockType } from "@wordpress/blocks";
import { RichText, useBlockProps, InspectorControls } from "@wordpress/block-editor";
import { PanelBody, TextControl } from "@wordpress/components";

registerBlockType("custom/leaderboard", {
	edit({ attributes, setAttributes }) {
		const { title, buttonText, buttonUrl } = attributes;

		return (
			<section {...useBlockProps({ className: "editor-leaderboard" })}>
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

				<div class="editor-grid editor-grid-cols-4 editor-gap-4">
					<div class="editor-card"></div>
					<div class="editor-card"></div>
					<div class="editor-card"></div>
					<div class="editor-card"></div>
				</div>

				<RichText
					tagName="span"
					className="editor-link"
					placeholder="Zadajte text tlačidla"
					value={buttonText}
					onChange={(value) => {
						setAttributes({
							buttonText: value,
						});
					}}
				/>

				<InspectorControls>
					<PanelBody title="Tlačidlo">
						<TextControl
							label="URL tlačidla"
							value={buttonUrl}
							onChange={(value) => {
								setAttributes({
									buttonUrl: value,
								});
							}}
						/>
					</PanelBody>
				</InspectorControls>
			</section>
		);
	},
	save() {
		return null;
	},
});
