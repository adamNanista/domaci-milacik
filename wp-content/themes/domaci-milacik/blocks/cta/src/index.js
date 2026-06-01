import { registerBlockType } from "@wordpress/blocks";
import { RichText, useBlockProps, InspectorControls } from "@wordpress/block-editor";
import { PanelBody, TextControl } from "@wordpress/components";

registerBlockType("custom/cta", {
	edit({ attributes, setAttributes }) {
		const { title, subtitle, primaryButtonText, primaryButtonUrl, secondaryButtonText, secondaryButtonUrl } = attributes;

		return (
			<section {...useBlockProps({ className: "editor-cta" })}>
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
				/>

				<div class="editor-flex editor-gap-4">
					<RichText
						tagName="span"
						className="editor-button editor-button-primary"
						placeholder="Zadajte text hlavného tlačidla"
						value={primaryButtonText}
						onChange={(value) => {
							setAttributes({
								primaryButtonText: value,
							});
						}}
					/>

					<RichText
						tagName="span"
						className="editor-button editor-button-outline"
						placeholder="Zadajte text vedľajšieho tlačidla"
						value={secondaryButtonText}
						onChange={(value) => {
							setAttributes({
								secondaryButtonText: value,
							});
						}}
					/>
				</div>

				<InspectorControls>
					<PanelBody title="Tlačidlá">
						<TextControl
							label="URL hlavného tlačidla"
							value={primaryButtonUrl}
							onChange={(value) => {
								setAttributes({
									primaryButtonUrl: value,
								});
							}}
						/>
						<TextControl
							label="URL vedľajšieho tlačidla"
							value={secondaryButtonUrl}
							onChange={(value) => {
								setAttributes({
									secondaryButtonUrl: value,
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
