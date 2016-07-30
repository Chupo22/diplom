<?
namespace Helpers;


use ATSModule\Info;
use CModule;

class Tools{
	static public function isAjax(){
		return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
	}
	public static function getTemplateAsset($type){
		$bAdminPage = !defined('SITE_TEMPLATE_ID');
		$entry = $bAdminPage ? 'admin' : SITE_TEMPLATE_ID;
		
		if(!$GLOBALS['JS_ASSETS']){
			if($bAdminPage){
				CModule::IncludeModule('automated_testing_system');
				$assetsPath = Info::$MODULE_PATH.'/build/assets.json';
			}
			else
				$assetsPath = DOCUMENT_ROOT.'/build/assets.json';
			
			$GLOBALS['JS_ASSETS'] = json_decode(file_get_contents($assetsPath), true);
		}
		return $GLOBALS['JS_ASSETS'][$entry][$type];
	}
	public static function getTemplateAsset2($type){
		$bAdminPage = !defined('SITE_TEMPLATE_ID');
		$entry = $bAdminPage ? 'admin' : 'test';
		
		if(!$GLOBALS['JS_ASSETS']){
			if($bAdminPage){
				CModule::IncludeModule('automated_testing_system');
				$assetsPath = Info::$MODULE_PATH.'/buildtest/assets.json';
			}
			else
				$assetsPath = DOCUMENT_ROOT.'/buildtest/assets.json';
			
			$GLOBALS['JS_ASSETS'] = json_decode(file_get_contents($assetsPath), true);
		}
		return $GLOBALS['JS_ASSETS'][$entry][$type];
	}
	//public static function prepareTextForJSON($sql){
	//	return str_replace([],[], $sql);
	//}
}
