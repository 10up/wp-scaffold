/**
 * @module UIKitSection
 *
 * @description
 *
 * Collapsible UI kit sections
 *
 */
export default class UIKitSection {
	/**
	 * Initialize everything
	 *
	 * @param {Element[]} elements Section Heading elements.
	 */
	constructor(elements) {
		// Grab all the UI kit section headings
		this.sectionHeadings = elements;
	}

	/**
	 * Inits UI Kit Section
	 */
	init() {
		// Stop if there's no section heading
		if (!this.sectionHeadings) {
			console.error( 'Styleguide: No sections detected.' ); // eslint-disable-line
			return;
		}

		this.sectionHeadings.forEach((sectionHeading) => {
			this.setupCollapsible(sectionHeading);
		});
	}

	/**
	 * Create a button, add chevron SVG and inject in heading
	 * Hide section content, set ARIA attributes
	 *
	 * @param   {Element} sectionHeading The UI Kit section heading
	 *
	 */
	setupCollapsible(sectionHeading) {
		// Get section ID
		const sectionID = sectionHeading.parentNode.id;

		// Default state
		let expanded = true;

		// Check localStorage to see if we want to expand some sections by default
		if (localStorage) {
			// Override expanded state
			expanded = localStorage.getItem(`section-${sectionID}`) !== 'true' || false;
		}

		// Build the button, add the SVG chevron icons
		// eslint-disable-next-line no-param-reassign
		sectionHeading.innerHTML = `
			<button class="toggle" aria-expanded="${!expanded}" id="toggle-${sectionID}">
				<span>${sectionHeading.textContent}</span>
				<svg aria-hidden="true" focusable="false" class="uikit__chevron-up" width="12" height="7" xmlns="http://www.w3.org/2000/svg" viewBox="3.3 4.5 11.4 7" role="img"><polygon points="9,4.5 3.3,10.1 4.8,11.5 9,7.3 13.2,11.5 14.7,10.1 "></polygon></svg>
				<svg aria-hidden="true" focusable="false" class="uikit__chevron-down" width="12" height="7" xmlns="http://www.w3.org/2000/svg" viewBox="3.3 6.5 11.4 7" role="img"><polygon points="9,13.5 14.7,7.9 13.2,6.5 9,10.7 4.8,6.5 3.3,7.9 "></polygon></svg>
			</button>
		`;

		// Get the section content and hide it
		const wrapper = sectionHeading.parentNode.querySelector('.content');
		wrapper.hidden = expanded;
		wrapper.setAttribute('aria-hidden', expanded);
		wrapper.setAttribute('aria-labelledby', `toggle-${sectionID}`);

		// Assign click event to the button
		const button = sectionHeading.querySelector('button');
		button.onclick = (e) => this.toggleCollapsible(e, wrapper, button);
	}

	/**
	 * Toggles a section
	 *
	 * @param   {object} e        The click event
	 * @param   {Element} wrapper The UI Kit section content
	 * @param   {Element} button  The UI Kit section toggle button
	 *
	 */
	toggleCollapsible(e, wrapper, button) {
		// Expanded state as bool
		const expanded = button.getAttribute('aria-expanded') === 'true' || false;

		// Toggle expanded state and visibility
		button.setAttribute('aria-expanded', !expanded);
		// eslint-disable-next-line no-param-reassign
		wrapper.hidden = expanded;
		wrapper.setAttribute('aria-hidden', expanded);

		// Store expanded state in localStorage
		const sectionID = wrapper.parentNode.id;

		if (localStorage) {
			localStorage.setItem(`section-${sectionID}`, !expanded);
		}
	}
}
