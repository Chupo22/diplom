///<reference path="include.d.ts"/>
import React = require('react');
import ReactDOM = require('react-dom');
import {State} from './state';
import {Description} from './description';
import {Query} from './query';

export interface ITable{
	name: string
	columns: string[]
}
export interface IExercise{
	name: string
	number: number
	query: string
	completed: boolean
	
	tasks: IExerciseTask[]
}

export interface IExerciseTask{
	table: string
	column: string
	condition: string
	value: string	
}

export interface IState {

}
export interface IProps {
	tables: ITable[]
	dbName: string
	exercises: IExercise[]
	
	exerciseNumber?: number
}

export class Test extends React.Component<IProps, IState> {
	state: IState = {
	};
	
	constructor(){
		super();
	}
	
	render(){
		return (
			<div>
				<Description {... this.props}/>
				<State {... this.props} />
				<Query {... this.props} />
			</div>
		);
	}
}

export function  bootstrap(params){
	ReactDOM.render(<Test {... params} />, document.getElementById('test-container'));
}
