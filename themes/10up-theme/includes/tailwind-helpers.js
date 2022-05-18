const themeJSON = require('../theme.json');

function getThemePalette() {
	const { palette } = themeJSON.settings.color;

	return Array.isArray(palette) ? palette : [];
}

function getThemeColors(includeBlocks = false) {
	const themePalette = getThemePalette();
	const blocks = themeJSON.settings.blocks ?? null;

	if (!themePalette) return [];

	const colors = {};

	/* Add a palette to the colors object */
	const addColors = (palette) => {
		palette.forEach((paletteColor) => {
			const { slug, color } = paletteColor;
			const colorExists = slug in colors;
			// Skip if color already exists
			if (!colorExists) {
				colors[slug] = color;
			}
		});
	};

	addColors(themePalette);

	/*
	 * If theme.json includes any block settings, check if their
	 * colors should be included
	 *
	 */
	if (blocks) {
		// Check if we only need to get some blocks or all of them
		if (Array.isArray(includeBlocks) && includeBlocks.length > 0) {
			for (const [name, block] of Object.entries(blocks)) {
				const { palette } = block.color;

				if (includeBlocks.includes(name)) {
					addColors(palette);
				}
			}
		} else if (includeBlocks === true) {
			for (const block of Object.values(blocks)) {
				const { palette } = block.color;

				addColors(palette);
			}
		}
	}

	return colors;
}

module.exports = { getThemeColors, getThemePalette };
