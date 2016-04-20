///<reference path="include.d.ts"/>
import React = require('react');
import ReactDOM = require('react-dom');
import {State} from './state';
import {Description} from './description';
import {Editor} from './editor';
import _ =  require('lodash');
import {IUserExercise as IEx, UserExercise} from '../../../../../../src/ts/user-exercise';
import helpers = require('../../../../../../src/ts/helpers');

export interface ITable{
	name: string
	columns: string[]
}
export interface IExercise{
	userExerciseId?: number
	name?: string
	number?: number
	query?: string
	completed?: boolean
	
	tasks?: IExerciseTask[]
}
export interface ITest{
	id?: number
	code?: string
	name?: string
}

export interface IExerciseTask{
	table: string
	column: string
	condition: string
	value: string	
}

export interface IState {
	selected?: IExercise 
	exercises?: IExercise[]
}
export interface IProps {
	tables: ITable[]
	dbName: string
	exercises: IExercise[]
	test: ITest
	
	exerciseNumber?: number
}

var prefix = 'test:';
export var events = {
	changeExercise: prefix+'change.exercise'
};

export class Test extends React.Component<IProps, IState> {
	state: IState = {};
	
	constructor(props: IProps){
		super();
		props.exercises.forEach((item: IExercise, index)=>item.query = helpers.base64_decode(item.query));
		this.saveQuery = helpers.delay(this.saveQuery, 200);
		this.state.selected = _.find(props.exercises, {number: props.exerciseNumber});
	}
	
	get selected(){
		return this.state.selected;
	}
	
	set selected(val){
		this.setState({selected: val});
	}
	
	get exercises(){
		return this.props.exercises;
	}
	
	onChangeExercise(e: any){
		this.selected = _.find(this.exercises, {number: parseInt(e.target.value)});
	}
	
	onChangeQuery(value){
		this.selected.query = value;
		this.saveQuery();
	}
	
	saveQuery(){
		var ex = this.selected;
		UserExercise.save({
			id: ex.userExerciseId,
			number: ex.number,
			query: ex.query
		});
	}
	
	render(){
		return (
			<div>
				<Description 
					tables={this.props.tables}
					dbName={this.props.dbName}
				/>
				
				<State 
					exercises={this.props.exercises}
					exerciseNumber={this.props.exerciseNumber}
					onChange={this.onChangeExercise.bind(this)}
				/>
				
				<div>
					Запрос:<br/>
					<Editor onChange={this.onChangeQuery.bind(this)} exercise={this.selected} />
					<input type="button" value="Выполнить" style={{width: 200}} className="form-control btn-default"/>
				</div>
			</div>
		);
	}
}

export function  bootstrap(params){
	ReactDOM.render(<Test {... params} />, document.getElementById('test-container'));
}
