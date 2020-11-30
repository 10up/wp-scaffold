import PropTypes from 'prop-types';

export const propsShape = {
	attributes: PropTypes.shape({
		customTitle: PropTypes.string,
	}).isRequired,
	className: PropTypes.string,
};

export const editPropsShape = {
	...propsShape,
	clientId: PropTypes.string,
	isSelected: PropTypes.bool,
	setAttributes: PropTypes.func.isRequired,
};
