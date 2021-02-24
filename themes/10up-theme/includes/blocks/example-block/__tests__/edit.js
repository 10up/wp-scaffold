import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import Edit from '../edit';

test('it works', () => {
	const setAttributes = jest.fn();
	const { container } = render(
		<Edit setAttributes={setAttributes} attributes={{ currentTitle: 'test' }} />,
	);

	expect(container).toMatchInlineSnapshot(`
		<div>
		  <div>
		    <textarea />
		  </div>
		</div>
	`);

	userEvent.type(screen.getByRole('textbox'), 'my value');
	expect(setAttributes).toHaveBeenCalled();
});
