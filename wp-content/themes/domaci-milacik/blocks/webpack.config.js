const defaultConfig = require("@wordpress/scripts/config/webpack.config");
const path = require("path");

const blocks = ["hero", "leaderboard", "prizes", "prize", "steps", "step", "donation", "cta", "page-title", "entry-form", "sms-leaderboard"];

module.exports = blocks.map((block) => ({
	...defaultConfig,

	entry: {
		index: path.resolve(__dirname, `${block}/src/index.js`),
	},

	output: {
		path: path.resolve(__dirname, `${block}/build`),
		filename: "index.js",
	},
}));
