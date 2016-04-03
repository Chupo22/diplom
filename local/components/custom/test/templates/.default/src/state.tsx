///<reference path="include.d.ts"/>

import * as React from 'react';
import * as ReactDOM from 'react-dom';
import {DropdownButton, MenuItem} from 'react-bootstrap';
import {IProps as IParentProps} from './test';
import {} from '../../../../../../../node_modules/highlight.js/lib/'

interface IState{
	
}

export class State extends React.Component<IParentProps, IState> {

	constructor() {
		super();
	}
	
	render() {
		var props = this.props;
		return (
			<div>
				<label>
					<select className="form-control" style={{maxWidth:300,float: 'right'}}>
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
