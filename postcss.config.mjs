export default {
	plugins: {
		"@tailwindcss/postcss": {},
		"postcss-prefix-selector": {
			prefix: ".irfs-base",
			exclude: [".irfs-base"],
		},
	},
};
