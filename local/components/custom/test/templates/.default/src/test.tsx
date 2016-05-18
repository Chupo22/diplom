///<reference path="include.d.ts"/>
import React = require('react');
import ReactDOM = require('react-dom');
import {Description} from './description';
import {Editor} from './editor';
import _ =  require('lodash');
import {UserExercise} from 'user-exercise';
import helpers = require('helpers');
import $ = require('jquery');
import {ICompletion} from "sql-editor";

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
	id?: number
	type?: string
	table?: string
	column?: string
	condition?: string
	value?: string	
}
export interface IState {
	selected?: IExercise 
	exercises?: IExercise[]
	errors?: string[]
	result?: {
		isSuccess?:boolean
		items?:any[]
		errorItems?:any[]
	}
	
	isExecuting?: boolean
}
export interface IProps {
	tables: ITable[]
	dbName: string
	exercises: IExercise[]
	test: ITest
	completion: ICompletion[]
	
	exerciseNumber?: number
}

var prefix = 'test:';
export var events = {
	changeExercise: prefix+'change.exercise',
	onAfterExecQuery: prefix+'onAfterExecQuery'
};

export class Test extends React.Component<IProps, IState> {
	state: IState = {};
	
	constructor(props: IProps){
		super();
		// props.exercises.forEach((item: IExercise, index)=>{item.query = helpers.base64_decode(item.query))};
		this.saveQuery = helpers.delay(this.saveQuery, 200);
		this.state.selected = _.find(props.exercises, {number: props.exerciseNumber});
	}
	
	set isExecutingQuery(val: boolean){
		this.setState({isExecuting: val});
	}
	get isExecutingQuery(){
		return this.state && this.state.isExecuting ? true : false;
	}
	
	set errors(val: string[]){
		this.setState({errors: val});
	}
	
	get errors(){
		if(this.state && this.state.errors && this.state.errors.length)
			return this.state.errors;
		else
			return [];
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
		this.result = undefined;
		this.errors = undefined;
		this.selected = _.find(this.exercises, {number: parseInt(e.target.value)});
	}
	
	onChangeQuery(value){
		this.selected.query = value;
		this.saveQuery();
	}
	
	get result(){
		return this.state && this.state.result ? this.state.result : undefined;
	}
	
	set result(val){
		this.setState({result: val});
	}
	
	execQuery(){
		this.isExecutingQuery = true;
		//noinspection TypeScriptValidateTypes
		$.ajax({
			url: UserExercise.ajaxUrl,
			dataType: 'json',
			method: 'POST',
			cache: false,
			data:{
				action: UserExercise.ajaxActions.execQuery,
				query: this.selected.query,
				userExerciseId: this.selected.userExerciseId,
			},
			success:(result)=>{
				$(window).trigger(events.onAfterExecQuery, [result]);
				if(result.success)
					this.selected.completed = true;
				console.log(result);
				this.setState({
					result: result,
					errors: result.errors,
					isExecuting: false,
				});
			}
			
		});
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
			<div style={{paddingBottom: 10}}>
				<Description 
					{... this.props}
					onChangeExercise={this.onChangeExercise.bind(this)}
					exercise={this.selected}
				/>
				
				<div>
					<h2>Запрос:</h2>
					<Editor
						onChange={this.onChangeQuery.bind(this)}
						exercise={this.selected}
						completion={this.props.completion}
					/>
					<button 
						style={{width: 150, marginTop: 10}}
						className="btn btn-default"
						
						onClick={this.execQuery.bind(this)}
					>
						{(()=>{
							if(this.isExecutingQuery)
								return <span className="glyphicon glyphicon-refresh glyphicon-refresh-animate" />
						})()}
						&nbsp;Выполнить
					</button>
				</div>
				{this.renderErrors()}
				{/*this.renderResult()*/}
				<ItemsTable 
					className="bs-callout bs-callout-mediumpurple"
					tittle="Query result"
					items={this.result ? this.result.items : undefined}
				/>
				<ItemsTable 
					className="bs-callout bs-callout-danger"
					tittle="Bad items"
					items={this.result ? this.result.errorItems : undefined}
				/>
			</div>
		);
	}
	
	renderErrors(){
		if(this.errors.length)
			return (
				<div className="bs-callout bs-callout-danger">
					<h4>Ошибка!</h4>
					{this.errors.map((error, index)=><p key={index}>{error}</p>)}
					
					
				</div>
			)
	}
}

interface IItemsTableProps{
	items: any[]
	className: string
	tittle: string
}
class ItemsTable extends React.Component<IItemsTableProps,any>{
	componentWillReceiveProps(newProps: IItemsTableProps){
		this.props = newProps;
		this.setState({});
	}
	
	render(){
		if(this.props && this.props.items && this.props.items.length){
			var cols = Object.keys(this.props.items[0]);
			return (
				<div className={this.props.className}>
					<h4>{this.props.tittle}</h4>
					<table className="table table-hover table-bordered table-striped">
						<thead>
							<tr>
								{cols.map((colName, index) => <td key={index}>{colName}</td>)}
							</tr>
						</thead>
						<tbody>
							{this.props.items.map((row, idnex)=>
								idnex > 6 ? false : <tr key={idnex}>
									{(()=>{
										var values = [];
										_.each(row, (value)=>values.push(value));
										return values.map((value, index) =><td key={index}>{value}</td>);
									})()}
								</tr>)}
						</tbody>
					</table>
				</div>
			);
		}else return <div style={{display: 'none'}}></div>;
	}
}

export function  bootstrap(params){
	ReactDOM.render(<Test {... params} />, document.getElementById('test-container'));
}
