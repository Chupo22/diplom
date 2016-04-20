///<reference path="../typings/tsd.d.ts"/>
import $ = require('jquery');

import {IExercise} from "./exercise";
export interface IUserExercise extends IExercise{
	query?: string
	completed?: boolean
	exerciseId?: number
	userId?: number
}

export class UserExercise{
	// static add(){
	//	
	// }
	// static update(){
	//	
	// }
	//
	// static remove(){
	//	
	// }
	
	static save(item: IUserExercise){
		
		$.ajax('/ajax/user-exercise.php', {
			dataType: 'json',
			method: 'POST',
			cache: false,
			data: {
				action: 'save',
				item: item
			},
			success: function (result) {
			}
		});	
	}
}
