'use strict';
var webpack = require('webpack');
//noinspection CodeAssistanceForCoreModules
var path = require('path');
var AssetsPlugin = require('assets-webpack-plugin');
var CleanWebpackPlugin = require('clean-webpack-plugin');
var extractTextPlugin = require('extract-text-webpack-plugin');

var config = {
    output: {
        filename: "[name]_[chunkhash].js",
		library: 'components'
    },
	watchOptions:{
		aggregateTimeout: 100
	},
	resolve:{
		moduleDirectories: ['node_modules'],
		fallback: [path.resolve('./local/src/ts')],
		extensions: ['', '.js','.jsx', '.ts', '.tsx', '.css'],
		alias: {
			jquery: 'jquery2'
			// helpers: path.resolve('./local/src/ts/helpers'),
			// exercise: path.resolve('./local/src/ts/exercise'),
			// "user-exercise": path.resolve('./local/src/ts/user-exercise'),
		}
	},
	resolveLoaders:{
		moduleDirectories:['node_modules'],
		moduleTemplates: ['*-loader', '*'],
		extensions: ['', '.js', '.css']
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
			{
				test: /\.css$/i,
				loader: extractTextPlugin.extract('css!autoprefixer?browsers=last 2 versions!csso')
				// loader: 'style!css!autoprefixer?browsers=last 2 versions!csso'
			},
			{
				test: /\.(woff2|woff|eot|ttf|otf)$/,
				loader: 'url?name=/fonts/static-cache.[name].[hash].[ext]&limit=50000'
			},
			{
				test: /\.(gif|jpe?g|png|svg)$/,
				loader: 'url?name=/images/static-cache.[name].[hash].[ext]&limit=50000!img?minimize&optimizationLevel=5&progressive=true'
			}
		]
	},
	devtool: false,
	plugins:[
		new webpack.ProvidePlugin({
			jquery: 'jquery2/jquery-2.0.3.js'
		}),
		new extractTextPlugin('[name]_[chunkhash].css', {allChunks: true})
	]
	
};

var env = 'dev';
var build = 'public';
process.argv.forEach(function(arg){
	if(arg.indexOf('--build' === 0))
		build = arg.substring('--build-'.length);
	if(arg.indexOf('--env' === 0))
		env = arg.substring('--env-'.length);
});

switch(build){
	case 'admin':
		config.entry = {admin: './local/modules/automated_testing_system/index.js'};
		config.output.path = __dirname + '/local/modules/automated_testing_system/build/';
		config.output.publicPath = '/local/modules/automated_testing_system/build/';
		break;
	default:
		config.entry = {main: './local/templates/main/index.js'};
		config.output.path = __dirname + '/build/';
		config.output.publicPath = '/build/';
		break;
}

if(env == 'prod')
	config.plugins.push(
		new webpack.optimize.UglifyJsPlugin({
			compress: {
				warnings: false,
				drop_console: false,
				unsafe: true
			}
		})
	);
config.plugins.push(
	new AssetsPlugin({
		filename: 'assets.json',
		path: config.output.path
	}),
	new CleanWebpackPlugin([config.output.path], {
		root: __dirname,
		verbose: false,
		exclude: []
	})
);

module.exports = config;
