// ***********************************************************
// Create a Post
// ***********************************************************

// Import the test utils.
import { registerWpTestCommands } from 'cypress-wp-test-utils';

// Register the test util commands.
registerWpTestCommands();

describe( 'WordPress Publish Post', () => {
	it( 'can publish a post', () => {
        const title = 'new post';
        const content = 'this is the content';
        const excerpt = 'this is the excerpt';
        
        cy.createNewPost({
          title,
          content,
          excerpt,
        });
        cy.get('.editor-post-title__input').contains(title);
        cy.get('[data-type="core/freeform"]')
          .should('exist')
          .within(() => {
            cy.get('.wp-block-freeform').contains(content);
          });
        cy.openSidebarPanelWithTitle('Excerpt');
        cy.get('.editor-post-excerpt .components-textarea-control__input').contains(
          excerpt
        );
	});
});
