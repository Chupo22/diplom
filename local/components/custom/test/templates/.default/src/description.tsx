///<reference path="include.d.ts"/>
import * as React from 'react';
import * as ReactDOM from 'react-dom';
import {IProps} from './test';


interface IState{
	
}
export class Description extends React.Component<IProps, {}> {
	state:IState = {};
	
	constructor() {
		super();
	}
	
	render() {
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
				
				Тут должно быть описание. Нужно придумать как его формировать.
				<br/>
			</div>
		);
	}
}
