import Edit from './edit';

import {render,act,fireEvent} from '@testing-library/react';
describe( 'Basic block', () => {
    test( 'Edit component matches snapshot ', () => {
        const setAttributes = jest.fn();
        const {container} = render(<Edit setAttributes={setAttributes} className={'one two'} attributes={{customTitle: 'Title'}} />)
        expect(container).toMatchSnapshot();
    });

    test( 'Edit component calls setAttibutes ', () => {
        const setAttributes = jest.fn();
        const customTitle = 'Title';
        const {getByPlaceholderText} = render(<Edit setAttributes={setAttributes} className={'one two'} attributes={{customTitle}} />)
        act( () => {
            fireEvent.change( getByPlaceholderText(customTitle), {target: {value: 'Hi Roy'}});
        });
        expect(setAttributes).toBeCalledTimes(1);
        expect(setAttributes).toBeCalledWith('Hi Roy');
    });
}) 