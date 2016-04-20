<?
define('NO_KEEP_STATISTIC', true);
define("STOP_STATISTICS", true);
define('NO_AGENT_CHECK', true);
define("DisableEventsCheck", true);
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

use \AutomatedTestingSystem\ORM\ExerciseTable as Exercise;
use \AutomatedTestingSystem\ORM\UserExerciseTable as UserExercise;

$arAjaxResult = array('errors' => array());
if(!$_REQUEST['action'])
	$arAjaxResult['errors'][] = 'no action';

switch($_REQUEST['action']){
	case 'save':
		global $USER;
		$id = (int) $_REQUEST['item']['id'];
		$arExercise = Exercise::getList([
			'filter' => ['userExercise.id' => $id],
			'select' => ['id'],
			'limit' => 1
		])->fetch();
		
		$arItem = $_REQUEST['item'];
		$arItem = [
			'userId' => $USER->GetID(),
			'exerciseId' => $arExercise['id'],
			'query' => $_REQUEST['item']['query'],
		];
		
		if($id) {
			UserExercise::update($id, $arItem);
		}
		else
			UserExercise::add($arItem);
		
		
		break;
}

$arAjaxResult['success'] = !$arAjaxResult['errors'];
die(json_encode($arAjaxResult));
