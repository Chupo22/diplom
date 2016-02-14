var gulp = require('gulp');
var ts = require('gulp-typescript');
var typescript = require('typescript');
var merge = require('merge2');
var gulpConcat = require('gulp-concat');
var del = require('del');

var tsComponents = require('./ts-components.js');

var tsCompileComponent = function(component){
	var project = ts.createProject(component.path + '/tsconfig.json', {});
		var params = require('./' + component.path + '/tsconfig.json');
		return project
			.src()
			//.pipe(ts(params.compilerOptions))
			.pipe(ts(project))
			.js
			.pipe(gulp.dest(component.path + '/concat'));
};
var concat = function(component){
	var params = require('./' + component.path + '/tsconfig.json');
	//var files = [];
	//params.files.forEach(function(filePath, index){
	//	var jsFilePath = component.path + '/' + filePath
	//		.replace('.tsx', '.js')
	//		.replace('.ts', '.js');
	//	files.push(jsFilePath);
	//});
	
	
	//return gulp.src(files)
	return gulp.src(component.path + '/concat/*.js')
		.pipe(gulpConcat('script.js'))
		.pipe(gulp.dest(component.path + '/' + 'build'));
};

gulp.task('compile', function(){
	var result = [];
	tsComponents.forEach(function(component, index){
		result.push(tsCompileComponent(component));
	});
	return merge(result);
});

gulp.task('concat', ['compile'], function(){
	var result = [];
	tsComponents.forEach(function(component, index){
	    result.push(concat(component));
	});
	return merge(result);
});

gulp.task('del', ['concat'], function(){
	tsComponents.forEach(function(component, index){
	    del(component.path + '/concat');
	});
});

gulp.task('watch', function(){
	tsComponents.forEach(function(component, index){
	    gulp.watch(component.path + '/src/*.{ts,tsx}', ['build']);
	});
	//gulp.watch('local/templates/main/components/bitrix/system.auth.form/.default/src/*.tsx', ['build']);
});
gulp.task('build', ['del']);
gulp.task('default', ['build']);
