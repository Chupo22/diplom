///<reference path="../../../../../../../src/typings/tsd.d.ts"/>
///<reference path="AuthForm.tsx"/>

module components.bitrix.authForm{
	declare var params: any;
	import React = __React;
	import ReactDOM = __React.__DOM;
	import AuthForm = components.bitrix.authForm.AuthForm;
	
	ReactDOM.render(React.createElement(AuthForm, params.authForm), $('#auth-form-container')[0]);
}
