<?
namespace AutomatedTestingSystem;


use AutomatedTestingSystem\ORM\ConditionTable;
use AutomatedTestingSystem\ORM\TaskTable;
use AutomatedTestingSystem\ORM\TaskTypeTable;
use AutomatedTestingSystem\ORM\UserExerciseTable;
use AutomatedTestingSystem\ORM\UserTaskTable;

class QueryChecker{

	private $arUserTasks;
	public $isSuccess;

	public $arErrors = [];
	public $verificationQueries;
	public $arErrorResult;
	public $arUserResult;
	
	public function __construct($userExId, $userQuery) {
		$this->userExerciseId = $userExId;
		$this->userQuery = $userQuery;
		$this->setUserTasks();
		$this->setVerificationQuery();
		$this->setResult();
	}
	
	private function setUserTasks(){
		$arTasks = [];
		$dbRes = UserTaskTable::getList([
			'select' => [
				'id',
				'table',
				'column',
				'condition',
				'value',
				'type'
			],
			'filter' => ['userExercise.id' => $this->userExerciseId],
		]);
		
		while($arTask = $dbRes->fetch()){
			$arTasks[$arTask['type']][] = $arTask;
		}
		$this->arUserTasks = $arTasks;
	}

	private function setVerificationQuery(){
		$arSelect = [];
		foreach ($this->arUserTasks[TaskTypeTable::TYPE_SELECT] as $arTask) {
		    $arSelect[] = '`'.$arTask['column'].'`';
		}
		
		$arWhere = [];
		foreach ($this->arUserTasks[TaskTypeTable::TYPE_FILTER] as $arTask) {
		    if(!$arSelect)
				$arSelect[] = '`'.$arTask['column'].'`';
			$column = $arTask['column'];
			$condition = ConditionTable::getSqlCondition($arTask['condition']);
			$value = $arTask['value'];
			
			switch($arTask['condition']){
				case ConditionTable::CONDITION_LIKE:
					$arWhere[] = "(`$column` like '%{$arTask['value']}%')";
					break;
				case ConditionTable::CONDITION_IN:
					$arWhere[] = "(`$column` in ({$arTask['value']}))";
					break;
				case ConditionTable::CONDITION_BETWEEN:
					$arValues = explode(',', $arTask['value']);
					$arWhere[] = "(`$column` between '{$arValues[0]}' and '{$arValues[1]}')";
					break;
				default:
					$arWhere[] = "(`$column` $condition '$value')";
					break;
			}
			
		}

		$tableName = reset($this->arUserTasks[TaskTypeTable::TYPE_FILTER])['table'];
		$columnName = $this->getUniqueColumn($tableName);
		$userQuery = str_replace(PHP_EOL, '', $this->userQuery);
		$userQuery = preg_replace('/^select (.*?) from/i', "select `$columnName` from", $userQuery, 1);

		$verificationQuery = "select `$columnName` from `$tableName` where ".implode(' AND ', $arWhere);
		$query1 = 'select '.implode(',', $arSelect)." from `$tableName` where `$columnName` IN($verificationQuery) AND `$columnName` NOT IN($userQuery)";
		$query2 = 'select '.implode(',', $arSelect)." from `$tableName` where `$columnName` IN($userQuery) AND `$columnName` NOT IN($verificationQuery)";
		
		$this->verificationQueries = [$query1, $query2];
	}
	
	private function setResult(){
		$db = CDatabase::getConnection();
		$this->arErrorResult = [];
		foreach($this->verificationQueries as $query){
			$checkDbResult = $db->Query($query, true);
			if(!$checkDbResult){
				if($errorMess = $db->GetErrorMessage())
					$this->arErrors[] = "Verification sql error: ".$errorMess;
			}
			else
				while($arItem = $checkDbResult->Fetch())
					$this->arErrorResult[] = $arItem;
		}
		
		
		$dbUserRes = $db->Query($this->userQuery, true);
		if(!$dbUserRes)
			$this->arErrors[] = 'sql error!';
		if($this->arErrorResult)
			$this->arErrors[] = 'Filter task error!';
		if($dbUserRes){
			$this->arUserResult = [];
			while($arItem = $dbUserRes->Fetch()){
				$this->arUserResult[] = $arItem;
			}
		}

		$arFirstItem = $this->arUserResult[0];
		if($arFirstItem && !$this->checkSelect($this->arUserResult[0]))
		{
			$this->arErrors[] = 'Select task error!';
		}

		$this->isSuccess = !$this->arErrorResult && !$this->arErrors;
	}
	
	private function getUniqueColumn($tableName){
		$arConvert = [
			'departments' => 'dept_no',
			'dept_emp' => 'emp_no',
			'dept_manager' => 'emp_no',
			'employees' => 'emp_no',
			'salaries' => 'id',
			'titles' => 'id',
		];
		return $arConvert[$tableName];

	}

	private function checkSelect($arItem){
		$result = true;
		$arTaskColumns = [];
		$arUserColumns = array_keys($arItem);
		foreach ($this->arUserTasks[TaskTypeTable::TYPE_SELECT] as $arUserTask) {
			$arTaskColumns[] = $arUserTask['column'];
		}
		if($arTaskColumns){
			$result = !array_diff($arTaskColumns, $arUserColumns);
		}
		return $result;
	}
}
