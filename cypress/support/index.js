// ***********************************************************
// This example support/index.js is processed and
// loaded automatically before your test files.
//
// This is a great place to put global configuration and
// behavior that modifies Cypress.
//
// You can change the location of this file or turn off
// automatically serving support files with the
// 'supportFile' configuration option.
//
// You can read more here:
// https://on.cypress.io/configuration
// ***********************************************************

// Import the test utils.
import { registerWpTestCommands } from 'cypress-wp-test-utils';

// Register the test util commands.
registerWpTestCommands();

// Preserve the WordPress cookies so that we don't get caught by RSA.
Cypress.Cookies.defaults({
	preserve: /wp|wordpress/
});

/**
 * Try to visit the homepage.
 *
 * If we get redirected, then we can assume RSA has kicked in and we can login
 * to store the cookies for the rest of our tests.
 */
before(() => {
	cy.visit('/').then(window => {
		if( window.location.href !== Cypress.config().baseUrl ) {
			cy.loginUser();
		}
	});
})

// Import commands.js using ES2015 syntax:
import './commands'
