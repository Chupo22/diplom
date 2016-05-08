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
	public $verificationQuery;
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
			$arWhere[] = "(`$column` $condition '$value')";
		}
		
		$tableName = reset($this->arUserTasks[TaskTypeTable::TYPE_FILTER])['table'];
		$userQuery = str_replace(PHP_EOL, '', $this->userQuery);
		$userQuery = preg_replace('/^select(.*?)from/i', 'select `'.$this->getUniqueColumn($tableName).'` from', $userQuery, 1);
		$arWhere[] = $this->getUniqueColumn($tableName)." NOT IN($userQuery)";
		$query = 'select '.implode(',', $arSelect).' from `'.$tableName.'` where '.implode(' AND ', $arWhere);
		
		$this->verificationQuery = $query;
	}
	
	private function setResult(){
		$db = CDatabase::getConnection();
		$checkDbResult = $db->Query($this->verificationQuery);
		$this->arErrorResult = [];
		while($arItem = $checkDbResult->Fetch()){
			$this->arErrorResult[] = $arItem;
		}
		$this->isSuccess = !$this->arErrorResult; 
		
		$dbUserRes = $db->Query($this->userQuery);
		$this->arUserResult = [];
		while($arItem = $dbUserRes->Fetch()){
			$this->arUserResult[] = $arItem;
		}
	}
	
	private function getUniqueColumn($tableName){
		$arConvert = [
			'departments' => 'dept_no',
			'dept_emp' => 'emp_no',
			'dept_manager' => 'emp_no',
			'employees' => '',
			'salaries' => 'id',
			'titles' => '',
		];
		return $arConvert[$tableName];

	}	
}
