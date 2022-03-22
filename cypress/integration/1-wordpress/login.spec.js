// ***********************************************************
// Login to WordPress
// ***********************************************************

// Import the test utils.
import { registerWpTestCommands } from 'cypress-wp-test-utils';

// Register the test util commands.
registerWpTestCommands();

describe( 'WordPress Login', () => {
	it( 'can login to wordpress', () => {
		cy.visit('/wp-login.php');
        cy.get('h1').should('have.text', 'Powered by WordPress');
		cy.loginUser();
		cy.get('.wrap > h1').should('have.text', 'Dashboard');
	});
});
