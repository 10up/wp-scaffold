// https://plopjs.com/documentation/

/* eslint-disable */
module.exports = (plop) => {
	plop.setGenerator('UI component', {
		description: 'UI Component with options for Partial, CSS, JS & Styleguide example',
		prompts: [
			{
				type: 'input',
				name: 'name',
				message: 'Component name?',
			},
			{
				type: 'list',
				name: 'typePath',
				message: 'Component type?',
				choices: [
					{
						name: 'No type',
						value: '',
					},
					{
						name: 'Atom',
						value: 'atoms/',
					},
					{
						name: 'Molecule',
						value: 'molecules/',
					},
					{
						name: 'Organism',
						value: 'organisms/',
					},
				],
				default: 'No type',
			},
			{
				type: 'confirm',
				name: 'withPartial',
				message: 'Add component partial?',
				default: true,
			},
			{
				type: 'confirm',
				name: 'withStyleguide',
				message: 'Add partial styleguide example?',
				default: true,
			},
			{
				type: 'confirm',
				name: 'withCSS',
				message: 'Add component CSS?',
				default: true,
			},
			{
				type: 'confirm',
				name: 'withJS',
				message: 'Add component JavaScript?',
				default: true,
			},
		],
		actions: (data) => {
			const actions = [];

			if (data.withPartial) {
				actions.push(
					{
						type: 'add',
						path: 'partials/{{typePath}}{{kebabCase name}}/{{kebabCase name}}.php',
						templateFile: 'plop-templates/ui-component-partial.hbs',
					},
				);
			}

			if (data.withStyleguide) {
				actions.push(
					{
						type: 'add',
						path: 'partials/{{typePath}}{{kebabCase name}}/styleguide/examples.php',
						templateFile: 'plop-templates/ui-component-partial-styleguide-index.hbs',
					},
					{
						type: 'add',
						path: 'partials/{{typePath}}{{kebabCase name}}/styleguide/example-default.php',
						templateFile: 'plop-templates/ui-component-partial-styleguide-example.hbs',
					},
				);
			}

			if (data.withCSS) {
				actions.push(
					{
						type: 'add',
						path: 'assets/css/frontend/components/{{typePath}}{{kebabCase name}}/index.css',
						templateFile: 'plop-templates/ui-component-css-index.hbs',
					},
					{
						type: 'add',
						path: 'assets/css/frontend/components/{{typePath}}{{kebabCase name}}/{{kebabCase name}}.css',
						templateFile: 'plop-templates/ui-component-css.hbs',
					},
				);
			}

			if (data.withJS) {
				actions.push(
					{
						type: 'add',
						path: 'assets/js/frontend/components/{{typePath}}{{kebabCase name}}/index.js',
						templateFile: 'plop-templates/ui-component-js-index.hbs',
					},
					{
						type: 'add',
						path: 'assets/js/frontend/components/{{typePath}}{{kebabCase name}}/{{kebabCase name}}.js',
						templateFile: 'plop-templates/ui-component-js.hbs',
					},
					{
						type: 'add',
						path: 'assets/js/frontend/components/{{typePath}}{{kebabCase name}}/find-{{kebabCase name}}.js',
						templateFile: 'plop-templates/ui-component-js-find.hbs',
					},
				);
			}

			return actions;
		},
	});
};
