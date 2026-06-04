import { RichTextToolbarButton } from "@wordpress/block-editor";
import { registerFormatType, toggleFormat } from "@wordpress/rich-text";

registerFormatType("custom/highlight", {
	title: "Highlight",
	tagName: "span",
	className: "highlight",

	edit({ isActive, value, onChange }) {
		return (
			<RichTextToolbarButton
				icon="marker"
				title="Highlight"
				onClick={() => {
					onChange(
						toggleFormat(value, {
							type: "custom/highlight",
						}),
					);
				}}
				isActive={isActive}
			/>
		);
	},
});
