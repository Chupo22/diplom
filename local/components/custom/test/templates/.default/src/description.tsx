///<reference path="include.d.ts"/>
import * as React from 'react';
import {ITable} from "./test";
// import * as ReactDOM from 'react-dom';
// import {IProps} from './test';

interface IProps{
	tables: ITable[]
	dbName: string
}

interface IState{
}
export class Description extends React.Component<IProps, IState> {
	state:IState = {};
	
	constructor() {
		super();
	}
	
	render() {
		console.log('render -description');
		return (
			<div>
				<b>Краткая информация о базе данных "{this.props.dbName}":</b>
				<br/>
				Схема БД состоит из {this.props.tables.length} таблиц:<br/>
				
				{
					//Выводится описание таблиц базы данных
					this.props.tables.map((table)=>{
						return (
							<div key={table.name}>{table.name} ({table.columns.join(', ')})</div>
						);
					})
				}
				
				<br/>
			</div>
		);
	}
}
