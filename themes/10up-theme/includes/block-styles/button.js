// Import WP dependencies.
import { __ } from '@wordpress/i18n';
import domReady from '@wordpress/dom-ready';
import { registerBlockStyle } from '@wordpress/blocks';

const blockName = 'core/button';

const registerBlockStyles = () => {
	registerBlockStyle(blockName, {
		name: 'text',
		label: __('Text', '10up-theme'),
	});
};

/**
 * Init the Gutenberg block style registrations.
 */
domReady(() => {
	registerBlockStyles();
});
