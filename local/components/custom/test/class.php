<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use LearningDatabase\CDatabase;
use LearningDatabase\CDatabaseTable;
use LearningDatabase\ORM\ExerciseTable as Exercise;
use LearningDatabase\ORM\UserExerciseTable as UserExercise;
use LearningDatabase\ORM\UserTaskTable as UserTask;
use LearningDatabase\ORM\TaskTable as Task;
use LearningDatabase\ORM\ConditionTable as Condition;


class Test extends CBitrixComponent {
	var $DB = false;

	public function onPrepareComponentParams($arParams)
	{
		if(!$arParams['TEST_CODE'])
			throw new Exception('No test code in params!');
		if(!$arParams['EXERCISE_NUMBER']){
			$arParams['EXERCISE_NUMBER'] = UserExercise::getList([
				'order' => ['EXERCISE.NUMBER' => 'ASC'],
				'filter' => ['COMPLETED' => false],
				'limit' => 1,
				'select' => ['NUMBER' => 'EXERCISE.NUMBER']
			])->fetch()['NUMBER'] ?: 1;
		}
		return $arParams;
	}

	public function executeComponent() {
		
		global $USER, $APPLICATION;
		if(!$USER->IsAuthorized())
			$APPLICATION->AuthForm('');
		
		//if(!Helpers\Tools::isAjax()){
			$this->DB = CDatabase::getConnection();
			$this->arResult['dbName'] = CDatabase::dbName;
			$this->arResult['tables'] = $this->getTables();
			$this->arResult['exercises'] = $this->getExercises($this->arParams['TEST_CODE']);
			$this->arResult['selected'] = $this->arParams['EXERCISE_NUMBER'];
			$this->arResult['exerciseNumber'] = $this->arParams['EXERCISE_NUMBER'];
			//$this->arResult['userExercise'] = self::getUserQuery();
			
			
			
			if($this->arParams['USER_QUERY']) {
				$bSuccessQuery = self::checkUserQuery();
				self::saveUserQuery($bSuccessQuery);
			}
				
		
		
			$this->IncludeComponentTemplate();
		//}else{
		//	die(json_encode($this->getCurrentExercise()));
		//}
		

	}
	
	public function getTables(){
		$dbName = CDatabase::dbName;
		$tables = 'INFORMATION_SCHEMA.TABLES';
		$columns = 'INFORMATION_SCHEMA.COLUMNS';
		/** @noinspection SqlDialectInspection */
		/** @noinspection SqlNoDataSourceInspection */
		$query = "
			SELECT $columns.COLUMN_NAME as colName, $tables.TABLE_NAME as tabName
		 	FROM $columns
		 	LEFT JOIN $tables ON $tables.TABLE_NAME = $columns.TABLE_NAME
		 	WHERE $columns.TABLE_SCHEMA='$dbName'
		";
		
		$dbResult = CDatabase::getConnection()->Query($query);
		$arResult = [];
		while($arItem = $dbResult->Fetch()){
			$arResult[$arItem['tabName']]['columns'][] = $arItem['colName'];
			$arResult[$arItem['tabName']]['name'] = $arItem['tabName'];
		}
		$arResult = array_values($arResult);
		return $arResult;
	}
	
