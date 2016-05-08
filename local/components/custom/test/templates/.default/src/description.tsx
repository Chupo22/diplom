///<reference path="include.d.ts"/>
import * as React from 'react';
import {ITable, IExercise} from "./test";
import {State} from "./state";

interface IProps{
	tables: ITable[]
	dbName: string
	exercise: IExercise
	exercises: IExercise[]
	exerciseNumber: number
	onChangeExercise: React.EventHandler<React.FormEvent>
}

interface IState{
}
export class Description extends React.Component<IProps, IState> {
	state:IState = {};
	
	componentWillReceiveProps(newProps: IProps){
		this.props = newProps;
		this.setState({});
	}
	
	render() {
		return (
			<div>

				<h2>Краткая информация о базе данных "{this.props.dbName}":</h2>
				<div className="bs-callout bs-callout-info">
					<h4>Схема БД состоит из {this.props.tables.length} таблиц:</h4>
					{
						//Выводится описание таблиц базы данных
						this.props.tables.map((table, index)=>{
							return (
								<p key={table.name}>{table.name} ({table.columns.join(', ')})</p>
							);
						})
					}
				</div>
				
				<State 
				 	onChange={this.props.onChangeExercise}
				 	exercises={this.props.exercises}
				 	exerciseNumber={this.props.exerciseNumber}
					exercise={this.props.exercise}
				/>
				
				<h2>Задача:</h2>
				<div className="bs-callout bs-callout-info">
					<div className="row">{
						this.props.exercise.tasks.map((task) => 
							<p key={task.id}> 
								Выбрать из таблицы <b>{task.table}</b>&nbsp;
								поле <b>{task.column}</b>&nbsp;
								с условием <b>{task.condition}</b>&nbsp;
								по значению <b>{task.value}</b>.&nbsp;
								TYPE = {task.type}
							</p>
						)
					}</div>
				</div>
			</div>
		);
	}
}
