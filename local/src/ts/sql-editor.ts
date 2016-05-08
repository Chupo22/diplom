import {ICompletion} from "sql-editor";
export var SqlEditor = require('react-ace');
require('../../../node_modules/brace/mode/mysql');
require('../../../node_modules/brace/theme/chrome');
require('../../../node_modules/brace/ext/language_tools');

export function addCompleterWords(words: ICompletion[]){
	var staticWordCompleter = {
		getCompletions: function(editor, session, pos, prefix, callback) {
			callback(null, words);
		}
	};
	var langTools = ace.acequire('ace/ext/language_tools');
	langTools.addCompleter(staticWordCompleter);
}

// export var SqlEditor;
