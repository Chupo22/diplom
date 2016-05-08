// module 'helpers'{export * from '../ts/helpers'}
// module 'exercise'{export * from '../ts/exercise'}

declare module 'helpers'{
	//noinspection SpellCheckingInspection
	export function base64_decode(utftext: string): string 
	export function delay(func, delay)
	
	export interface IAjaxResult{
		success?: boolean
		errors?: string[]
	}
}

declare module 'exercise'{
	export interface IExercise{
		id?: number
		name?: string
		number: number
	}
}

declare module 'user-exercise'{
	import {IExercise} from 'exercise';
	import helpers = require('helpers');
	
	export interface IUserExercise extends IExercise{
		query?: string
		completed?: boolean
		exerciseId?: number
		userId?: number
	}
	export interface ISaveResult extends helpers.IAjaxResult{
		items: any
	}
	export class UserExercise{
		static ajaxUrl: string;
		static ajaxActions : {
			save: string,
			execQuery: string,
		};
		static save(item: IUserExercise): void
	}
}


declare module 'sql-editor'{
	export var SqlEditor: any;
	export function addCompleterWords(words: ICompletion[]): void
	
	export interface ICompletion{
		caption: string
		value: string
		meta: string
	}
}

declare namespace AceAjax{
	//noinspection SpellCheckingInspection
	export interface Ace {
		acequire(name:string):any
	}
}
