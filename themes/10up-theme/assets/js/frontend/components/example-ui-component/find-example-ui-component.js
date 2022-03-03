// Reference to the DOM element to ensure we don't search multiple times
let $elementRef = null;

// DRY re-usable component naming e.g. to create new DOM elements with BEM naming
export const EXAMPLE_UI_COMPONENT_CLASSNAME = 'example-ui-component';

/**
 * FindExampleUiComponent
 *
 * Used to identify or determine presence of the element in the DOM
 * - e.g. DRY selectors for dynamic imports
 * - e.g. DRY selectors for initialisation checks
 *
 * @return {HTMLElement} the DOM element we are looking for
 */
const FindExampleUiComponent = () => {
	$elementRef = $elementRef ?? document.querySelector(`.example-ui-component`);

	return $elementRef;
};

export default FindExampleUiComponent;
