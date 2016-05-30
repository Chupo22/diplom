'use strict';
var webpack = require('webpack');
//noinspection CodeAssistanceForCoreModules
var path = require('path');

var config = {
    entry: {
		// bundle: "./local/templates/main/index.js",
	},
    output: {
        // path: __dirname + '/local/templates/main/build/',
		// publicPath: '/local/templates/main/build/',
        filename: "[name].js",
		library: 'components'
    },
	
	watchOptions:{
		aggregateTimeout: 100
	},
	
	resolve:{
		moduleDirectories: ['node_modules'],
		fallback: [path.resolve('./local/src/ts')],
		extensions: ['', '.js','.jsx', '.ts', '.tsx'],
		alias: {
			jquery: 'jquery2',
			
			// helpers: path.resolve('./local/src/ts/helpers'),
			// exercise: path.resolve('./local/src/ts/exercise'),
			// "user-exercise": path.resolve('./local/src/ts/user-exercise'),
		}
	},
	
	resolveLoaders:{
		moduleDirectories:['node_modules'],
		moduleTemplates: ['*-loader', '*'],
		extensions: ['', '.js']
	},
	
	module: {
		loaders: [
			// {
			// 	test: /\.tsx?$/,
			// 	loader: 'webpack-typescript?target=ES5&jsx=react',
			// },
			
			{
				test: /\.tsx?$/,
				loader: 'ts-loader'
			},
			
			// {
			// 	test: /\.jsx?$/,
			// 	exclude: /(node_modules|bower_components)/,
			// 	loader: 'babel-loader',
			// 	query: {
			// 	  presets: ['react', 'es2015']
			// 	}
			// }
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
			jquery: 'jquery2/jquery-2.0.3.js',
		})
	]
	
};

module.exports = config;
