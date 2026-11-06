import domReady from '@wordpress/dom-ready';
import { unregisterBlockVariation } from '@wordpress/blocks';

domReady(() => {
	unregisterBlockVariation('core/heading', 'stretchy-heading');
	unregisterBlockVariation('core/paragraph', 'stretchy-paragraph');
});