	public function getExercises($testCode){
		global $USER;
		$arResult = [];
		$arFilter = [
			'EXERCISE.TEST.CODE' => $testCode,
			'USER.ID' => $USER->GetID()
		];
		
		
		if(!UserTask::getList(['filter' => $arFilter, 'limit' => 1])->getSelectedRowsCount())
			$this->generateUserExercise($testCode);
		
		$dbRes = UserTask::getList([
			'order' => ['EXERCISE.NUMBER' => 'ASC'],
			'filter' => $arFilter,
			'select' => [
				'number' => 'EXERCISE.NUMBER',
				'name' => 'EXERCISE.NAME',
				'query' => 'EXERCISE.USER_EXERCISE.QUERY',
				'completed' => 'EXERCISE.USER_EXERCISE.COMPLETED',
				
				'table' => 'TABLE',
				'column' => 'COLUMN',
				'condition' => 'CONDITION',
				'value' => 'VALUE',
			]
		]);
		while($arEx = $dbRes->fetch()){
			$arResult[$arEx['number']]['name'] = $arEx['name'];
			$arResult[$arEx['number']]['number'] = $arEx['number'];
			$arResult[$arEx['number']]['query'] = $arEx['query'];
			$arResult[$arEx['number']]['completed'] = $arEx['completed'];
			$arResult[$arEx['number']]['tasks'][] = [
				'table' => $arEx['table'],
				'column' => $arEx['column'],
				'condition' => $arEx['condition'],
				'value' => $arEx['value'],
			];
		}
		return array_values($arResult);
	}
	public function generateUserExercise($testCode){
		if(!$testCode)
			throw new Exception('No test code in exercise generation!');
		
		$dbRes = Task::getList([
			'order' => ['EXERCISE.NUMBER' => 'ASC'],
			'filter' => [
				'EXERCISE.TEST.CODE' => $testCode,
			],
			'select' => [
				'exerciseId' => 'EXERCISE.ID',
				//'name' => 'EXERCISE.NAME',
				//'query' => 'EXERCISE.USER_EXERCISE.QUERY',
				//'completed' => 'EXERCISE.USER_EXERCISE.COMPLETED',
				
				'table' => 'TABLE',
				'column' => 'COLUMN',
				'condition' => 'CONDITION',
				'value' => 'VALUE',
			]
		]);
		$arExercises = [];
		$arTasks = [];
		while($arItem = $dbRes->fetch()){
			$table = UserTask::getTable($arItem['table']);
			$column = UserTask::getColumn($table, $arItem['column']);
			$condition = UserTask::getCondition($table, $column, $arItem['condition']);
			$value = UserTask::getValue($table, $column, $condition, $arItem['value']);
			
			$arExercises[$arItem['exerciseId']][]= [
				'table' => $table,
				'column' => $column,
				'condition' => $condition,
				'value' => $value,
			];
			
		}
		//$GLOBALS['APPLICATION']->RestartBuffer();\CPL::pr($arExercises);die;
		global $USER;
		foreach($arExercises as $exId => $arTasks){
			
			$arUserExerciseId = UserExercise::add([
				'USER_ID' => $USER->GetID(),
				'EXERCISE_ID' => $exId,
				'QUERY' => '',
				'COMPLETED' => false,
			])->getId();
			
			foreach($arTasks as $arTask){
				UserTask::add([
					'USER_EXERCISE_ID' => $arUserExerciseId,
					'TABLE' => $arTask['table'],
					'COLUMN' => $arTask['column'],
					'CONDITION' => $arTask['condition'],
					'VALUE' => $arTask['value'],
				]);
			}
		}
	}
	
	public function checkUserQuery(){
		$arTasks = [];
		$dbTasks = Task::getList([
			'filter' => [
				'EXERCISE.ID' => $this->arParams['EXERCISE_NUMBER'],
			],
			'select' => [
				'CONDITION',
				'VALUE',
				'COLUMN',
			]
		]);
		while($arTask = $dbTasks->fetch())
			$arTasks[] = $arTask;
		if(!$arTasks)
			return false;
		
		$arElements = [];
		$dbResult = CDatabase::getConnection()->Query(htmlspecialchars_decode($this->arParams['USER_QUERY']));
		while($arElement = $dbResult->fetch())
			$arElements[] = $arElement;
		if(!$arElements)
			return false;
		
		foreach($arTasks as $arTask)
			foreach($arElements as $arElement)
				if(!Condition::checkResult($arTask['CONDITION'], $arTask['VALUE'], $arElement[$arTask['COLUMN']]))
					return false;
		
		return true;
	}
	public function saveUserQuery($bCompleted) {
		if($this->arResult['userExercise']['id'])
			UserExercise::update($this->arResult['userExercise']['id'], [
				'COMPLETED' => $bCompleted,
				'QUERY' => $this->arParams['USER_QUERY'],
			]);
		else
			UserExercise::add([
				'COMPLETED' => $bCompleted,
				'QUERY' => $this->arParams['USER_QUERY'],
				'EXERCISE_ID' => $this->arParams['EXERCISE_NUMBER'],
				'USER_ID' => (new CUser)->getId(),
			]);
	}
}
