import importAndRun from '../util/import-and-run';

// List your components here.
const COMPONENTS = {
	'components/navigation': {
		selectors: ['.site-navigation'],
	},
};

const initComponents = () => {
	Object.entries(COMPONENTS).forEach(([key, value]) => {
		importAndRun(key, value.selectors, value.callback);
	});
};

const init = () => {
	window.requestAnimationFrame(() => {
		document.body.classList.add('js-loaded');
	});

	initComponents();
};

document.addEventListener('DOMContentLoaded', init);
