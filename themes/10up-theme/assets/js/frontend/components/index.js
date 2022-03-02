import importAndRun from '../../util/import-and-run';

// Outline components and the matching selectors.
// Each key is the name of a component, corresponding to its filename
// in the `frontend/components` directory. The value is an object
// with the following properties:
// - `selectors`: An array of selectors that the component should
//   be initialized on. If any of these selectors exist on the page,
//   the component will be initialized.
// - `callback`: A callback function that will be called when the
//   component is initialized.
const COMPONENTS = {
	navigation: {
		selectors: ['.site-navigation', 'body'],
	},

	// Add your own components here.
};

/**
 * Default callback when using `importAndRun`.
 *
 * This assumes the module is a class with an `init` method.
 * Feel free to modify this to suit your needs. For example,
 * if your site uses functional components, you can change
 * the `try` block to simply call the module: `Module()`.
 *
 * @param {Object} Module - Factory function or class.
 */
const DEFAULT_CALLBACK = (Module) => {
	try {
		const module = new Module();
		module.init();
	} catch (error) {
		// Something failed
	}
};

/**
 * Initialize frontend components
 *
 * Run through the components map above and import them.
 */
export const initComponents = () => {
	Object.entries(COMPONENTS).forEach(([component, args]) => {
		importAndRun(
			`frontend/components/${component}`,
			args?.selectors || [],
			args?.callback || DEFAULT_CALLBACK,
		);
	});
};
