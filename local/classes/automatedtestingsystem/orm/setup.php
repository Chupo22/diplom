<?
namespace AutomatedTestingSystem\ORM;
use AutomatedTestingSystem\ORM\TestTable as Test;
use AutomatedTestingSystem\ORM\ExerciseTable as Exercise;
use AutomatedTestingSystem\ORM\TaskTable as Task;
use AutomatedTestingSystem\ORM\ConditionTable as Condition;
use Bitrix\Main\UserTable as User;


class Setup{
	
	public function __construct() {
		$this->arTests = [];
		$this->arExercises = [];
		$this->arConditions = [];
		$this->arTasks = [];
	}

	public static function resetDataBases(){
		global $DB;
		foreach([
			get_class(new Test),
			get_class(new Exercise),
			get_class(new Condition),
			get_class(new Task),
			get_class(new UserExerciseTable),
			get_class(new UserTaskTable),
		] as $class){
			/** @noinspection PhpUndefinedMethodInspection */
			$dbName = $class::getTableName();
			$DB->Query("DROP TABLE IF EXISTS `$dbName`");
			/** @noinspection PhpUndefinedMethodInspection */
			$DB->Query($class::getEntity()->compileDbTableStructureDump()[0]);
		}
	}
	
	public function setTests($arTests){
		$arResult = [];
		foreach($arTests as $arTest)
			$arResult[$arTest['code']] = $arTest;
		
		foreach($arResult as &$arTest)
			$arTest['id'] = Test::add($arTest)->getId();
		unset($arTest);
		$this->arTests = $arResult;
	}
	
	public function setConditions($arItems){
		$arResult = [];
		foreach($arItems as $arItem){
			$arItem['id'] = Condition::add($arItem)->getId();
			$arResult[] = $arItem;
		}
		$this->arConditions = $arResult;
	}
	
	public function setExercises($arTestsWithExercises){
		$arResult = [];
		foreach($arTestsWithExercises as $TEST_CODE => $arItems){
			foreach($arItems as $number => $arExercise){
				$number++;
				$arExercise['number'] = $number;
				$arExercise['name'] = 'Exercise '.$number;
				$arExercise['testId'] = $this->arTests[$TEST_CODE]['id'];
				
				$arTasks = $arExercise['TASKS'];
				unset($arExercise['TASKS']);
				$arExercise['id'] = Exercise::add($arExercise)->getId();
				
				foreach($arTasks as $arTask){
					$arTask['exerciseId'] = $arExercise['id'];
					Task::add($arTask);
				}
				
				
				$arResult[] = $arExercise;
			}
		}
	}
	
	public function setUser(){
		global $USER;
		$login = 'tester';
		$arUser = User::getList([
			'filter' => ['LOGIN' => $login],
			'select' => ['ID']
		])->fetch();
		$arFields = [
			'LOGIN' => $login,
			'PASSWORD' => 'tester',
			'EMAIL' => 'test@test.ru'
		];
		if(!$arUser)
			$USER->Add($arFields);
		else
			$USER->Update($arUser['ID'], $arFields);
		
		if($USER->LAST_ERROR)
			\CPL::pr($USER->LAST_ERROR);
	}
}
