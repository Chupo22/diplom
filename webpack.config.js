'use strict';
var webpack = require('webpack');

var config = {
    entry: {
		bundle: "./local/templates/main/index.js",
		// testBundle: "./local/templates/main/test.jsx",
		// testBundle: "./local/templates/main/test.js",
		// testBundle: "./local/templates/main/test.tsx",
	},
    output: {
        path: __dirname + '/local/templates/main/build/',
		publicPath: '/local/templates/main/build/',
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
	
	// resolveLoaders:{
	// 	moduleDirectories:['node_modules'],
	// 	moduleTemplates: ['*-loader', '*'],
	// 	extensions: ['js']
	// },
	
	module: {
		loaders: [
			{
				test: /\.tsx?$/,
				loader: 'webpack-typescript?target=ES5&jsx=react'
			},
			{
				test: /\.jsx?$/,
				exclude: /(node_modules|bower_components)/,
				loader: 'babel-loader',
				query: {
				  presets: ['react', 'es2015']
				}
			}
		]
	},
	
	devtool: 'source-map',
	
	plugins:[
		// new webpack.optimize.UglifyJsPlugin({
		// 	compress: {
		// 		warnings: false,
		// 		drop_console: false,
		// 		unsafe: true
		// 	}
		// }),
		new webpack.ProvidePlugin({
			jquery: 'jquery2/jquery-2.0.3.js'
		})
	]
	
};

module.exports = config;
