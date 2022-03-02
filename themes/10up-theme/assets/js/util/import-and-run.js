/**
 * Check if element exists in DOM
 *
 * @param {string} el HTML Element Selector
 *
 * @return {boolean} Whether element exists in DOM
 */
const elementExists = (el) => {
	return document.querySelector(el) !== null;
};

/**
 * Dynamically import a module if it appears on the page.
 *
 * @param {string}   module    Module to import
 * @param {Array}    selectors Selectors associated with the module
 * @param {Function} cb        Module callback
 *
 * @return {Promise} Promise that resolves when the module is loaded
 */
const importAndRun = (module, selectors, cb) => {
	let shouldImport = true;

	// If the element exists on the page, set shouldImport to true
	if (Array.isArray(selectors) && selectors.length) {
		shouldImport = selectors.some(elementExists);
	}

	// Element doesn't exist? Get out of here.
	if (!shouldImport) {
		return Promise.resolve();
	}

	// Import the module and run any associated callbacks
	return import(`./../${module}`)
		.then(({ default: module }) => {
			if (cb && typeof cb === 'function') {
				cb(module);
			}
		})
		.catch((error) => {
			// Catch errors
			console.error(`Error with module '${module}':`, error); // eslint-disable-line no-console
		});
};

export default importAndRun;
