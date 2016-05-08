<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use AutomatedTestingSystem\CDatabase;
use AutomatedTestingSystem\CDatabaseTable;
use AutomatedTestingSystem\ORM\ExerciseTable as Exercise;
use AutomatedTestingSystem\ORM\TestTable;
use AutomatedTestingSystem\ORM\UserExerciseTable as UserExercise;
use AutomatedTestingSystem\ORM\UserTaskTable as UserTask;
use AutomatedTestingSystem\ORM\TaskTable as Task;
use AutomatedTestingSystem\ORM\ConditionTable as Condition;
use Bitrix\Main\Application;


class Test extends CBitrixComponent {
	var $DB = false;

	public function onPrepareComponentParams($arParams)
	{
		if(!$arParams['TEST_CODE'])
			throw new Exception('No test code in params!');
		if(!$arParams['EXERCISE_NUMBER']){
			$arParams['EXERCISE_NUMBER'] = UserExercise::getList([
				'order' => ['exercise.number' => 'ASC'],
				'filter' => ['completed' => false],
				'limit' => 1,
				'select' => ['number' => 'exercise.number']
			])->fetch()['number'] ?: 1;
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
			$this->arResult['completion'] = $this->getCompletion($this->arResult['tables']);
			$this->arResult['test'] = $this->getTest();
			$this->arResult['exercises'] = $this->getExercises($this->arParams['TEST_CODE']);
			$this->arResult['selected'] = $this->arParams['EXERCISE_NUMBER'];
			$this->arResult['exerciseNumber'] = $this->arParams['EXERCISE_NUMBER'];
			//$this->arResult['userExercise'] = self::getUserQuery();
			
			//$GLOBALS['APPLICATION']->RestartBuffer();\CPL::pr($this->arResult);die;
			
			//if($this->arParams['USER_QUERY']) {
			//	$bSuccessQuery = self::checkUserQuery();
			//	self::saveUserQuery($bSuccessQuery);
			//}
				
		
		
			$this->includeComponentTemplate();
		//}else{
		//	die(json_encode($this->getCurrentExercise()));
		//}
		

	}
	
	public function getCompletion($arTables){
		$arResult = [];
		foreach($arTables as $arTable){
			$arResult[] = [
				'caption' => $arTable['name'].' (table)',
				'value' => $arTable['name'],
				'meta' => 'ATS',
			];
			foreach ($arTables['columns'] as $arColumn) {
				$arResult[] = [
					'caption' => $arColumn['name'].' (column)',
					'value' => $arColumn['name'],
					'meta' => 'ATS',
				];    
			}
		}
		return $arResult;
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
	
	public function getTest(){
		$arResult = [];
		$dbRes = TestTable::getList([
			'filter' => ['code' => $this->arParams['TEST_CODE']],
			'limit' => 1,
			'select' => [
				'name',
				'code',
				'id'
			]
		]);
		while($arItem = $dbRes->fetch())
			$arResult[] = $arItem;
		
		return $arResult;
	}
	
	public function getExercises($testCode){
		global $USER;
		$arResult = [];
		$arFilter = [
			'exercise.test.code' => $testCode,
			'userExercise.user.ID' => $USER->GetID()
		];
		
		if(!UserTask::getList(['filter' => $arFilter, 'limit' => 1])->getSelectedRowsCount())
			$this->generateUserExercise($testCode);

		$dbRes = UserTask::getList([
			'order' => ['exercise.number' => 'ASC'],
			'filter' => $arFilter,
			'select' => [
				'taskId' => 'id',
				'userExerciseId',
				'number' => 'exercise.number',
				'name' => 'exercise.name',
				'query' => 'userExercise.query',
				'completed' => 'userExercise.completed',
				
				'type',
				'table',
				'column',
				'condition',
				'value',
			]
		]);
		while($arEx = $dbRes->fetch()){
			$arResult[$arEx['number']]['userExerciseId'] = $arEx['userExerciseId'];
			$arResult[$arEx['number']]['name'] = $arEx['name'];
			$arResult[$arEx['number']]['number'] = $arEx['number'];
			$arResult[$arEx['number']]['query'] = base64_encode($arEx['query']);
			$arResult[$arEx['number']]['completed'] = $arEx['completed'];
			$arResult[$arEx['number']]['tasks'][] = [
				'id' => $arEx['taskId'],
				'type' => $arEx['type'],
				'table' => $arEx['table'],
				'column' => $arEx['column'],
				'condition' => $arEx['condition'],
				'value' => $arEx['value'],
			];
		}
		return array_values($arResult);
	}
	
	//public function getExercise($exId){
	//	return array_filter($this->arResult['exercises'], function($arExercise) use($exId){
	//		return $arExercise['id'] == $exId;
	//	})[0];
	//}
	
	public function generateUserExercise($testCode){
		if(!$testCode)
			throw new Exception('No test code in exercise generation!');
		
		$dbRes = Task::getList([
			'order' => ['exercise.number' => 'ASC'],
			'filter' => [
				'exercise.test.code' => $testCode,
			],
			'select' => [
				'exerciseId',
				//'name' => 'EXERCISE.NAME',
				//'query' => 'EXERCISE.USER_EXERCISE.QUERY',
				//'completed' => 'EXERCISE.USER_EXERCISE.COMPLETED',
				
				'type',
				'table',
				'column',
				'condition',
				'value',
			]
		]);
		$arExercises = [];
		$arTasks = [];
		while($arItem = $dbRes->fetch()){
			$type = $arItem['type'];
			$table = UserTask::getTable($arItem['table']);
			$column = UserTask::getColumn($table, $arItem['column']);
			$condition = UserTask::getCondition($table, $column, $arItem['condition']);
			$value = UserTask::getValue($table, $column, $condition, $arItem['value']);
			
			$arExercises[$arItem['exerciseId']][]= [
				'type' => $type,
				'table' => $table,
				'column' => $column,
				'condition' => $condition,
				'value' => $value,
			];
			
		}
		global $USER;
		foreach($arExercises as $exId => $arTasks){
			
			$arUserExerciseId = UserExercise::add([
				'userId' => $USER->GetID(),
				'exerciseId' => $exId,
				'query' => '',
				'completed' => false,
			])->getId();
			
			foreach($arTasks as $arTask){
				UserTask::add([
					'userExerciseId' => $arUserExerciseId,
					'type' => $arTask['type'],
					'table' => $arTask['table'],
					'column' => $arTask['column'],
					'condition' => $arTask['condition'],
					'value' => $arTask['value'],
				]);
			}
		}
	}
	
	public function checkUserQuery($exerciseId){
		$arTasks = [];
		$dbTasks = Task::getList([
			'filter' => [
				'exercise.id' => $this->arParams['EXERCISE_NUMBER'],
			],
			'select' => [
				'condition',
				'value',
				'column',
			]
		]);
		while($arTask = $dbTasks->fetch())
			$arTasks[] = $arTask;
		if(!$arTasks)
			return false;
		
		$arElements = [];
		$dbResult = CDatabase::getConnection()->Query(htmlspecialchars_decode($this->arParams['USER_QUERY']));
		while($arElement = $dbResult->Fetch())
			$arElements[] = $arElement;
		if(!$arElements)
			return false;
		
		foreach($arTasks as $arTask)
			foreach($arElements as $arElement)
				if(!Condition::checkResult($arTask['condition'], $arTask['value'], $arElement[$arTask['column']]))
					return false;
		
		return true;
	}
	//public function saveUserQuery($bCompleted) {
	//	if($this->arResult['userExercise']['id'])
	//		UserExercise::update($this->arResult['userExercise']['id'], [
	//			'completed' => $bCompleted,
	//			'query' => $this->arParams['USER_QUERY'],
	//		]);
	//	else
	//		UserExercise::add([
	//			'completed' => $bCompleted,
	//			'query' => $this->arParams['USER_QUERY'],
	//			'exerciseId' => $this->arParams['EXERCISE_NUMBER'],
	//			'userId' => (new CUser)->GetID(),
	//		]);
	//}
}
