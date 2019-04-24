const path = require('path');

// include the js minification plugin
const UglifyJSPlugin = require('uglifyjs-webpack-plugin');

module.exports = {
	entry: ['./js/src/app.js'],
	output: {
		filename: './js/build/app.min.js',
		path: path.resolve(__dirname)
	},
	module: {
		rules: [
			// perform js babelization on all .js files
			{
				test: /\.js$/,
				exclude: /node_modules/,
				use: {
					loader: "babel-loader",
					options: {
						presets: ['@babel/env']
					}
				}
			}
		]
	},
	optimization: {
		minimizer: [
			// enable the js minification plugin
			new UglifyJSPlugin({
				cache: true,
				parallel: true
			})
		]
	}
};
