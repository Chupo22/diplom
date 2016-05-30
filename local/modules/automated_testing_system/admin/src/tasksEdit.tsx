///<reference path="../../../../../local/src/typings/tsd.d.ts"/>
import React = require('react');
import ReactDOM = require('react-dom');
import _ =  require('lodash');

interface IState {
	table?: ITable
	column?: IColumn
	condition?: string
	value?: string
	template?: string
}
interface IProps {
	tables?: ITable[]
}
interface ITable {
	name: string
	columns: IColumn[]
}
interface IColumn {
	name: string
	conditions: string[]
}
class TasksEdit extends React.Component<IProps, IState> {
	state: IState = {
	};
	DOMTemplate: JSX.Element;
	// refs:{
	// 	'templateTextArea': __React.ReactInstance
	// };
	
	constructor(){
		super();
	}
	
	get table(){
		return this.state.table || {name:'', columns:[]};
	}
	set table(val){
		this.setState({
			table: val,
			column: undefined
		});
	}
	onChangeTable(e: any){
		this.table = _.find(this.props.tables, {name: e.target.value});
		this.column = undefined;
	}
	
	get column(){
		return this.state.column || {name:'', conditions:[]};
	}
	set column(val){
		this.setState({
			column: val,
			condition: undefined
		});
	}
	onChangeColumn(e: any){
		this.column = _.find(this.table.columns, {name: e.target.value});
	}
	
	get condition(){
		return this.state.condition || '';
	}
	set condition(val){
		this.setState({
			condition: val
		});
	}
	onChangeCondition(e: any){
		this.condition = e.target.value;
	}
	
	get value(){
		return this.state.value;
	}
	set value(val){
		this.setState({
			value: val
		});
	}
	onChangeValue(e: any){
		this.value = e.target.value;
	}
	
	get template(){
		return this.state.template;
	}
	set template(val){
		this.setState({
			template: val
		});
	}
	
	insertTemplateKey(e){
		var insertTextAtCursor = function insertTextAtCursor(el, text) {
			var selectionStart = el.selectionStart;
			var selectionEnd = el.selectionEnd;
			el.value = el.value.slice(0, selectionStart) + text + el.value.slice(selectionEnd);
			el.selectionStart = el.selectionEnd = selectionStart + text.length;
			el.focus();
		};
		insertTextAtCursor(this.refs['templateTextArea'], e.target.innerHTML);
	}
	
	render(){
		//noinspection CheckTagEmptyBody
		return (
			<div>
				<select
					name="data[task][table]"
					onChange={this.onChangeTable.bind(this)}
				>
					<option value="">Выберите таблицу</option>
					{
						this.props.tables.map((table, index)=>{
							return <option key={index} value={table.name}>{table.name}</option>;
						})
					}
				</select>
				<select
					name="data[task][column]"
					onChange={this.onChangeColumn.bind(this)}
					disabled={!this.table.name}
					value={this.column.name}
				>
					<option value="">Выберите колонку</option>
					{
						this.table.columns.map((column, index)=>{
							return <option key={index} value={column.name}>{column.name}</option>;
						})
					}
				</select>
				<select
					name="data[task][condition]"
					onChange={this.onChangeCondition.bind(this)}
					disabled={!this.column.name}
					value={this.condition}
				>
					<option value="">Выберите условие</option>
					{
						this.column.conditions.map((condition, index)=>{
							return <option key={index} value={condition}>{condition}</option>;
						})
					}
				</select>
				<input
					name="data[task][value]"
					onChange={this.onChangeValue.bind(this)}
					disabled={!this.condition}
					type="text"
					placeholder="Укажите значение"
				/>
				<br />
				<br />
				<textarea
					name="data[template]"
					cols={50}
					rows={5}
					style={{float: 'left'}}
					ref="templateTextArea"
				></textarea>
				<div style={{float: 'left'}}>
					<a onClick={this.insertTemplateKey.bind(this)}>#TABLE#</a><br />
					<a onClick={this.insertTemplateKey.bind(this)}>#COLUMN#</a><br />
					<a onClick={this.insertTemplateKey.bind(this)}>#CONDITION#</a><br />
					<a onClick={this.insertTemplateKey.bind(this)}>#VALUE#</a><br />
				</div>
			</div>
		);
	}
}

export function bootstrap(params){
	ReactDOM.render(<TasksEdit {... params} />, document.getElementById('tasks-container'));
}
