///<reference path="../../../../../../src/typings/include.d.ts"/>
import * as React from 'react';
import {IExercise} from "./test";
import {addCompleterWords, SqlEditor, ICompletion} from 'sql-editor';
import $ = require('jquery');
import Ace = AceAjax.Ace;

export interface IState {
	exercise?: IExercise
	lines?: number
}
export interface IProps {
	exercise: IExercise
	onChange: (value: string) => void
	completion: ICompletion[]
}

export class Editor extends React.Component<IProps, IState> {
	minLines = 5;
	state:IState = {};
	editor: any;
	
	constructor(props: IProps){
		super();
		this.lines = props.exercise.query.split('\n').length;
		addCompleterWords(props.completion);
		
		/*// $(window).keydown(function (e) {
		// 	if (e.ctrlKey && e.keyCode == 13
				&& this.editor
			// ) {
				Ctrl-Enter pressed
				// console.log(this.editor.isFocused());
			 // }
		// });*/
	}
	
	componentWillReceiveProps(newProps: IProps){
		this.props = newProps;
		this.setState({});
	}
	
	set query(value: string){
		this.props.exercise.query = value;
		this.setState({});
	}
	
	get query(){
		return this.props.exercise.query
	}
	
	get lines(){
		return this.state.lines || this.minLines;
	}
	
	set lines(val){
		this.state.lines = val > this.minLines ? val : this.minLines;
	}
	
	setState(state: IState, callback: () => any = undefined){
		this.lines = this.props.exercise.query.split('\n').length;
		super.setState(state,callback);
	}
	
	onChange(val){
		this.query = val;
		this.props.onChange(val);
	}
	
	render() {
		return <SqlEditor 
			mode="mysql"
			theme="chrome"
			name="test-sql-editor"
			width="800"
			value={this.query}
			maxLines={this.lines}
			minLines={this.lines}
			onChange={this.onChange.bind(this)}
			enableBasicAutocompletion={true}
			enableLiveAutocompletion={true}
		/>;
	}
}
