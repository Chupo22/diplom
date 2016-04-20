///<reference path="include.d.ts"/>

import * as React from 'react';
import {IExercise} from "./test";
// import * as ReactDOM from 'react-dom';
// import {DropdownButton, MenuItem} from 'react-bootstrap';

interface IState{
	
}

interface IProps{
	onChange?: React.EventHandler<React.FormEvent>
	exercises?: IExercise[]
	exerciseNumber?: number
}

export class State extends React.Component<IProps, IState> {

	constructor() {
		super();
	}
	
	render() {
		console.trace('render - state');
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
				<br/>
			</div>
		);
	}
}
