if (document.querySelector('.site-navigation')) {
	import('./components/navigation')
		.then(({ default: Module }) => {
			Module();
		})
		.catch((error) => {
			console.error('An error occurred while importing the NavigationComponent', error); // eslint-disable-line
		});
}
