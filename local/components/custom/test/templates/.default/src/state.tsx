///<reference path="include.d.ts"/>

import * as React from 'react';
import {IExercise} from "./test";
// import * as ReactDOM from 'react-dom';
// import {DropdownButton, MenuItem} from 'react-bootstrap';
export interface IState{
	
}

export interface IProps{
	onChange?: React.EventHandler<React.FormEvent>
	exercises?: IExercise[]
	exerciseNumber?: number
	exercise?: IExercise
}

export class State extends React.Component<IProps, IState> {

	constructor() {
		super();
	}
	
	componentWillReceiveProps(newProps: IProps){
		this.props = newProps;
		this.setState({});
	}
	
	render() {
		var exerciseCompleted = this.props && this.props.exercise && this.props.exercise.completed ? 
			<div className="alert alert-success" role="alert"><span className="glyphicon glyphicon-ok-circle"/> completed</div> : null;
		return (
			<div>
				<label>
					<select 
						onChange={this.props.onChange}
						defaultValue={this.props.exerciseNumber.toString()}
						className="form-control"
						style={{maxWidth:300,float: 'right'}}
					>
						{
							this.props.exercises.map((item)=>{
								return <option key={item.number} value={item.number.toString()}>{item.name}</option>;
							})
						}
					</select>
				</label>
				{exerciseCompleted}
				<br/>
			</div>
		);
	}
}
