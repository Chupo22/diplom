'use strict';
var webpack = require('webpack');

var config = {
    entry: {
		bundle: "./local/templates/main/index.js",
		// testBundle: "./local/templates/main/test.js",
	},
    output: {
        path: __dirname + "/local/templates/main/",
        filename: "[name].js",
		library: 'components'
    },
	
	watchOptions:{
		aggregateTimeout: 100
	},
	
	resolve:{
		moduleDirectories: ['node_modules'],
		extensions: ['', '.js','.jsx', '.ts', '.tsx'],
		alias: {
			jquery: 'jquery2'
		}
	},
	
	resolveLoaders:{
		moduleDirectories:['node_modules'],
		moduleTemplates: ['*-loader', '*'],
		extensions: ['js']
	},
	
	module: {
		loaders: [
			{
				test: /\.tsx?$/,
				loader: 'webpack-typescript?target=ES5&jsx=react'
			}
		]
	},
	
	devtool: 'source-map',
	
	plugins:[
		//new webpack.optimize.UglifyJsPlugin({
		//	compress: {
		//		warnings: false,
		//		drop_console: false,
		//		unsafe: true
		//	}
		//})
		new webpack.ProvidePlugin({
			jquery: 'jquery2/jquery-2.0.3.js'
		})
	]
	
};

module.exports = config;
