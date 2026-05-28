import { registerBlockType } from "@wordpress/blocks";
import { MediaUploadCheck, MediaUpload, RichText, useBlockProps } from "@wordpress/block-editor";
import { Button } from "@wordpress/components";

registerBlockType("custom/prize", {
	edit({ attributes, setAttributes }) {
		const { imageId, position, prize } = attributes;

		return (
			<article {...useBlockProps({ className: "editor-prize" })}>
				<MediaUploadCheck>
					<MediaUpload
						onSelect={(media) => {
							setAttributes({ imageId: media.id });
						}}
						allowedTypes={["image"]}
						value={imageId}
						render={({ open }) => (
							<Button onClick={open} variant="secondary">
								{imageId ? "Zmeniť obrázok" : "Vybrať obrázok"}
							</Button>
						)}
					/>
				</MediaUploadCheck>

				<RichText
					tagName="h2"
					placeholder="Zadajte miesto"
					value={position}
					onChange={(value) => {
						setAttributes({
							position: value,
						});
					}}
				/>

				<RichText
					tagName="p"
					placeholder="Zadajte výhru"
					value={prize}
					onChange={(value) => {
						setAttributes({
							prize: value,
						});
					}}
				/>
			</article>
		);
	},
	save() {
		return null;
	},
});
