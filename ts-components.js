var mainPath = 'local/templates/main';
var path = [
	mainPath + '/components/bitrix/system.auth.form/.default'
];
var result = [];

path.forEach(function(componentPath, index){
    result.push({
		path: componentPath,
		build: componentPath + '/build',
		fileName: 'script.js'
	});
});

module.exports = result;
