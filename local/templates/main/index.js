exports.initTest = function(params){
	// require.ensure([], ()=>{
		require('../../components/custom/test/templates/.default/src/test.tsx').bootstrap(params);
	// });
};

exports.initAuthForm = function(params){
	require('./components/bitrix/system.auth.form/.default/src/AuthForm.tsx').bootstrap(params);
};
