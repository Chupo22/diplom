///<reference path="../../../../../../../src/typings/tsd.d.ts"/>
import React = require('react');
import ReactDOM = require('react-dom');
import $ = require('jquery');
require('!style!css!autoprefixer?browsers=last 2 versions!csso!./style.css');

export interface IState {
	login?: string
	password?: string
	isShow?: boolean
	isShown?: boolean

}
export interface IProps {
	isAuthorized: boolean
	userName: string
	login: string
}

export class AuthForm extends React.Component<IProps, IState> {
	state: IState = {
		password: '',
		isShow: false,
		isShown: false
	};
	$selector: JQuery;
	
	constructor(props: IProps){
		super();
		this.onChangeLogin = this.onChangeLogin.bind(this);
		this.onChangePassword = this.onChangePassword.bind(this);
		this.onSubmit = this.onSubmit.bind(this);
		this.onCancel = this.onCancel.bind(this);
		this.onLogout = this.onLogout.bind(this);
	}
	
	componentDidMount(){
		this.setState({
			login: this.props.login
		});
		this.$selector = $('#auth-form-container');
	}
	
	show(){
		this.setState({
			isShow: true,
			isShown: true
		});
	}
	
	hide(){
		this.setState({isShow: false})
	}
	
	onChangeLogin(e: any){this.setState({login:e.target.value})}
	onChangePassword(e: any){this.setState({password:e.target.value})}
	onSubmit(e: any){
		if(!this.state.isShow)
			e.preventDefault();
		this.setState({
			isShow:true,
			isShown: true
		});
	}
	onCancel(e: any){
		e.preventDefault();
		this.hide()
	}
	onLogout(e: any){
		e.preventDefault();
		var $form = this.$selector.find('form');
		$form.submit();
	}
	
	render () {
		var result: any;
		if(!this.props.isAuthorized){
			var animationClass = this.state.isShow ? 'zoomInLeft' : 'zoomOutLeft';
			result =  (
				<div>
					<button type="submit" id="btn-login" className="btn btn-default fl_r">login</button>
					<div className={"form-group animated fl_l " + animationClass} style={{
						display: this.state.isShown ? 'block' : 'none'
					}}>
						<input name="login" type="hidden" value="yes"/>
						<input name="backurl" type="hidden" value={window.location.href}/>
						<input name="AUTH_FORM" type="hidden" value="Y"/>
						<input name="TYPE" type="hidden" value="AUTH"/>
						<input name="USER_LOGIN" onChange={this.onChangeLogin} className="form-control" type="text" value={this.state.login} placeholder="login"/>
						<input name="USER_PASSWORD" onChange={this.onChangePassword} className="form-control" type="password" autoComplete="off" placeholder="password"/>
						<a className="close" href="#" onClick={this.onCancel}>X</a>
						<br />
						<label><input name="USER_REMEMBER" className="form-control user-remember" type="checkbox" value="Y"/> Remember me</label>
					</div>
				</div>
			);
		}else{
			result =  (
				<a className="logout-label btn btn-default" href="#" onClick={function(e: any){e.preventDefault()}}>
					{this.props.userName}
					<span className="close" href="#" onClick={this.onLogout} style={{marginTop: '-3px'}}>x</span>
					<input type="hidden" name="logout" value="yes"/>
					<input type="submit" className="disp_n"/>
				</a>
			);
		} 
		
		return (
			<form id="auth-form" className="navbar-form navbar-left" action="" method="post" onSubmit={this.onSubmit}>
				{result}
			</form>
		)
	}
}

export function bootstrap(params){
	ReactDOM.render(<AuthForm {... params}/>, document.getElementById('auth-form-container'));
}
