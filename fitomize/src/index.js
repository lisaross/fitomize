import _ from 'lodash';

function component() {
	const element = document.createElement('div');

	element.innerHTML = _.join(['Hello', 'babel'], ' ');

	return element;
}

console.log("hello, watch");

document.body.appendChild(component());
