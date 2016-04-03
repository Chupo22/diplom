///<reference path="../../../../../../src/typings/tsd.d.ts"/>
import * as React from 'react';
import * as hljs from 'highlight.js';


export interface IState {
	query?: string
}
export interface IProps {
	
}

export class Query extends React.Component<IProps, IState> {
	state:IState = {};
	
	onChange(e: any){
		this.setState({
			query: e.target.value
		});
		setTimeout(()=>{
			if(this.state.query)
				hljs.highlightBlock(document.getElementById('highlight'));
		}, 1)
	}
	
	test(q,w,e){
		console.log(q,w,e);
	}
	
	render() {
		return (
			<div>
				Запрос:<br/>
				<textarea id="queryInput" className="form-control" onChange={this.onChange.bind(this)}/>
				{(()=>{
					if(this.state.query)
						return <pre><code id="highlight" className="sql">{this.state.query}</code></pre>
				})()}
				<input type="submit" style={{width: 200}} className="form-control btn-default"/>
			</div>
		);
	}
}
