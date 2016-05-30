'use strict';

var config = require('./base.config.js');

var build = 'public';
process.argv.forEach(function(arg){
	if(arg.indexOf('-build' === 0))
		build = arg.substring('-build-'.length);
});

switch(build){
	case 'admin':
		config.entry.bundle = "./local/modules/automated_testing_system/index.js";
		config.output.path = __dirname + "/local/modules/automated_testing_system/";
		config.output.publicPath = "/local/modules/automated_testing_system/";
		break;
	default:
		config.entry.bundle = "./local/templates/main/index.js";
		config.output.path = __dirname + "/local/templates/main/build";
		config.output.publicPath = "/local/templates/main/build";
		break;
}

module.exports = config;
