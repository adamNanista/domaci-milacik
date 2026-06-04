import { registerBlockType } from "@wordpress/blocks";
import { MediaUploadCheck, MediaUpload, RichText, useBlockProps } from "@wordpress/block-editor";
import { Button } from "@wordpress/components";
import { useSelect } from "@wordpress/data";

registerBlockType("custom/prize", {
	edit({ attributes, setAttributes }) {
		const { iconId, title, subtitle, imageId } = attributes;

		const iconUrl = useSelect(
			(select) => {
				if (!iconId) return null;
				const media = select("core").getMedia(iconId);
				return media?.source_url ?? null;
			},
			[iconId],
		);

		const imageUrl = useSelect(
			(select) => {
				if (!imageId) return null;
				const media = select("core").getMedia(imageId);
				return media?.source_url ?? null;
			},
			[imageId],
		);

		return (
			<article {...useBlockProps({ className: "editor-prize" })}>
				<MediaUploadCheck>
					{imageUrl && (
						<div style={{ marginBottom: "8px" }}>
							<img src={imageUrl} alt="" style={{ display: "block", width: "100%", height: "auto" }} />
						</div>
					)}
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

				<MediaUploadCheck>
					{iconUrl && (
						<div style={{ marginBottom: "8px" }}>
							<img src={iconUrl} alt="" style={{ display: "block", width: "100%", height: "auto", maxWidth: "6rem" }} />
						</div>
					)}
					<MediaUpload
						onSelect={(media) => {
							setAttributes({ iconId: media.id });
						}}
						allowedTypes={["image"]}
						value={iconId}
						render={({ open }) => (
							<Button onClick={open} variant="secondary">
								{iconId ? "Zmeniť obrázok" : "Vybrať obrázok"}
							</Button>
						)}
					/>
				</MediaUploadCheck>

				<RichText
					tagName="h2"
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
			</article>
		);
	},
	save() {
		return null;
	},
});
