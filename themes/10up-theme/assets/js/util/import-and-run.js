/**
 * Default callback when using `importAndRun`
 *
 * @param {Object} Module - Factory function or class
 */
const DEFAULT_CALLBACK = (Module) => {
	try {
		const module = new Module();
		module.init();
	} catch (error) {
		// Something failed
	}
};

export default (module, selectors = ['body'], cb = DEFAULT_CALLBACK) => {
	let shouldImport = false;

	if (Array.isArray(selectors) && selectors.length) {
		shouldImport = Boolean(
			selectors.find((selector) => {
				return Boolean(document.querySelector(selector));
			}),
		);
	}

	if (!shouldImport) {
		Promise.resolve();
	}

	/* eslint-disable-next-line consistent-return */
	return import(`./../${module}`).then(({ default: module }) => {
		if (cb && typeof cb === 'function') {
			cb(module);
		}
	});
};
