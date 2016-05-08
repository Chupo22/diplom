///<reference path="../typings/include.d.ts"/>
import $ = require('jquery');
import helpers = require('helpers');
import {IUserExercise} from "user-exercise";

//noinspection JSUnusedGlobalSymbols
export class UserExercise{
	static ajaxUrl =  '/ajax/user-exercise.php';
	static ajaxActions = {
		save: 'save',
		execQuery: 'execQuery',
	};
	static save(item: IUserExercise){
		//noinspection TypeScriptValidateTypes
		$.ajax(this.ajaxUrl, {
			dataType: 'json',
			method: 'POST',
			cache: false,
			data: {
				action: this.ajaxActions.save,
				item: item
			},
			success: function (result) {
			}
		});	
	}
}
