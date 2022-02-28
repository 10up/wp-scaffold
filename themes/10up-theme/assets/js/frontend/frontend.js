import importAndRun from '../util/import-and-run';

// Outline components and the matching selector.
const COMPONENTS = {
	'components/navigation': {
		selectors: ['.site-navigation'],
	},
};

/**
 * Initialize components
 *
 * Run through the components above and import them.
 *
 * 'frontend' in this case tells importAndRun to look in the frontend folder.
 * Possible values could be:
 * - admin
 * - shared
 * - styleguide
 *
 * @return {void}
 */
const initComponents = () => {
	Object.entries(COMPONENTS).forEach(([key, value]) => {
		importAndRun(key, 'frontend', value.selectors, value.callback);
	});
};

/**
 * Initialize frontend
 *
 * Add js-loaded class to body, then initialize components.
 *
 * @return {void}
 */
const init = () => {
	window.requestAnimationFrame(() => {
		document.body.classList.add('js-loaded');
	});

	initComponents();
};

// Run init when DOM is ready.
document.addEventListener('DOMContentLoaded', init);
