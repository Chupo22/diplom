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
								var completed = item.completed ? '✔' : null;
								return <option key={item.number} value={item.number.toString()}>{item.name} {completed}</option>;
							})
						}
					</select>
				</label>
			</div>
		);
	}
}
