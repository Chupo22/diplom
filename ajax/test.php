<?
define('NO_KEEP_STATISTIC', true);
define("STOP_STATISTICS", true);
define('NO_AGENT_CHECK', true);
define("DisableEventsCheck", true);
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

$arAjaxResult = array('errors' => array());
if(!$_REQUEST['action'])
	$arAjaxResult['errors'][] = 'no action';

switch($_REQUEST['action']){
	case 'getExercise':
			$APPLICATION->IncludeComponent('custom:test', '',[],false);
		break;
}

$arAjaxResult['success'] = !$arAjaxResult['errors'];
die(json_encode($arAjaxResult));
