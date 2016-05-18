<?
define('NO_KEEP_STATISTIC', true);
define("STOP_STATISTICS", true);
define('NO_AGENT_CHECK', true);
define("DisableEventsCheck", true);
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

use AutomatedTestingSystem\CDatabase;
use \AutomatedTestingSystem\ORM\ExerciseTable as Exercise;
use \AutomatedTestingSystem\ORM\UserExerciseTable as UserExercise;
use AutomatedTestingSystem\QueryChecker;

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
			$test = 'update';
			UserExercise::update($id, $arItem);
		}
		else{
			$test = 'add';
			UserExercise::add($arItem);
		}
		
		
		break;
	case 'execQuery':
		if(!$userExId = (int)$_REQUEST['userExerciseId'])
			$arAjaxResult['errors'][] = 'No userExerciseId!';
		if(!$query = $_REQUEST['query'])
			$arAjaxResult['errors'][] = 'No query!';
		
		$dbRes = false;
		if(!$arExercise['errors']){
			$db = CDatabase::getConnection();
			$dbRes = $db->Query($query, true);
			if(!$dbRes){
				$arAjaxResult['errors'][] = $query;
				$arAjaxResult['errors'][] = $db->GetErrorMessage();
			}else{
				$obQueryChecker = new QueryChecker($userExId, $query);
				if(!$obQueryChecker->isSuccess){
					if($obQueryChecker->arErrors)
						$arAjaxResult['errors'] =  array_merge($arAjaxResult['errors'], $obQueryChecker->arErrors);
					else
						$arAjaxResult['errors'][] = 'Ошибка при проверке результата!';
				}else{
					UserExercise::update($userExId, ['completed' => true, 'successQuery' => $query]);
				}
				$arAjaxResult['success'] = $obQueryChecker->isSuccess;
				if($arAjaxResult['success'])
					$arAjaxResult['items'] = array_slice($obQueryChecker->arUserResult, 0, 10);
				$arAjaxResult['errorItems'] = array_slice($obQueryChecker->arErrorResult, 0, 10);
			}
		}
		break;
}

$arAjaxResult['success'] = !$arAjaxResult['errors'];
die(json_encode($arAjaxResult));
