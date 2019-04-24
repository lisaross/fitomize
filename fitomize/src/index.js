import _ from 'lodash'

function component() {
	const element = document.createElement('div');

	element.innerHTML = _.join(['Hello', 'webpack'], ' ');

	return element;
}

console.log("hello, watch");

document.body.appendChild(component());
