<?
namespace Helpers;


class Tools{
	static public function isAjax(){
		return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
	}	
	
	//public static function prepareTextForJSON($sql){
	//	return str_replace([],[], $sql);
	//}
}
