<?
namespace LearningDatabase\ORM;
use LearningDatabase\ORM\TestTable as Test;
use LearningDatabase\ORM\ExerciseTable as Exercise;
use LearningDatabase\ORM\TaskTable as Task;
use LearningDatabase\ORM\ConditionTable as Condition;


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
			$arResult[$arTest['CODE']] = $arTest;
		
		foreach($arResult as &$arTest)
			$arTest['ID'] = Test::add($arTest)->getId();
		unset($arTest);
		$this->arTests = $arResult;
	}
	
	public function setConditions($arItems){
		$arResult = [];
		foreach($arItems as $arItem){
			$arItem['ID'] = Condition::add($arItem)->getId();
			$arResult[] = $arItem;
		}
		$this->arConditions = $arResult;
	}
	
	public function setExercises($arTestsWithExercises){
		$arResult = [];
		foreach($arTestsWithExercises as $TEST_CODE => $arItems){
			foreach($arItems as $number => $arExercise){
				$number++;
				$arExercise['NUMBER'] = $number;
				$arExercise['NAME'] = 'Exercise '.$number;
				$arExercise['TEST_ID'] = $this->arTests[$TEST_CODE]['ID'];
				
				$arTasks = $arExercise['TASKS'];
				unset($arExercise['TASKS']);
				$arExercise['ID'] = Exercise::add($arExercise)->getId();
				
				foreach($arTasks as $arTask){
					$arTask['EXERCISE_ID'] = $arExercise['ID'];
					Task::add($arTask);
				}
				
				
				$arResult[] = $arExercise;
			}
		}
	}
}
